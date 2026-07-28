-- NOTA: esta migration tentava adicionar image2/image3/image4 a "products".
-- Essas colunas já existiam fisicamente na base de dados (de uma aplicação
-- anterior cujo registo de sucesso não ficou gravado), o que fazia o
-- instalador repetir esta migration a cada deploy e falhar sempre com
-- "Duplicate column name" — impedindo as migrations seguintes de correr
-- (as que criam a tabela product_images e transferem os dados para lá).
-- Substituída por uma instrução inofensiva, só para desbloquear o deploy.
-- As imagens extra passam a viver em product_images (migrations 010+).
ALTER TABLE products COMMENT = 'Inforocasiao - catalogo de produtos';
