-- =============================================================================
-- BLOOM-CHLOE - SCHÉMA POSTGRESQL COMPLET POUR SUPABASE
-- =============================================================================
-- Exécutez ce script directement dans le SQL Editor de Supabase
-- =============================================================================

-- Extension pour UUID si nécessaire
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- =============================================================================
-- 1. TYPES ÉNUMÉRÉS
-- =============================================================================
DO $$ BEGIN
    CREATE TYPE user_role AS ENUM ('customer', 'admin');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE login_status AS ENUM ('success', 'failed', 'blocked');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE product_source AS ENUM ('produit', 'store');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE product_status AS ENUM ('published', 'draft', 'archived');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

DO $$ BEGIN
    CREATE TYPE order_status AS ENUM ('pending', 'processing', 'shipped', 'completed', 'cancelled');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- =============================================================================
-- 2. TABLE DES UTILISATEURS
-- =============================================================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    role user_role DEFAULT 'customer',
    token VARCHAR(255) DEFAULT NULL,
    token_expires_at TIMESTAMP DEFAULT NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP DEFAULT NULL,
    last_login_at TIMESTAMP DEFAULT NULL,
    last_login_ip VARCHAR(45) DEFAULT NULL,
    email_verified_at TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users (email);
CREATE INDEX IF NOT EXISTS idx_users_token ON users (token);

