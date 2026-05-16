<?php
require_once __DIR__ . '/../config/db.php';
try {
    $orderId = 1; // test with a dummy order ID
    $transactionId = 'TXN-TEST';
    $provider = 'mobile_money';
    $amount = 1000;
    
    // Test the exact insert query from process.php
    $stmt = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, provider, amount, status) VALUES (?, ?, ?, ?, 'succeeded')");
    $stmt->execute([$orderId, $transactionId, $provider, $amount]);
    echo "Insert successful\n";
    
    $updateStmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
    $updateStmt->execute([$orderId]);
    echo "Update successful\n";
    
} catch (PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
?>
