-- Deixar apenas 4 produtos em destaque na página inicial.
-- Mexe só nos produtos de demonstração (nome com "— Grau"): fica 1 para os
-- quatro escolhidos e 0 para os restantes de demonstração. Uma só instrução.
UPDATE products
SET is_featured = (name IN (
    'iPhone 14 128GB — Grau A',
    'MacBook Air 13" M1 256GB — Grau A',
    'iMac 24" M1 256GB — Grau A',
    'iPad Air 4ª ger 64GB — Grau B'
))
WHERE name LIKE '%— Grau %';
