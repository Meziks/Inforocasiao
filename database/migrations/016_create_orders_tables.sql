-- Encomendas (Fase 1: sem pagamento online, só "pagamento na loja/entrega").
CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_id      INT UNSIGNED NOT NULL,
    status           ENUM('pendente','confirmada','pronta','enviada','concluida','cancelada')
                         NOT NULL DEFAULT 'pendente',
    fulfillment      ENUM('levantamento','envio') NOT NULL,
    shipping_name    VARCHAR(150) DEFAULT NULL,
    shipping_address VARCHAR(255) DEFAULT NULL,
    shipping_postal  VARCHAR(20)  DEFAULT NULL,
    shipping_city    VARCHAR(100) DEFAULT NULL,
    phone            VARCHAR(30)  DEFAULT NULL,
    notes            TEXT DEFAULT NULL,
    total            DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_orders_customer (customer_id),
    KEY idx_orders_status (status),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id)
        REFERENCES customers (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Linhas da encomenda. Nome e preço ficam gravados no momento da compra
-- ("snapshot"), para o histórico não mudar se o artigo for editado depois.
CREATE TABLE IF NOT EXISTS order_items (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id     INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NOT NULL,
    product_name VARCHAR(160) NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    qty          SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_order_items_order (order_id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id)
        REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id)
        REFERENCES products (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
