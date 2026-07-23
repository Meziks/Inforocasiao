-- Trocar o traço longo por "·" nos nomes dos produtos de demonstração.
-- Ex.: "iPhone 14 128GB — Grau A" -> "iPhone 14 128GB · Grau A".
-- Corre depois das 006/007 (que usam os nomes antigos), por isso é seguro.
UPDATE products
SET name = REPLACE(name, ' — Grau ', ' · Grau ')
WHERE name LIKE '%— Grau %';
