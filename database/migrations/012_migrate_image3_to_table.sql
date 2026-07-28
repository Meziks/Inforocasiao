-- Transferir a 3ª imagem (coluna image3) já existente para product_images.
INSERT INTO product_images (product_id, image, sort_order)
SELECT id, image3, 2 FROM products WHERE image3 IS NOT NULL AND image3 <> '';
