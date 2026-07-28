<?php
/**
 * Controlador frontal — todos os pedidos passam por aqui (ver .htaccess).
 */

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

// --- Determinar a rota a partir do URL --------------------------------------
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$base = rtrim($GLOBALS['config']['app']['base_url'] ?? '', '/');
if ($base && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
$path   = '/' . trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --- Rotas -------------------------------------------------------------------
try {
    switch (true) {

        // ---------- Site público ----------
        case $path === '/' && $method === 'GET':
            $destaques = Database::all(
                "SELECT p.*, c.name AS category_name FROM products p
                 LEFT JOIN categories c ON c.id = p.category_id
                 WHERE p.is_active = 1 AND p.is_featured = 1
                 ORDER BY p.created_at DESC LIMIT 4"
            );
            seo(['description' => Seo::DESCRIPTION, 'canonical' => '/']);
            render('home', ['destaques' => $destaques, 'title' => null]);
            break;

        case $path === '/produtos' && $method === 'GET':
            $categoria = isset($_GET['categoria']) ? (string) $_GET['categoria'] : '';
            $q         = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
            $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                    FROM products p LEFT JOIN categories c ON c.id = p.category_id
                    WHERE p.is_active = 1";
            $params = [];
            if ($categoria !== '') {
                $sql .= " AND c.slug = ?";
                $params[] = $categoria;
            }
            if ($q !== '') {
                $sql .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
                $like = '%' . $q . '%';
                array_push($params, $like, $like, $like);
            }
            $sql .= " ORDER BY p.created_at DESC";
            $produtos   = Database::all($sql, $params);
            $categorias = Database::all("SELECT * FROM categories ORDER BY name");
            seo([
                'description' => 'Catálogo da Inforocasião: computadores, portáteis, telemóveis e '
                    . 'componentes electrónicos, novos e recondicionados, em Cucujães. Veja preços e disponibilidade.',
                'canonical'   => '/produtos',
            ]);
            render('products', compact('produtos', 'categorias', 'categoria', 'q') + ['title' => 'Produtos']);
            break;

        case preg_match('#^/produto/(\d+)$#', $path, $m) === 1 && $method === 'GET':
            $produto = Database::one(
                "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                 FROM products p LEFT JOIN categories c ON c.id = p.category_id
                 WHERE p.id = ? AND p.is_active = 1",
                [(int) $m[1]]
            );
            if (!$produto) {
                http_response_code(404);
                render('404', ['title' => 'Não encontrado']);
                break;
            }
            $galeria = array_values(array_filter([
                $produto['image'], $produto['image2'], $produto['image3'], $produto['image4'],
            ]));
            $pDesc = trim((string) ($produto['description'] ?? ''));
            if ($pDesc === '') {
                $pDesc = sprintf(
                    '%s%s · %s por %s na Inforocasião, em Cucujães.',
                    $produto['name'],
                    !empty($produto['brand']) ? ' ' . $produto['brand'] : '',
                    $produto['condition'] ?? 'Novo',
                    money($produto['price'])
                );
            }
            seo([
                'description' => mb_strimwidth($pDesc, 0, 160, '…'),
                'canonical'   => '/produto/' . $produto['id'],
                'jsonld'      => [
                    Seo::productJsonLd($produto, $galeria),
                    Seo::breadcrumbJsonLd([
                        ['Início', '/'],
                        ['Produtos', '/produtos'],
                        [$produto['name'], '/produto/' . $produto['id']],
                    ]),
                ],
            ]);
            render('product', ['produto' => $produto, 'galeria' => $galeria, 'title' => $produto['name']]);
            break;

        case $path === '/servicos' && $method === 'GET':
            seo([
                'description' => 'Reparação de telemóveis e computadores em Cucujães: ecrãs, baterias, '
                    . 'remoção de vírus, upgrades (SSD/RAM), recuperação de dados e recondicionamento. '
                    . 'Diagnóstico e orçamento sem compromisso.',
                'canonical'   => '/servicos',
            ]);
            render('services', ['title' => 'Serviços e Reparações']);
            break;

        case $path === '/contactos' && $method === 'GET':
            seo([
                'description' => 'Contactos da Inforocasião em Cucujães: Rua do Clube Desportivo de Cucujães 275, '
                    . '3720-385. Telemóvel/WhatsApp 912 138 094. Horário e mapa.',
                'canonical'   => '/contactos',
            ]);
            render('contact', ['title' => 'Contactos']);
            break;

        // ---------- SEO: sitemap e robots ----------
        case $path === '/sitemap.xml' && $method === 'GET':
            header('Content-Type: application/xml; charset=utf-8');
            $urls = [
                ['/', '1.0'], ['/produtos', '0.9'], ['/servicos', '0.8'], ['/contactos', '0.7'],
            ];
            $prod = Database::all("SELECT id, updated_at FROM products WHERE is_active = 1 ORDER BY updated_at DESC");
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($urls as [$p, $prio]) {
                echo '  <url><loc>' . e(Seo::abs($p)) . '</loc><changefreq>weekly</changefreq><priority>' . $prio . "</priority></url>\n";
            }
            foreach ($prod as $pr) {
                $lastmod = !empty($pr['updated_at']) ? date('Y-m-d', strtotime($pr['updated_at'])) : date('Y-m-d');
                echo '  <url><loc>' . e(Seo::abs('/produto/' . $pr['id'])) . '</loc><lastmod>' . $lastmod . "</lastmod><changefreq>weekly</changefreq><priority>0.6</priority></url>\n";
            }
            echo '</urlset>';
            break;

        case $path === '/robots.txt' && $method === 'GET':
            header('Content-Type: text/plain; charset=utf-8');
            echo "User-agent: *\n";
            echo "Allow: /\n";
            echo "Disallow: /admin\n\n";
            echo "Sitemap: " . Seo::abs('/sitemap.xml') . "\n";
            break;

        // ---------- Autenticação ----------
        case $path === '/admin' && $method === 'GET':
            redirect(Auth::check() ? '/admin/dashboard' : '/admin/login');
            break;

        case $path === '/admin/login' && $method === 'GET':
            if (Auth::check()) redirect('/admin/dashboard');
            render('admin/login', ['title' => 'Entrar'], 'layout_admin');
            break;

        case $path === '/admin/login' && $method === 'POST':
            csrf_verify();
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            if (Auth::attempt($username, $password)) {
                redirect('/admin/dashboard');
            }
            flash('error', 'Credenciais inválidas.');
            redirect('/admin/login');
            break;

        case $path === '/admin/logout' && $method === 'POST':
            csrf_verify();
            Auth::logout();
            redirect('/admin/login');
            break;

        // ---------- Painel de gestão ----------
        case $path === '/admin/dashboard' && $method === 'GET':
            Auth::requireLogin();
            $produtos = Database::all(
                "SELECT p.*, c.name AS category_name FROM products p
                 LEFT JOIN categories c ON c.id = p.category_id
                 ORDER BY p.created_at DESC"
            );
            render('admin/dashboard', ['produtos' => $produtos, 'title' => 'Painel'], 'layout_admin');
            break;

        case $path === '/admin/produtos/novo' && $method === 'GET':
            Auth::requireLogin();
            $categorias = Database::all("SELECT * FROM categories ORDER BY name");
            render('admin/product_form', [
                'produto' => null, 'categorias' => $categorias, 'title' => 'Novo artigo',
            ], 'layout_admin');
            break;

        case $path === '/admin/produtos' && $method === 'POST':
            Auth::requireLogin();
            csrf_verify();
            saveProduct(null);
            break;

        case preg_match('#^/admin/produtos/(\d+)/editar$#', $path, $m) === 1 && $method === 'GET':
            Auth::requireLogin();
            $produto = Database::one("SELECT * FROM products WHERE id = ?", [(int) $m[1]]);
            if (!$produto) { http_response_code(404); render('404', ['title' => '404'], 'layout_admin'); break; }
            $categorias = Database::all("SELECT * FROM categories ORDER BY name");
            render('admin/product_form', [
                'produto' => $produto, 'categorias' => $categorias, 'title' => 'Editar artigo',
            ], 'layout_admin');
            break;

        case preg_match('#^/admin/produtos/(\d+)$#', $path, $m) === 1 && $method === 'POST':
            Auth::requireLogin();
            csrf_verify();
            saveProduct((int) $m[1]);
            break;

        case preg_match('#^/admin/produtos/(\d+)/apagar$#', $path, $m) === 1 && $method === 'POST':
            Auth::requireLogin();
            csrf_verify();
            $produto = Database::one(
                "SELECT image, image2, image3, image4 FROM products WHERE id = ?",
                [(int) $m[1]]
            );
            Database::run("DELETE FROM products WHERE id = ?", [(int) $m[1]]);
            if ($produto) {
                foreach ([$produto['image'], $produto['image2'], $produto['image3'], $produto['image4']] as $img) {
                    if ($img && !isRemoteImage($img)) {
                        @unlink(BASE_PATH . '/uploads/' . $img);
                    }
                }
            }
            flash('success', 'Artigo apagado.');
            redirect('/admin/dashboard');
            break;

        // ---------- 404 ----------
        default:
            http_response_code(404);
            render('404', ['title' => 'Página não encontrada']);
    }
} catch (Throwable $ex) {
    if (($GLOBALS['config']['app']['env'] ?? 'production') === 'development') {
        http_response_code(500);
        echo '<pre>' . e($ex->getMessage() . "\n" . $ex->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        render('500', ['title' => 'Erro']);
    }
}

/**
 * Cria ou atualiza um produto a partir do formulário do painel.
 */
function saveProduct(?int $id): void
{
    $name        = trim((string) ($_POST['name'] ?? ''));
    $brand       = trim((string) ($_POST['brand'] ?? ''));
    $categoryId  = (int) ($_POST['category_id'] ?? 0) ?: null;
    $price       = (float) str_replace(',', '.', (string) ($_POST['price'] ?? '0'));
    $stock       = (int) ($_POST['stock'] ?? 0);
    $condition   = (string) ($_POST['condition'] ?? 'Novo');
    $description = trim((string) ($_POST['description'] ?? ''));
    $isActive    = isset($_POST['is_active']) ? 1 : 0;
    $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;

    $allowedConditions = ['Novo', 'Usado', 'Recondicionado'];
    if (!in_array($condition, $allowedConditions, true)) {
        $condition = 'Novo';
    }

    if ($name === '') {
        flash('error', 'O nome do artigo é obrigatório.');
        redirect($id ? "/admin/produtos/$id/editar" : '/admin/produtos/novo');
    }

    // Imagem principal (slot 1): ficheiro carregado tem prioridade; senão, URL colado (opcional)
    $imageName = resolveImageSlot('image', 'image_url');

    // Imagens extra (slots 2 a 4): cada uma pode ser substituída, removida, ou deixada como está
    $extraSlots = [];
    for ($slot = 2; $slot <= 4; $slot++) {
        $col = "image{$slot}";
        $new = resolveImageSlot($col, "image_url{$slot}");
        if ($new !== null) {
            $extraSlots[$col] = ['action' => 'replace', 'value' => $new];
        } elseif (!empty($_POST["{$col}_remove"])) {
            $extraSlots[$col] = ['action' => 'remove', 'value' => null];
        }
        // se nada foi enviado nem marcado, o slot fica como está (nenhuma entrada no array)
    }

    if ($id === null) {
        Database::run(
            "INSERT INTO products
             (name, brand, category_id, price, stock, `condition`, description, image, image2, image3, image4, is_active, is_featured, created_at, updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())",
            [
                $name, $brand, $categoryId, $price, $stock, $condition, $description, $imageName,
                $extraSlots['image2']['value'] ?? null,
                $extraSlots['image3']['value'] ?? null,
                $extraSlots['image4']['value'] ?? null,
                $isActive, $isFeatured,
            ]
        );
        flash('success', 'Artigo criado com sucesso.');
    } else {
        $old = Database::one(
            "SELECT image, image2, image3, image4 FROM products WHERE id = ?",
            [$id]
        );

        // Apaga o ficheiro antigo sempre que a imagem principal é substituída
        if ($imageName !== null && $old && $old['image'] && !isRemoteImage($old['image'])) {
            @unlink(BASE_PATH . '/uploads/' . $old['image']);
        }

        $sql = "UPDATE products SET name=?, brand=?, category_id=?, price=?, stock=?,
                `condition`=?, description=?, is_active=?, is_featured=?, updated_at=NOW()";
        $params = [$name, $brand, $categoryId, $price, $stock, $condition, $description, $isActive, $isFeatured];

        if ($imageName !== null) {
            $sql .= ", image=?";
            $params[] = $imageName;
        }
        foreach ($extraSlots as $col => $change) {
            // Substituir ou remover: sempre que muda, apaga o ficheiro antigo local (se existir)
            if ($old && $old[$col] && !isRemoteImage($old[$col])) {
                @unlink(BASE_PATH . '/uploads/' . $old[$col]);
            }
            $sql .= ", {$col}=?";
            $params[] = $change['value']; // null (remover) ou o novo ficheiro/URL (substituir)
        }

        $sql .= " WHERE id=?";
        $params[] = $id;
        Database::run($sql, $params);
        flash('success', 'Artigo atualizado com sucesso.');
    }

    redirect('/admin/dashboard');
}

/** Resolve um slot de imagem: ficheiro carregado tem prioridade sobre URL colado. */
function resolveImageSlot(string $fileKey, string $urlKey): ?string
{
    if (!empty($_FILES[$fileKey]['name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        return handleImageUpload($_FILES[$fileKey]);
    }
    if (!empty($_POST[$urlKey])) {
        $urlIn = trim((string) $_POST[$urlKey]);
        if (preg_match('#^https?://#i', $urlIn)) {
            return $urlIn;
        }
        flash('error', 'O URL da imagem deve começar por http:// ou https://');
    }
    return null;
}

/** Valida e guarda uma imagem carregada. Devolve o nome do ficheiro. */
function handleImageUpload(array $file): ?string
{
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        flash('error', 'Formato de imagem não suportado (use JPG, PNG, WEBP ou GIF).');
        return null;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        flash('error', 'A imagem excede 5 MB.');
        return null;
    }
    $dir = BASE_PATH . '/uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    move_uploaded_file($file['tmp_name'], $dir . '/' . $newName);
    return $newName;
}
