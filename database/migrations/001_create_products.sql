DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id CHAR(32) NOT NULL,                       -- 32 hex chars (UUID sem hífens)
  name VARCHAR(255) NOT NULL,
  category ENUM('food','beverage') NOT NULL,
  base_price_cents INT NOT NULL,
  is_iced TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
