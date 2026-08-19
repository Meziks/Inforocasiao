-- NOTA: esta migration apagava as colunas legadas "image2/image3/image4"
-- depois de migradas (011-013). Numa instalação nova essas colunas nunca
-- chegam a existir (ver 009), o que fazia "DROP COLUMN" falhar sempre com
-- "Can't DROP COLUMN image2; check that it exists" e bloquear o deploy.
-- Substituída por uma instrução inofensiva. Em produção já foi aplicada
-- com sucesso antes desta alteração, por isso este ficheiro nunca volta a
-- correr lá — só afeta instalações novas.
ALTER TABLE products COMMENT = 'Inforocasiao - catalogo de produtos';
