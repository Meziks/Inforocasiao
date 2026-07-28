-- Imagens extra dos produtos (além da imagem principal em products.image).
-- sort_order 1..3 correspondem às imagens 2ª, 3ª e 4ª do artigo.
CREATE TABLE IF NOT EXISTS product_images (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED NOT NULL,
    image      VARCHAR(255) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_product_images_slot (product_id, sort_order),
    CONSTRAINT fk_product_images_product FOREIGN KEY (product_id)
        REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
