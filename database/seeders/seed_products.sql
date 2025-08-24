INSERT INTO products (id, name, category, base_price_cents, is_iced)
VALUES
    (REPLACE(UUID(),'-',''), 'Pão de queijo', 'food', 600, 0),
    (REPLACE(UUID(),'-',''), 'Café expresso', 'beverage', 800, 0),
    (REPLACE(UUID(),'-',''), 'Latte gelado', 'beverage', 1200, 1);
