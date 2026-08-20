-- Fase 2: portes de envio. "total" passa a ser sempre subtotal + shipping_cost.
ALTER TABLE orders
    ADD COLUMN subtotal DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER fulfillment,
    ADD COLUMN shipping_cost DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal;

-- Encomendas já existentes (Fase 1, sem portes): subtotal = total, portes = 0.
UPDATE orders SET subtotal = total, shipping_cost = 0 WHERE subtotal = 0;
