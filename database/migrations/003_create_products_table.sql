-- Produtos / artigos da loja
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(160)  NOT NULL,
    brand       VARCHAR(80)   NULL,
    category_id INT UNSIGNED  NULL,
    price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock       INT           NOT NULL DEFAULT 0,
    `condition` ENUM('Novo','Usado','Recondicionado') NOT NULL DEFAULT 'Novo',
    description TEXT          NULL,
    image       VARCHAR(255)  NULL,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    is_featured TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_products_category (category_id),
    KEY idx_products_active (is_active),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
