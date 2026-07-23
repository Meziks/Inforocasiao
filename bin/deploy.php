<?php
/**
 * ============================================================================
 * AUTO-INSTALADOR / DEPLOY da Inforocasião — para alojamentos SEM Git nem SSH
 * ============================================================================
 *
 * O que faz (sozinho, agendado pelo Cron Jobs do cPanel):
 *   1. Vai buscar o código mais recente do GitHub (descarrega um .zip).
 *   2. Publica-o em public_html (sem apagar imagens carregadas).
 *   3. Cria/atualiza o config.php com as credenciais da base de dados.
 *   4. Aplica as migrations (cria/atualiza as tabelas).
 *   5. Cria o primeiro utilizador de gestão, se ainda não existir.
 * Só faz trabalho quando há código novo no GitHub (compara o commit).
 *
 * COMO USAR (uma vez):
 *   1. cPanel → File Manager → ir à HOME (/home/inforoca) — NÃO ao public_html.
 *   2. Criar aqui um ficheiro chamado  deploy.php  e colar este conteúdo.
 *   3. Preencher a secção "CONFIGURAÇÃO" abaixo (base de dados + login gestor).
 *   4. cPanel → Cron Jobs → adicionar um agendamento de 5 em 5 minutos
 *      (ver instrucoes de cron detalhadas no chat / README) com o comando:
 *        /usr/local/bin/php /home/inforoca/deploy.php >> /home/inforoca/deploy.log 2>&1
 *
 * IMPORTANTE: este ficheiro contém palavras-passe. Tem de ficar na HOME
 * (/home/inforoca), NUNCA dentro de public_html. Por segurança, ele recusa
 * ser aberto pelo navegador.
 * ============================================================================
 */

// ------------------------------------------------------------------ SEGURANÇA
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}

// ============================================================================
//  CONFIGURAÇÃO  — preencher os valores marcados com  <<< ALTERAR
// ============================================================================
$CFG = [
    // -- GitHub ----------------------------------------------------------------
    'repo'   => 'meziks/inforocasiao',
    'branch' => 'claude/cpanel-github-deployment-3sp8i1',
    // Token só é preciso se o repositório for PRIVADO. Se o tornar público,
    // deixe vazio. (PAT "fine-grained" com permissão de leitura de conteúdos.)
    'token'  => '',                                  // <<< ALTERAR se privado

    // -- Onde publicar o site --------------------------------------------------
    'public_dir' => '/home/inforoca/public_html',    // pasta do domínio principal

    // -- Base de dados (criada no cPanel → MySQL Databases) --------------------
    'db_host' => 'localhost',
    'db_name' => 'inforoca_XXXX',                    // <<< ALTERAR
    'db_user' => 'inforoca_XXXX',                    // <<< ALTERAR
    'db_pass' => 'XXXXXXXX',                          // <<< ALTERAR

    // -- Primeiro login de gestão (criado só na 1ª vez) -----------------------
    'admin_user' => 'gerente',                        // <<< ALTERAR se quiser
    'admin_pass' => 'ALTERE_ESTA_PASSWORD',           // <<< ALTERAR (mín. 8)
];
// ============================================================================
//  A partir daqui não é preciso mexer.
// ============================================================================

function logline(string $msg): void
{
    echo '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n";
}

/** Pedido HTTP GET ao GitHub (segue redirects, com token opcional). */
function ghGet(string $url, string $token, bool $binary = false)
{
    $ch = curl_init($url);
    $headers = ['User-Agent: inforocasiao-deployer', 'Accept: application/vnd.github+json'];
    if ($token !== '') {
        $headers[] = 'Authorization: token ' . $token;
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 120,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($body === false) {
        throw new RuntimeException("Falha de rede ao contactar o GitHub: $err");
    }
    if ($code < 200 || $code >= 300) {
        $hint = $code === 404 ? ' (repo privado sem token? ramo errado?)' : '';
        throw new RuntimeException("GitHub respondeu HTTP $code$hint");
    }
    return $body;
}

/** Copia recursivamente $src para $dst, sem apagar o que já existe em $dst. */
function copyTree(string $src, string $dst): void
{
    $dir = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($dir as $item) {
        $target = $dst . DIRECTORY_SEPARATOR . $dir->getSubPathName();
        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            copy($item->getPathname(), $target);
        }
    }
}

/** Apaga recursivamente uma pasta. */
function rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($dir);
}