-- =============================================================================
-- 3. TABLE DES REFRESH TOKENS
-- =============================================================================
CREATE TABLE IF NOT EXISTS refresh_tokens (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    revoked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_refresh_tokens_token ON refresh_tokens (token);
CREATE INDEX IF NOT EXISTS idx_refresh_tokens_user_id ON refresh_tokens (user_id);

-- =============================================================================
-- 4. TABLE DES VÉRIFICATIONS EMAIL
-- =============================================================================
CREATE TABLE IF NOT EXISTS email_verifications (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_email_verif_token ON email_verifications (token);

-- =============================================================================
-- 5. TABLE DES LOGS DE CONNEXION
-- =============================================================================
CREATE TABLE IF NOT EXISTS login_logs (
    id SERIAL PRIMARY KEY,
    user_id INT DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
    email VARCHAR(255) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    status login_status NOT NULL,
    failure_reason VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_login_logs_ip ON login_logs (ip_address);
CREATE INDEX IF NOT EXISTS idx_login_logs_created ON login_logs (created_at);

-- =============================================================================
-- 6. TABLE DES CATÉGORIES
-- =============================================================================
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    image_url VARCHAR(255),
    icon VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories (slug);

-- =============================================================================
-- 7. TABLE DES PRODUITS
-- =============================================================================
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    category_id INT DEFAULT NULL REFERENCES categories(id) ON DELETE SET NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) DEFAULT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    compare_price DECIMAL(10, 2) DEFAULT NULL,
    stock INT DEFAULT 0,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(255),
    gallery_urls TEXT DEFAULT NULL,
    rating DECIMAL(2,1) DEFAULT 0.0,
    source product_source DEFAULT 'produit',
    status product_status DEFAULT 'published',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_products_status ON products (status);
CREATE INDEX IF NOT EXISTS idx_products_category ON products (category_id);
CREATE INDEX IF NOT EXISTS idx_products_source ON products (source);

-- =============================================================================
-- 8. TABLE DU PANIER
-- =============================================================================
CREATE TABLE IF NOT EXISTS cart (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_cart_item UNIQUE (user_id, product_id)
);

CREATE INDEX IF NOT EXISTS idx_cart_user ON cart (user_id);

-- =============================================================================
-- 9. TABLE DES FAVORIS (WISHLIST)
-- =============================================================================
CREATE TABLE IF NOT EXISTS favorites (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_favorite UNIQUE (user_id, product_id)
);

CREATE INDEX IF NOT EXISTS idx_favorites_user ON favorites (user_id);

-- =============================================================================
-- 10. TABLE DES COMMANDES
-- =============================================================================
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    user_id INT DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
    guest_info JSONB DEFAULT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status order_status DEFAULT 'pending',
    shipping_address TEXT,
    shipping_fee DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_orders_user ON orders (user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders (status);

-- =============================================================================
-- 11. TABLE DES LIGNES DE COMMANDE
-- =============================================================================
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT DEFAULT NULL REFERENCES products(id) ON DELETE SET NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_order_items_order ON order_items (order_id);

-- =============================================================================
-- 12. TABLE DES PAIEMENTS
-- =============================================================================
CREATE TABLE IF NOT EXISTS payments (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    transaction_id VARCHAR(255),
    provider VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'XOF',
    status VARCHAR(50) NOT NULL,
    metadata JSONB DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_payments_order ON payments (order_id);

-- =============================================================================
-- 13. TABLE DE RATE LIMITING
-- =============================================================================
CREATE TABLE IF NOT EXISTS rate_limits (
    id SERIAL PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(50) NOT NULL,
    attempts INT DEFAULT 1,
    last_attempt_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    blocked_until TIMESTAMP WITH TIME ZONE DEFAULT NULL
);

CREATE INDEX IF NOT EXISTS idx_rate_limits_ip_action ON rate_limits (ip_address, action);

-- =============================================================================
-- 14. INSERTION DES CATÉGORIES INITIALES
-- =============================================================================
INSERT INTO categories (name, slug, description, image_url, icon) VALUES
('Bien-être et relaxation', 'bien-etre-relaxation', 'Produits de bien-être et relaxation', NULL, 'solar:heart-pulse-bold'),
('Accessoire de coiffure', 'accessoire-coiffure', 'Accessoires pour cheveux et coiffure', NULL, 'solar:scissors-bold'),
('Soin personnel', 'soin-personnel', 'Produits de soin personnel', NULL, 'solar:hand-stars-bold'),
('Beauté et soin personnel', 'beaute-soin-personnel', 'Beauté et soin personnel', NULL, 'solar:palette-bold'),
('Accessoire tech', 'accessoire-tech', 'Accessoires technologiques', NULL, 'solar:smartphone-bold'),
('Santé féminine', 'sante-feminine', 'Produits de santé féminine', NULL, 'solar:heart-bold'),
('Accessoire de cuisine', 'accessoire-cuisine', 'Accessoires de cuisine', NULL, 'solar:chef-hat-bold'),
('Accessoire de douche', 'accessoire-douche', 'Accessoires de douche', NULL, 'solar:bath-bold'),
('Accessoire', 'accessoire', 'Accessoires divers', NULL, 'solar:bag-bold'),
('Accessoire de tournage', 'accessoire-tournage', 'Accessoires de tournage et photographie', NULL, 'solar:camera-bold'),
('Accessoire de beauté', 'accessoire-beaute', 'Accessoires de beauté', NULL, 'solar:magic-stick-bold'),
('Bien-être', 'bien-etre', 'Produits bien-être', NULL, 'solar:sun-bold'),
('Soin corporel', 'soin-corporel', 'Produits de soin corporel', NULL, 'solar:hand-heart-bold'),
('Esthétique et soin personnel', 'esthetique-soin', 'Esthétique et soin personnel', NULL, 'solar:star-bold'),
('Bien-être et plaisir personnel', 'bien-etre-plaisir', 'Bien-être et plaisir personnel', NULL, 'solar:heart-shine-bold'),
('Bien-être et soin de la peau', 'bien-etre-soin-peau', 'Bien-être et soin de la peau', NULL, 'solar:water-bold'),
('Soin personnel et beauté', 'soin-personnel-beaute', 'Soin personnel et beauté', NULL, 'solar:palette-round-bold'),
('Jardinage et lavage', 'jardinage-lavage', 'Jardinage et lavage', NULL, 'solar:leaf-bold'),
('Sport et bien-être', 'sport-bien-etre', 'Sport et bien-être', NULL, 'solar:running-bold'),
('Accessoire de bureau', 'accessoire-bureau', 'Accessoires de bureau', NULL, 'solar:monitor-bold'),
('Ménage', 'menage', 'Articles de ménage', NULL, 'solar:broom-bold'),
('Accessoire de sortie', 'accessoire-sortie', 'Accessoires de sortie', NULL, 'solar:bag-check-bold'),
('Accessoire de chambre', 'accessoire-chambre', 'Accessoires de chambre', NULL, 'solar:bed-bold'),
('Bien-être et santé', 'bien-etre-sante', 'Bien-être et santé', NULL, 'solar:shield-bold'),
('Accessoire personnel', 'accessoire-personnel', 'Accessoires personnels', NULL, 'solar:user-bold'),
('Mobilier de maison', 'mobilier-maison', 'Mobilier de maison', NULL, 'solar:sofa-bold'),
('Art de la table', 'art-table', 'Art de la table', NULL, 'solar:cup-bold'),
('Accessoire anti-insecte', 'accessoire-anti-insecte', 'Accessoires anti-insecte', NULL, 'solar:bug-bold'),
('Bricolage', 'bricolage', 'Outils de bricolage', NULL, 'solar:wrench-bold'),
('Accessoire High-Tech', 'accessoire-high-tech', 'Accessoires High-Tech', NULL, 'solar:laptop-bold'),
('Collection Bloom', 'collection-bloom', 'Collection exclusive Bloom', NULL, 'solar:crown-bold'),
('Range vaisselle', 'range-vaisselle', 'Rangement pour vaisselle', NULL, 'solar:sort-bold')
ON CONFLICT (slug) DO UPDATE SET name = EXCLUDED.name;

-- =============================================================================
-- 15. COMPTE ADMIN PAR DÉFAUT (Mot de passe: Admin123!)
-- =============================================================================
INSERT INTO users (email, password, first_name, last_name, role) VALUES
('admin@bloom-chloe.com', '$2y$12$LJ3m4ys.NUOvGQZ5UYueNe/FgKR5F0VHuVkYW3N.JQG/5.hxljMaO', 'Admin', 'Bloom', 'admin')
ON CONFLICT (email) DO NOTHING;
