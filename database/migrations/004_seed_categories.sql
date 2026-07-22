-- Categorias iniciais da loja
INSERT INTO categories (name, slug) VALUES
    ('Computadores', 'computadores'),
    ('Portáteis',    'portateis'),
    ('Telemóveis',   'telemoveis'),
    ('Componentes',  'componentes'),
    ('Acessórios',   'acessorios')
ON DUPLICATE KEY UPDATE name = VALUES(name);
