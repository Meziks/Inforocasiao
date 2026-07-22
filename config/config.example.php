<?php
/**
 * Configuração da aplicação.
 *
 * COPIAR este ficheiro para "config.php" NO SERVIDOR (via SSH ou Gestor de
 * Ficheiros do cPanel) e preencher com os dados reais. O "config.php" NÃO é
 * versionado no Git (ver .gitignore) para as credenciais nunca irem parar ao
 * GitHub.
 */

return [
    'db' => [
        'host'    => 'localhost',        // No cPanel é quase sempre "localhost"
        'name'    => 'CHANGE_ME_dbname', // Nome da BD criada no cPanel (ex: user_inforocasiao)
        'user'    => 'CHANGE_ME_dbuser', // Utilizador MySQL criado no cPanel
        'pass'    => 'CHANGE_ME_dbpass', // Password desse utilizador
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name'     => 'Inforocasião',
        'base_url' => '',            // Deixar vazio se o site está na raiz do domínio
        'env'      => 'production',  // "production" ou "development"
    ],
];
