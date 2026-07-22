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

/** URL de um ficheiro carregado (imagem de produto). */
function uploadUrl(?string $file): string
{
    if (!$file) {
        return url('assets/img/placeholder.svg');
    }
    return url('uploads/' . ltrim($file, '/'));
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

/** Cria um slug amigável a partir de um texto. */
function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}
