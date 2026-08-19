-- NOTA: mesma situação da 011, mas para a coluna legada "image3" — só
-- existia em instalações antigas com dados legados e nunca é criada numa
-- instalação nova (ver 009). Substituída por uma instrução inofensiva.
-- Em produção já foi aplicada com sucesso antes desta alteração, por isso
-- este ficheiro nunca volta a correr lá — só afeta instalações novas.
ALTER TABLE products COMMENT = 'Inforocasiao - catalogo de produtos';
