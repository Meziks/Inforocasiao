<?php
/**
 * Arranque da aplicação: carrega config, sessão segura e classes/funções base.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// --- Carregar configuração ---------------------------------------------------
$configFile = BASE_PATH . '/config/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    exit('Configuração em falta: copie config/config.example.php para config/config.php e preencha os dados.');
}
$config = require $configFile;
$GLOBALS['config'] = $config;

// --- Erros conforme ambiente -------------------------------------------------
if (($config['app']['env'] ?? 'production') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}

// --- Sessão segura -----------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => $isHttps,
    ]);
    session_name('INFORO_SESS');
    session_start();
}

// --- Carregar núcleo ---------------------------------------------------------
require BASE_PATH . '/app/helpers.php';
require BASE_PATH . '/app/Database.php';
require BASE_PATH . '/app/Auth.php';
require BASE_PATH . '/app/CustomerAuth.php';
require BASE_PATH . '/app/Mailer.php';
require BASE_PATH . '/app/Cart.php';
require BASE_PATH . '/app/Reviews.php';
require BASE_PATH . '/app/Seo.php';
