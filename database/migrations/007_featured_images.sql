-- Autossuficiente: garante que os 4 escolhidos ficam em destaque E aplica-lhes
-- as imagens (licença livre, Unsplash). Os restantes de demonstração ficam sem
-- destaque. Uma só instrução, para o instalador aplicar sem problemas.
UPDATE products
SET
    is_featured = (name IN (
        'iPhone 14 128GB — Grau A',
        'MacBook Air 13" M1 256GB — Grau A',
        'iMac 24" M1 256GB — Grau A',
        'iPad Air 4ª ger 64GB — Grau B'
    )),
    image = CASE name
        WHEN 'iPhone 14 128GB — Grau A'         THEN 'https://images.unsplash.com/photo-1726587912121-ea21fcc57ff8?w=900&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8aXBob25lJTIwMTR8ZW58MHx8MHx8fDA%3D'
        WHEN 'MacBook Air 13" M1 256GB — Grau A' THEN 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=900&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8bWFjYm9vayUyMGFpcnxlbnwwfHwwfHx8MA%3D%3D'
        WHEN 'iMac 24" M1 256GB — Grau A'        THEN 'https://images.unsplash.com/photo-1527443195645-1133f7f28990?w=900&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8aW1hY3xlbnwwfHwwfHx8MA%3D%3D'
        WHEN 'iPad Air 4ª ger 64GB — Grau B'     THEN 'https://images.unsplash.com/photo-1630331528526-7d04c6eb463f?w=900&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8aXBhZCUyMGFpcnxlbnwwfHwwfHx8MA%3D%3D'
        ELSE image
    END
WHERE name LIKE '%— Grau %';
