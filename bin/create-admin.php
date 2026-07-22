<?php
/**
 * Cria (ou atualiza a password de) um utilizador de gestão.
 *
 * Uso (por SSH, na raiz do projeto):
 *     php bin/create-admin.php <utilizador> <password>
 *
 * Exemplo:
 *     php bin/create-admin.php gerente "UmaPasswordForte123!"
 *
 * Se o utilizador já existir, a password é atualizada.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado por linha de comandos (SSH).');
}

define('BASE_PATH', dirname(__DIR__));

$configFile = BASE_PATH . '/config/config.php';
if (!file_exists($configFile)) {
    fwrite(STDERR, "ERRO: config/config.php não existe.\n");
    exit(1);
}
$config = require $configFile;
$GLOBALS['config'] = $config;
require BASE_PATH . '/app/Database.php';

$username = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$username || !$password) {
    fwrite(STDERR, "Uso: php bin/create-admin.php <utilizador> <password>\n");
    exit(1);
}
if (strlen($password) < 8) {
    fwrite(STDERR, "ERRO: a password deve ter pelo menos 8 caracteres.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $exists = Database::one("SELECT id FROM users WHERE username = ?", [$username]);
    if ($exists) {
        Database::run("UPDATE users SET password_hash = ? WHERE username = ?", [$hash, $username]);
        echo "Password atualizada para o utilizador '$username'.\n";
    } else {
        Database::run(
            "INSERT INTO users (username, password_hash, name) VALUES (?, ?, ?)",
            [$username, $hash, $username]
        );
        echo "Utilizador '$username' criado com sucesso.\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "ERRO: " . $e->getMessage() . "\n");
    fwrite(STDERR, "(Já correu as migrations? Execute: php database/migrate.php)\n");
    exit(1);
}
