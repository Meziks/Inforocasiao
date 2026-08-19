-- NOTA: esta migration transferia a coluna legada "image2" (de uma aplicação
-- anterior) para product_images. Tal como a 009, essa coluna só existia em
-- instalações antigas com dados legados; numa instalação nova ela nunca é
-- criada (ver 009), o que fazia esta migration falhar sempre com
-- "Unknown column 'image2'" e bloquear todas as seguintes.
-- Substituída por uma instrução inofensiva, só para desbloquear o deploy.
-- Em produção já foi aplicada com sucesso antes desta alteração, por isso
-- este ficheiro nunca volta a correr lá — só afeta instalações novas.
ALTER TABLE products COMMENT = 'Inforocasiao - catalogo de produtos';