// ---------------------------------------------------------------------- MAIN
try {
    logline('--- Verificar atualizações ---');

    // 1) Último commit do ramo
    $meta = json_decode(
        ghGet("https://api.github.com/repos/{$CFG['repo']}/commits/{$CFG['branch']}", $CFG['token']),
        true
    );
    $sha = $meta['sha'] ?? null;
    if (!$sha) {
        throw new RuntimeException('Não foi possível ler o último commit.');
    }

    $stateFile = __DIR__ . '/.deploy_state';
    $current   = is_file($stateFile) ? trim((string) file_get_contents($stateFile)) : '';
    $configFile = $CFG['public_dir'] . '/config/config.php';

    // Se o commit é o mesmo E o site já está instalado, não há nada a fazer.
    if ($sha === $current && is_file($configFile)) {
        logline('Sem novidades (commit ' . substr($sha, 0, 7) . '). Nada a fazer.');
        exit(0);
    }

    logline('Novo código detetado: ' . substr($sha, 0, 7) . '. A publicar...');

    // 2) Descarregar o ZIP do ramo
    $tmpZip = sys_get_temp_dir() . '/inforo_' . uniqid() . '.zip';
    $zipUrl = "https://api.github.com/repos/{$CFG['repo']}/zipball/{$CFG['branch']}";
    file_put_contents($tmpZip, ghGet($zipUrl, $CFG['token'], true));

    // 3) Extrair
    $tmpDir = sys_get_temp_dir() . '/inforo_' . uniqid();
    mkdir($tmpDir, 0755, true);
    $zip = new ZipArchive();
    if ($zip->open($tmpZip) !== true) {
        throw new RuntimeException('Não foi possível abrir o ZIP descarregado.');
    }
    $zip->extractTo($tmpDir);
    $zip->close();
    unlink($tmpZip);

    // O ZIP do GitHub extrai para uma única subpasta (ex.: inforocasiao-abc123)
    $entries = array_values(array_filter(scandir($tmpDir), fn($e) => $e[0] !== '.'));
    if (count($entries) !== 1 || !is_dir($tmpDir . '/' . $entries[0])) {
        throw new RuntimeException('Estrutura inesperada no ZIP.');
    }
    $srcDir = $tmpDir . '/' . $entries[0];

    // 4) Publicar em public_html (preserva uploads e config.php existentes)
    if (!is_dir($CFG['public_dir'])) {
        mkdir($CFG['public_dir'], 0755, true);
    }
    copyTree($srcDir, $CFG['public_dir']);
    if (!is_dir($CFG['public_dir'] . '/uploads')) {
        mkdir($CFG['public_dir'] . '/uploads', 0755, true);
    }
    rrmdir($tmpDir);
    logline('Ficheiros publicados em ' . $CFG['public_dir']);

    // 5) Escrever config.php com as credenciais (a partir desta configuração)
    $appConfig = [
        'db'  => [
            'host'    => $CFG['db_host'],
            'name'    => $CFG['db_name'],
            'user'    => $CFG['db_user'],
            'pass'    => $CFG['db_pass'],
            'charset' => 'utf8mb4',
        ],
        'app' => ['name' => 'Inforocasião', 'base_url' => '', 'env' => 'production'],
    ];
    if (!is_dir($CFG['public_dir'] . '/config')) {
        mkdir($CFG['public_dir'] . '/config', 0755, true);
    }
    file_put_contents($configFile, "<?php\nreturn " . var_export($appConfig, true) . ";\n");
    logline('config.php atualizado.');

    // 6) Ligar à base de dados e aplicar migrations
    $pdo = new PDO(
        "mysql:host={$CFG['db_host']};dbname={$CFG['db_name']};charset=utf8mb4",
        $CFG['db_user'],
        $CFG['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS schema_migrations (
            migration VARCHAR(255) NOT NULL,
            applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    $applied = $pdo->query("SELECT migration FROM schema_migrations")->fetchAll(PDO::FETCH_COLUMN);
    $files = glob($CFG['public_dir'] . '/database/migrations/*.sql') ?: [];
    sort($files);
    $ran = 0;
    foreach ($files as $file) {
        $name = basename($file);
        if (in_array($name, $applied, true)) {
            continue;
        }
        $pdo->exec((string) file_get_contents($file));
        $pdo->prepare("INSERT INTO schema_migrations (migration) VALUES (?)")->execute([$name]);
        $ran++;
        logline("Migration aplicada: $name");
    }
    logline($ran === 0 ? 'Base de dados já estava atualizada.' : "$ran migration(s) aplicada(s).");

    // 7) Criar o primeiro gestor, se ainda não houver nenhum
    $count = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count === 0 && $CFG['admin_user'] && strlen($CFG['admin_pass']) >= 8
        && $CFG['admin_pass'] !== 'ALTERE_ESTA_PASSWORD') {
        $hash = password_hash($CFG['admin_pass'], PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password_hash, name) VALUES (?,?,?)")
            ->execute([$CFG['admin_user'], $hash, $CFG['admin_user']]);
        logline("Utilizador de gestão '{$CFG['admin_user']}' criado.");
    }

    // 8) Guardar o commit publicado
    file_put_contents($stateFile, $sha);
    logline('DEPLOY CONCLUÍDO COM SUCESSO (' . substr($sha, 0, 7) . ').');

} catch (Throwable $e) {
    logline('ERRO: ' . $e->getMessage());
    exit(1);
}
