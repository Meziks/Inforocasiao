-- Produtos de demonstração: recondicionados populares (Grau A/B/C), ESGOTADOS.
-- Servem apenas para o site não nascer vazio. O gerente edita/apaga na gestão.
-- (stock = 0 → aparecem como "Esgotado". condition = 'Recondicionado'.)
INSERT INTO products
    (name, brand, category_id, price, stock, `condition`, description, image, is_active, is_featured, created_at, updated_at)
VALUES
    ('iPhone 11 64GB — Grau A',  'Apple',   (SELECT id FROM categories WHERE slug='telemoveis'),  189.00, 0, 'Recondicionado', 'Recondicionado Grau A — como novo, sem marcas de uso visíveis. Totalmente funcional, com garantia.', NULL, 1, 1, NOW(), NOW()),
    ('iPhone 12 128GB — Grau A', 'Apple',   (SELECT id FROM categories WHERE slug='telemoveis'),  279.00, 0, 'Recondicionado', 'Recondicionado Grau A — como novo, sem marcas de uso visíveis. Totalmente funcional, com garantia.', NULL, 1, 1, NOW(), NOW()),
    ('iPhone 13 128GB — Grau B', 'Apple',   (SELECT id FROM categories WHERE slug='telemoveis'),  349.00, 0, 'Recondicionado', 'Recondicionado Grau B — ligeiros sinais de uso, pouco percetíveis. Totalmente funcional, com garantia.', NULL, 1, 1, NOW(), NOW()),
    ('iPhone 14 128GB — Grau A', 'Apple',   (SELECT id FROM categories WHERE slug='telemoveis'),  489.00, 0, 'Recondicionado', 'Recondicionado Grau A — como novo, sem marcas de uso visíveis. Totalmente funcional, com garantia.', NULL, 1, 1, NOW(), NOW()),
    ('Samsung Galaxy S22 128GB — Grau B', 'Samsung', (SELECT id FROM categories WHERE slug='telemoveis'), 259.00, 0, 'Recondicionado', 'Recondicionado Grau B — ligeiros sinais de uso. Totalmente funcional, com garantia.', NULL, 1, 0, NOW(), NOW()),
    ('MacBook Air 13" M1 256GB — Grau A', 'Apple', (SELECT id FROM categories WHERE slug='portateis'), 679.00, 0, 'Recondicionado', 'Recondicionado Grau A — como novo. Chip Apple M1, 8GB RAM, 256GB SSD. Com garantia.', NULL, 1, 1, NOW(), NOW()),
    ('MacBook Pro 13" M1 256GB — Grau B', 'Apple', (SELECT id FROM categories WHERE slug='portateis'), 749.00, 0, 'Recondicionado', 'Recondicionado Grau B — ligeiros sinais de uso. Chip Apple M1, 8GB RAM, 256GB SSD. Com garantia.', NULL, 1, 1, NOW(), NOW()),
    ('Dell Latitude 7420 i7 16GB — Grau B', 'Dell', (SELECT id FROM categories WHERE slug='portateis'), 429.00, 0, 'Recondicionado', 'Recondicionado Grau B — Intel Core i7, 16GB RAM, 512GB SSD. Ideal para trabalho. Com garantia.', NULL, 1, 0, NOW(), NOW()),
    ('Lenovo ThinkPad T14 i5 16GB — Grau C', 'Lenovo', (SELECT id FROM categories WHERE slug='portateis'), 319.00, 0, 'Recondicionado', 'Recondicionado Grau C — sinais de uso visíveis, totalmente funcional. Intel Core i5, 16GB RAM. Com garantia.', NULL, 1, 0, NOW(), NOW()),
    ('iMac 24" M1 256GB — Grau A', 'Apple', (SELECT id FROM categories WHERE slug='computadores'), 899.00, 0, 'Recondicionado', 'Recondicionado Grau A — como novo. Chip Apple M1, ecrã 24" 4.5K. Com garantia.', NULL, 1, 0, NOW(), NOW()),
    ('Mac mini M1 256GB — Grau A', 'Apple', (SELECT id FROM categories WHERE slug='computadores'), 499.00, 0, 'Recondicionado', 'Recondicionado Grau A — como novo. Chip Apple M1, 8GB RAM, 256GB SSD. Com garantia.', NULL, 1, 0, NOW(), NOW()),
    ('iPad Air 4ª ger 64GB — Grau B', 'Apple', (SELECT id FROM categories WHERE slug='acessorios'), 329.00, 0, 'Recondicionado', 'Recondicionado Grau B — ligeiros sinais de uso. Ecrã 10.9", chip A14. Com garantia.', NULL, 1, 0, NOW(), NOW());
