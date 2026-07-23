-- Deixar apenas 4 produtos em destaque na página inicial.
-- Mexe só nos produtos de demonstração (nome com "— Grau"), para não afetar
-- os artigos que o gerente venha a marcar como destaque.
UPDATE products SET is_featured = 0 WHERE name LIKE '%— Grau %';

UPDATE products SET is_featured = 1
WHERE name IN (
    'iPhone 14 128GB — Grau A',
    'MacBook Air 13" M1 256GB — Grau A',
    'iMac 24" M1 256GB — Grau A',
    'iPad Air 4ª ger 64GB — Grau B'
);
