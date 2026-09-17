<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
}

// Récupérer les données de la requête
$data = getJsonData();

// Déterminer si c'est une commande invité ou connecté
$isGuestOrder = !empty($data['guest_checkout']) && !empty($data['phone']);

if ($isGuestOrder) {
    // Mode invité: pas de token requis, identification par téléphone
    $guestName = $data['name'] ?? '';
    $guestPhone = $data['phone'] ?? '';
    $guestAddress = $data['address'] ?? '';

    if (empty($guestName) || empty($guestPhone) || empty($guestAddress)) {
        sendJsonResponse(['error' => 'Nom, téléphone et adresse sont obligatoires pour le checkout invité'], 400);
    }

    // Valider le format du téléphone
    if (!preg_match('/^\+?[0-9]{8,18}$/', $guestPhone)) {
        sendJsonResponse(['error' => 'Format de téléphone invalide'], 400);
    }
} else {
    // Mode connecté: authentification requise
    $user = authenticate();
}

// Valider les données d'entrée selon le mode
if ($isGuestOrder) {
    if (empty($data['items']) || !is_array($data['items'])) {
        sendJsonResponse(['error' => 'Les articles sont obligatoires pour le checkout invité'], 400);
    }
    $shippingAddress = $guestAddress;
} else {
    $requiredFields = ['shipping_address'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(['error' => 'Adresse de livraison obligatoire'], 400);
        }
    }
    $shippingAddress = $data['shipping_address'];
}

// Valider la méthode de paiement
$allowedPaymentMethods = ['cash_on_delivery', 'transfer', 'mobile_money_bj', 'celtis_cash_bj', 'uba_bank', 'credit_card', 'paypal', 'mobile_money'];
$paymentMethod = $data['payment_method'] ?? 'cash_on_delivery';

