<?php
/**
 * Funções utilitárias usadas em toda a aplicação.
 */

declare(strict_types=1);

/** Escapa texto para saída segura em HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Constrói um URL relativo ao base_url configurado. */
function url(string $path = ''): string
{
    $base = rtrim($GLOBALS['config']['app']['base_url'] ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

/** URL de um ficheiro carregado (imagem de produto). Aceita também URLs externos. */
function uploadUrl(?string $file): string
{
    if (!$file) {
        return url('assets/img/placeholder.svg');
    }
    if (preg_match('#^https?://#i', $file)) {
        return $file;
    }
    return url('uploads/' . ltrim($file, '/'));
}

/** Verdadeiro se o valor guardado na imagem é um URL externo (não um ficheiro local). */
function isRemoteImage(?string $file): bool
{
    return $file !== null && preg_match('#^https?://#i', $file) === 1;
}

/** Redireciona e termina a execução. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/** Formata um preço em euros (pt-PT). */
function money($value): string
{
    return number_format((float) $value, 2, ',', '.') . ' €';
}

// --- Mensagens "flash" (aparecem uma vez após redirect) ----------------------
function flash(string $key, ?string $message = null)
{
    if ($message === null) {
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
    $_SESSION['_flash'][$key] = $message;
    return null;
}

// --- Proteção CSRF -----------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sessão expirada ou pedido inválido (CSRF). Volte atrás e tente novamente.');
    }
}

/**
 * Define e/ou lê os metadados SEO da página atual (title, description,
 * canonical, ogImage e jsonld extra). Chamar nas rotas antes de render().
 */
function seo(array $data = []): array
{
    if (!isset($GLOBALS['_seo'])) {
        $GLOBALS['_seo'] = [];
    }
    if ($data) {
        $GLOBALS['_seo'] = array_merge($GLOBALS['_seo'], $data);
    }
    return $GLOBALS['_seo'];
}

/** Renderiza uma view dentro do layout principal. */
function render(string $view, array $data = [], ?string $layout = 'layout'): void
{
    extract($data, EXTR_SKIP);
    $viewFile = BASE_PATH . '/app/views/' . $view . '.php';

    ob_start();
    require $viewFile;
    $content = ob_get_clean();

    if ($layout === null) {
        echo $content;
        return;
    }
    require BASE_PATH . '/app/views/' . $layout . '.php';
}

/** Devolve o SVG de um ícone adequado ao tipo de produto (por categoria). */
function deviceIconSvg(string $category): string
{
    $c = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $category) ?: $category);
    $paths = match (true) {
        str_contains($c, 'telemov') => '<rect x="5" y="2" width="14" height="20" rx="3"/><path d="M12 18h.01"/>',
        str_contains($c, 'portat')  => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/>',
        str_contains($c, 'computad')=> '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
        str_contains($c, 'compon')  => '<rect x="6" y="6" width="12" height="12" rx="1"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/>',
        str_contains($c, 'acess')   => '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3Z"/>',
        default => '<path d="m7.5 4.3 9 5.2M3.3 7.5 12 12.5l8.7-5M12 22V12.5"/><path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>',
    };
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}

/** Cria um slug amigável a partir de um texto. */
function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}
