-- Transferir a 2ª imagem (coluna image2) já existente para product_images.
INSERT INTO product_images (product_id, image, sort_order)
SELECT id, image2, 1 FROM products WHERE image2 IS NOT NULL AND image2 <> '';
