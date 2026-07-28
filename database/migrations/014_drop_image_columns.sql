-- As imagens extra já foram transferidas para product_images (migrations
-- 011-013). Estas colunas deixam de ser necessárias.
ALTER TABLE products
DROP COLUMN image2,
DROP COLUMN image3,
DROP COLUMN image4;