try {
    $pdo->beginTransaction();
    $isPgsql = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql');

    // 1. Récupérer ou créer l'utilisateur (mode invité) ou utiliser l'utilisateur connecté
    if ($isGuestOrder) {
        $stmt = $pdo->prepare('SELECT id, first_name, last_name, address FROM users WHERE phone = ?');
        $stmt->execute([$guestPhone]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $userId = (int)$existingUser['id'];
            $stmt = $pdo->prepare('UPDATE users SET address = ? WHERE id = ?');
            $stmt->execute([$guestAddress, $userId]);
        } else {
            $guestEmail = 'guest_' . preg_replace('/[^0-9]/', '', $guestPhone) . '@bloomchloe.local';
            if ($isPgsql) {
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, phone, first_name, last_name, address, role, role_id, created_at)
                    VALUES (?, '', ?, ?, '', ?, 'customer', (SELECT id FROM roles WHERE name = 'customer' LIMIT 1), NOW())
                    RETURNING id
                ");
                $stmt->execute([$guestEmail, $guestPhone, $guestName, $guestAddress]);
                $userId = (int)$stmt->fetchColumn();
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, phone, first_name, last_name, address, role, role_id, created_at)
                    VALUES (?, '', ?, ?, '', ?, 'customer', (SELECT id FROM roles WHERE name = 'customer' LIMIT 1), NOW())
                ");
                $stmt->execute([$guestEmail, $guestPhone, $guestName, $guestAddress]);
                $userId = (int)$pdo->lastInsertId();
            }
        }
    } else {
        $userId = (int)$user['id'];
    }

    // 2. Récupérer les items (fournis dans la requête ou depuis la table cart)
    $itemsToProcess = [];
    if (!empty($data['items']) && is_array($data['items'])) {
        $itemsToProcess = $data['items'];
    } else if (!$isGuestOrder) {
        $stmt = $pdo->prepare('SELECT product_id, quantity FROM cart WHERE user_id = ?');
        $stmt->execute([$userId]);
        $itemsToProcess = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (empty($itemsToProcess)) {
        $pdo->rollBack();
        sendJsonResponse(['error' => 'Votre panier est vide. Veuillez ajouter des produits avant de commander.'], 400);
    }

    $orderItems = [];
    foreach ($itemsToProcess as $item) {
        $productId = (int)($item['product_id'] ?? $item['id'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 1);

        if ($productId <= 0 || $quantity <= 0) {
            continue;
        }

        // Récupérer les détails du produit
        $stmt = $pdo->prepare("
            SELECT id, name, price, COALESCE(stock_quantity, stock, 100) as available_quantity
            FROM products
            WHERE id = ?
            FOR UPDATE
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $pdo->rollBack();
            sendJsonResponse(['error' => 'Produit #' . $productId . ' non trouvé ou non disponible'], 400);
        }

        if ((int)$product['available_quantity'] < $quantity) {
            $pdo->rollBack();
            sendJsonResponse([
                'error' => "Stock insuffisant pour '{$product['name']}'. Quantité disponible : {$product['available_quantity']}"
            ], 400);
        }

        $price = (float)($item['price'] ?? $product['price']);
        $orderItems[] = [
            'product_id' => (int)$product['id'],
            'product_name' => $product['name'],
            'price' => $price,
            'quantity' => $quantity,
            'total' => $price * $quantity
        ];
    }

    if (empty($orderItems)) {
        $pdo->rollBack();
        sendJsonResponse(['error' => 'Aucun article valide dans la commande'], 400);
    }

    // 3. Calculer le total
    $subtotal = 0;
    foreach ($orderItems as $item) {
        $subtotal += $item['total'];
    }

    $shippingFee = calculateShippingFee($subtotal, $shippingAddress);
    $totalAmount = $subtotal + $shippingFee;
    $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -8));

    // 4. Créer la commande
    if ($isPgsql) {
        $stmt = $pdo->prepare('INSERT INTO orders (
            user_id, total_amount, status, shipping_address, shipping_fee, tax_amount, notes, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW()) RETURNING id');
        $stmt->execute([
            $userId,
            $totalAmount,
            'pending',
            $shippingAddress,
            $shippingFee,
            0,
            $data['customer_note'] ?? null
        ]);
        $orderId = (int)$stmt->fetchColumn();
    } else {
        $stmt = $pdo->prepare('INSERT INTO orders (
            user_id, total_amount, status, shipping_address, shipping_fee, tax_amount, notes, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            $userId,
            $totalAmount,
            'pending',
            $shippingAddress,
            $shippingFee,
            0,
            $data['customer_note'] ?? null
        ]);
        $orderId = (int)$pdo->lastInsertId();
    }

    // 5. Ajouter les articles de la commande et déduire le stock
    foreach ($orderItems as $item) {
        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_purchase) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['product_name'],
            $item['quantity'],
            $item['price']
        ]);
        
        // Mettre à jour le stock
        $updateStockStmt = $pdo->prepare('
            UPDATE products 
            SET stock_quantity = GREATEST(0, COALESCE(stock_quantity, 0) - ?),
                stock = GREATEST(0, COALESCE(stock, 0) - ?),
                updated_at = NOW()
            WHERE id = ?
        ');
        $updateStockStmt->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
    }
    
    // 6. Vider le panier (seulement en mode connecté)
    if (!$isGuestOrder) {
        try {
            $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
            $stmt->execute([$userId]);
        } catch (Exception $e) {}
    }
    
    // 7. Créer un enregistrement de paiement
    $paymentStatus = 'pending';
    if ($paymentMethod === 'cash_on_delivery') {
        $paymentStatus = 'pending_delivery';
    } elseif ($paymentMethod === 'transfer') {
        $paymentStatus = 'pending_verification';
    }

    try {
        $paymentStmt = $pdo->prepare('INSERT INTO payments (
            order_id, transaction_id, provider, amount, currency, status, metadata, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');

        $transactionId = ($paymentMethod === 'cash_on_delivery')
            ? 'COD-' . strtoupper(substr(uniqid(), -8))
            : (($paymentMethod === 'transfer') ? 'TRF-' . strtoupper(substr(uniqid(), -8)) : 'ORD-' . time() . '-' . mt_rand(1000, 9999));

        $paymentData = [
            'provider' => $paymentMethod,
            'status' => $paymentStatus,
            'created_at' => date('Y-m-d H:i:s'),
            'guest_checkout' => $isGuestOrder
        ];

        $paymentStmt->execute([
            $orderId,
            $transactionId,
            $paymentMethod,
            $totalAmount,
            'XOF',
            $paymentStatus,
            json_encode($paymentData)
        ]);
    } catch (Exception $e) {
        error_log('Notice payment log error: ' . $e->getMessage());
    }
    
    $pdo->commit();
    
    sendJsonResponse([
        'message' => 'Commande créée avec succès',
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'status' => 'pending',
        'amount' => $totalAmount
    ], 201);
    
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('BLOOM ERROR [Create Order]: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    sendJsonResponse(['error' => 'Erreur lors de la création de la commande : ' . $e->getMessage()], 500);
}

function calculateShippingFee(float $subtotal, string $shippingAddress): int {
    return 0;
}
