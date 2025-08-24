CREATE TABLE IF NOT EXISTS cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id CHAR(32) NOT NULL,
    name VARCHAR(255) NOT NULL,
    category ENUM('food','beverage') NOT NULL,
    price_cents INT NOT NULL,
    is_iced TINYINT(1) DEFAULT 0,
    qty INT NOT NULL DEFAULT 1,
    discount_percent DECIMAL(5,4) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    INDEX (cart_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
