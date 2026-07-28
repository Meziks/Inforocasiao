-- Transferir a 4ª imagem (coluna image4) já existente para product_images.
INSERT INTO product_images (product_id, image, sort_order)
SELECT id, image4, 3 FROM products WHERE image4 IS NOT NULL AND image4 <> '';
