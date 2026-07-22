<?php
/**
 * Executor de migrations.
 *
 * Aplica, por ordem, todos os ficheiros .sql em database/migrations que ainda
 * não tenham sido executados. Guarda o registo na tabela "schema_migrations",
 * por isso correr várias vezes é seguro — só aplica o que falta.
 *
 * Uso (por SSH, na raiz do projeto):
 *     php database/migrate.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado por linha de comandos (SSH).');
}

define('BASE_PATH', dirname(__DIR__));

$configFile = BASE_PATH . '/config/config.php';
if (!file_exists($configFile)) {
    fwrite(STDERR, "ERRO: config/config.php não existe. Copie config/config.example.php e preencha os dados.\n");
    exit(1);
}
$config = require $configFile;
$GLOBALS['config'] = $config;
require BASE_PATH . '/app/Database.php';

try {
    $pdo = Database::pdo();
} catch (Throwable $e) {
    fwrite(STDERR, "ERRO de ligação à base de dados: " . $e->getMessage() . "\n");
    exit(1);
}

// Tabela de controlo das migrations
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS schema_migrations (
        migration  VARCHAR(255) NOT NULL,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$applied = $pdo->query("SELECT migration FROM schema_migrations")
    ->fetchAll(PDO::FETCH_COLUMN);

$files = glob(BASE_PATH . '/database/migrations/*.sql') ?: [];
sort($files);

$ran = 0;
foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        continue;
    }
    echo "→ A aplicar: $name ... ";
    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        echo "vazio, ignorado.\n";
        continue;
    }
    try {
        $pdo->beginTransaction();
        $pdo->exec($sql);
        $stmt = $pdo->prepare("INSERT INTO schema_migrations (migration) VALUES (?)");
        $stmt->execute([$name]);
        $pdo->commit();
        echo "OK\n";
        $ran++;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fwrite(STDERR, "FALHOU: " . $e->getMessage() . "\n");
        exit(1);
    }
}

echo $ran === 0
    ? "Nada a fazer — base de dados já está atualizada.\n"
    : "Concluído: $ran migration(s) aplicada(s).\n";
