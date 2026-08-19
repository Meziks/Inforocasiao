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
                 ORDER BY p.created_at DESC"
            );
            // Se não há pelo menos 4 destaques escolhidos, completa com os artigos mais recentes
            if (count($destaques) < 4) {
                $destaques = Database::all(
                    "SELECT p.*, c.name AS category_name FROM products p
                     LEFT JOIN categories c ON c.id = p.category_id
                     WHERE p.is_active = 1
                     ORDER BY p.created_at DESC LIMIT 4"
                );
            }
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
            $galeria = array_values(array_filter(array_merge(
                [$produto['image']],
                array_column(extraProductImages((int) $produto['id']), 'image')
            )));
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

        // ---------- Contas de cliente ----------
        case $path === '/registo' && $method === 'GET':
            if (CustomerAuth::check()) redirect('/conta');
            seo(['canonical' => '/registo']);
            render('auth/registo', ['title' => 'Criar conta']);
            break;

        case $path === '/registo' && $method === 'POST':
            csrf_verify();
            $name     = trim((string) ($_POST['name'] ?? ''));
            $email    = strtolower(trim((string) ($_POST['email'] ?? '')));
            $password = (string) ($_POST['password'] ?? '');
            $phone    = trim((string) ($_POST['phone'] ?? '')) ?: null;

            if ($name === '' || $email === '' || strlen($password) < 8) {
                flash('error', 'Preencha o nome, email e uma password com pelo menos 8 caracteres.');
                redirect('/registo');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Introduza um email válido.');
                redirect('/registo');
            }

            $id = CustomerAuth::register($name, $email, $password, $phone);
            if ($id === null) {
                flash('error', 'Já existe uma conta com este email. Experimente entrar em vez de criar conta nova.');
                redirect('/registo');
            }
            flash('success', 'Conta criada com sucesso! Bem-vindo(a), ' . $name . '.');
            redirect('/conta');
            break;

        case $path === '/login' && $method === 'GET':
            if (CustomerAuth::check()) redirect('/conta');
            seo(['canonical' => '/login']);
            render('auth/login', ['title' => 'Entrar']);
            break;

        case $path === '/login' && $method === 'POST':
            csrf_verify();
            $email    = strtolower(trim((string) ($_POST['email'] ?? '')));
            $password = (string) ($_POST['password'] ?? '');
            if (CustomerAuth::attempt($email, $password)) {
                redirect('/conta');
            }
            flash('error', 'Email ou password incorretos.');
            redirect('/login');
            break;

        case $path === '/logout' && $method === 'POST':
            csrf_verify();
            CustomerAuth::logout();
            redirect('/');
            break;

        case $path === '/conta' && $method === 'GET':
            CustomerAuth::requireLogin();
            $cliente = CustomerAuth::user();
            $encomendas = Database::all(
                'SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC',
                [$cliente['id']]
            );
            render('account/index', ['cliente' => $cliente, 'encomendas' => $encomendas, 'title' => 'A minha conta']);
            break;

        case $path === '/recuperar-password' && $method === 'GET':
            seo(['canonical' => '/recuperar-password']);
            render('auth/recuperar', ['title' => 'Recuperar password']);
            break;

        case $path === '/recuperar-password' && $method === 'POST':
            csrf_verify();
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            if ($email !== '') {
                $token = CustomerAuth::createPasswordReset($email);
                if ($token !== null) {
                    $link = Seo::abs('/redefinir-password/' . $token);
                    Mailer::send(
                        $email,
                        '',
                        'Recuperar a sua password — Inforocasião',
                        '<p>Pediu para redefinir a password da sua conta na Inforocasião.</p>'
                        . '<p><a href="' . e($link) . '">Clique aqui para definir uma nova password</a></p>'
                        . '<p>Este link é válido durante 1 hora. Se não foi você a pedir isto, ignore este email.</p>'
                    );
                }
            }
            // Mensagem sempre igual, exista ou não conta com este email (não revelar quais emails têm conta).
            flash('success', 'Se existir uma conta com esse email, enviámos um link para redefinir a password.');
            redirect('/recuperar-password');
            break;

        case preg_match('#^/redefinir-password/([a-f0-9]{64})$#', $path, $m) === 1 && $method === 'GET':
            $customerId = CustomerAuth::validatePasswordResetToken($m[1]);
            if ($customerId === null) {
                flash('error', 'Este link é inválido ou já expirou. Peça um novo.');
                redirect('/recuperar-password');
            }
            seo(['canonical' => '/redefinir-password/' . $m[1]]);
            render('auth/redefinir', ['token' => $m[1], 'title' => 'Nova password']);
            break;

        case preg_match('#^/redefinir-password/([a-f0-9]{64})$#', $path, $m) === 1 && $method === 'POST':
            csrf_verify();
            $customerId = CustomerAuth::validatePasswordResetToken($m[1]);
            if ($customerId === null) {
                flash('error', 'Este link é inválido ou já expirou. Peça um novo.');
                redirect('/recuperar-password');
            }
            $password = (string) ($_POST['password'] ?? '');
            if (strlen($password) < 8) {
                flash('error', 'A password deve ter pelo menos 8 caracteres.');
                redirect('/redefinir-password/' . $m[1]);
            }
            CustomerAuth::resetPassword($customerId, $password);
            flash('success', 'Password alterada com sucesso. Já pode entrar.');
            redirect('/login');
            break;

        // ---------- Páginas legais ----------
        case $path === '/termos' && $method === 'GET':
            seo(['canonical' => '/termos', 'description' => 'Termos de Utilização da Inforocasião.']);
            render('legal/termos', ['title' => 'Termos de Utilização']);
            break;

        case $path === '/privacidade' && $method === 'GET':
            seo(['canonical' => '/privacidade', 'description' => 'Política de Privacidade da Inforocasião.']);
            render('legal/privacidade', ['title' => 'Política de Privacidade']);
            break;

        // ---------- Carrinho e checkout ----------
        case $path === '/carrinho/adicionar' && $method === 'POST':
            csrf_verify();
            $productId = (int) ($_POST['product_id'] ?? 0);
            $produto   = Database::one('SELECT id FROM products WHERE id = ? AND is_active = 1', [$productId]);
            if ($produto) {
                Cart::add($productId, 1);
                flash('success', 'Artigo adicionado ao carrinho.');
            } else {
                flash('error', 'Esse artigo já não está disponível.');
            }
            $redirect = (string) ($_POST['redirect'] ?? '/carrinho');
            redirect(str_starts_with($redirect, '/') && !str_starts_with($redirect, '//') ? $redirect : '/carrinho');
            break;

        case $path === '/carrinho/atualizar' && $method === 'POST':
            csrf_verify();
            Cart::setQty((int) ($_POST['product_id'] ?? 0), (int) ($_POST['qty'] ?? 0));
            redirect('/carrinho');
            break;

        case $path === '/carrinho/remover' && $method === 'POST':
            csrf_verify();
            Cart::remove((int) ($_POST['product_id'] ?? 0));
            redirect('/carrinho');
            break;

        case $path === '/carrinho' && $method === 'GET':
            seo(['canonical' => '/carrinho']);
            render('cart/index', ['itens' => Cart::items(), 'total' => Cart::total(), 'title' => 'Carrinho']);
            break;

        case $path === '/checkout' && $method === 'GET':
            CustomerAuth::requireLogin();
            $itens = Cart::items();
            if (!$itens) {
                flash('error', 'O seu carrinho está vazio.');
                redirect('/carrinho');
            }
            seo(['canonical' => '/checkout']);
            render('cart/checkout', [
                'itens' => $itens, 'total' => Cart::total(), 'cliente' => CustomerAuth::user(), 'title' => 'Checkout',
            ]);
            break;

        case $path === '/checkout' && $method === 'POST':
            csrf_verify();
            CustomerAuth::requireLogin();
            placeOrder();
            break;

        case preg_match('#^/encomendas/(\d+)$#', $path, $m) === 1 && $method === 'GET':
            CustomerAuth::requireLogin();
            $cliente = CustomerAuth::user();
            $encomenda = Database::one('SELECT * FROM orders WHERE id = ? AND customer_id = ?', [(int) $m[1], $cliente['id']]);
            if (!$encomenda) {
                http_response_code(404);
                render('404', ['title' => 'Não encontrado']);
                break;
            }
            $itensEncomenda = Database::all('SELECT * FROM order_items WHERE order_id = ?', [$encomenda['id']]);
            render('cart/order', ['encomenda' => $encomenda, 'itens' => $itensEncomenda, 'title' => 'Encomenda #' . $encomenda['id']]);
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
            echo "Disallow: /admin\n";
            echo "Disallow: /v2\n\n";
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

            // Estatísticas gerais (sempre sobre todo o catálogo, ignora filtros)
            $stats = Database::one(
                "SELECT COUNT(*) AS total,
                        SUM(is_active = 1) AS visiveis,
                        SUM(is_featured = 1) AS destaque,
                        SUM(stock = 0) AS esgotados
                 FROM products"
            );

            $q        = trim((string) ($_GET['q'] ?? ''));
            $catFiltro = (int) ($_GET['categoria'] ?? 0);
            $estado   = (string) ($_GET['estado'] ?? '');

            $sql = "SELECT p.*, c.name AS category_name FROM products p
                    LEFT JOIN categories c ON c.id = p.category_id WHERE 1=1";
            $params = [];
            if ($q !== '') {
                $sql .= " AND (p.name LIKE ? OR p.brand LIKE ?)";
                $like = '%' . $q . '%';
                array_push($params, $like, $like);
            }
            if ($catFiltro > 0) {
                $sql .= " AND p.category_id = ?";
                $params[] = $catFiltro;
            }
            if ($estado === 'visivel') {
                $sql .= " AND p.is_active = 1";
            } elseif ($estado === 'oculto') {
                $sql .= " AND p.is_active = 0";
            } elseif ($estado === 'destaque') {
                $sql .= " AND p.is_featured = 1";
            } elseif ($estado === 'esgotado') {
                $sql .= " AND p.stock = 0";
            }
            $sql .= " ORDER BY p.created_at DESC";
            $produtos   = Database::all($sql, $params);
            $categorias = Database::all("SELECT * FROM categories ORDER BY name");

            render('admin/dashboard', [
                'produtos' => $produtos, 'stats' => $stats, 'categorias' => $categorias,
                'q' => $q, 'catFiltro' => $catFiltro, 'estado' => $estado, 'title' => 'Painel',
            ], 'layout_admin');
            break;

        case $path === '/admin/produtos/novo' && $method === 'GET':
            Auth::requireLogin();
            $categorias = Database::all("SELECT * FROM categories ORDER BY name");
            render('admin/product_form', [
                'produto' => null, 'categorias' => $categorias, 'currentImages' => [], 'title' => 'Novo artigo',
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
            // Imagem principal + extras, pela ordem em que aparecem no artigo
            $currentImages = [];
            if (!empty($produto['image'])) {
                $currentImages[] = $produto['image'];
            }
            foreach (extraProductImages((int) $produto['id']) as $row) {
                $currentImages[] = $row['image'];
            }
            render('admin/product_form', [
                'produto' => $produto, 'categorias' => $categorias, 'currentImages' => $currentImages,
                'title' => 'Editar artigo',
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
            $produto = Database::one("SELECT image FROM products WHERE id = ?", [(int) $m[1]]);
            $extraDel = extraProductImages((int) $m[1]);
            Database::run("DELETE FROM products WHERE id = ?", [(int) $m[1]]); // product_images cai em cascata
            if ($produto && $produto['image'] && !isRemoteImage($produto['image'])) {
                @unlink(BASE_PATH . '/uploads/' . $produto['image']);
            }
            foreach ($extraDel as $row) {
                if ($row['image'] && !isRemoteImage($row['image'])) {
                    @unlink(BASE_PATH . '/uploads/' . $row['image']);
                }
            }
            flash('success', 'Artigo apagado.');
            redirect('/admin/dashboard');
            break;

        // ---------- Painel de gestão: encomendas ----------
        case $path === '/admin/encomendas' && $method === 'GET':
            Auth::requireLogin();
            $estadoFiltro = (string) ($_GET['estado'] ?? '');
            $sql = "SELECT o.*, c.name AS customer_name, c.email AS customer_email
                    FROM orders o JOIN customers c ON c.id = o.customer_id WHERE 1=1";
            $params = [];
            if ($estadoFiltro !== '') {
                $sql .= " AND o.status = ?";
                $params[] = $estadoFiltro;
            }
            $sql .= " ORDER BY o.created_at DESC";
            render('admin/orders', [
                'encomendas' => Database::all($sql, $params), 'estadoFiltro' => $estadoFiltro, 'title' => 'Encomendas',
            ], 'layout_admin');
            break;

        case preg_match('#^/admin/encomendas/(\d+)$#', $path, $m) === 1 && $method === 'GET':
            Auth::requireLogin();
            $encomenda = Database::one(
                "SELECT o.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
                 FROM orders o JOIN customers c ON c.id = o.customer_id WHERE o.id = ?",
                [(int) $m[1]]
            );
            if (!$encomenda) {
                http_response_code(404);
                render('404', ['title' => 'Não encontrado'], 'layout_admin');
                break;
            }
            $itensEncomenda = Database::all('SELECT * FROM order_items WHERE order_id = ?', [$encomenda['id']]);
            render('admin/order_detail', [
                'encomenda' => $encomenda, 'itens' => $itensEncomenda, 'title' => 'Encomenda #' . $encomenda['id'],
            ], 'layout_admin');
            break;

        case preg_match('#^/admin/encomendas/(\d+)$#', $path, $m) === 1 && $method === 'POST':
            Auth::requireLogin();
            csrf_verify();
            updateOrderStatus((int) $m[1], (string) ($_POST['status'] ?? ''));
            break;

        // ---------- 404 ----------
        default:
            http_response_code(404);
            render('404', ['title' => 'Página não encontrada']);
    }
} catch (Throwable $ex) {
    logError('Pedido: ' . $method . ' ' . $path, $ex);
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

    // Imagens antes de qualquer alteração, para saber depois quais os
    // ficheiros locais que deixaram de estar em uso e podem ser apagados.
    $previousImages = [];
    if ($id !== null) {
        $old = Database::one("SELECT image FROM products WHERE id = ?", [$id]);
        if ($old && $old['image']) {
            $previousImages[] = $old['image'];
        }
        foreach (extraProductImages($id) as $row) {
            $previousImages[] = $row['image'];
        }
    }

    // Imagens submetidas (upload múltiplo + URLs + já existentes), já pela
    // ordem final escolhida no formulário; a primeira é a imagem principal.
    $finalImages = collectSubmittedImages();
    $mainImage   = $finalImages[0] ?? null;

    if ($id === null) {
        Database::run(
            "INSERT INTO products
             (name, brand, category_id, price, stock, `condition`, description, image, is_active, is_featured, created_at, updated_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),NOW())",
            [$name, $brand, $categoryId, $price, $stock, $condition, $description, $mainImage, $isActive, $isFeatured]
        );
        $id = (int) Database::pdo()->lastInsertId();
        flash('success', 'Artigo criado com sucesso.');
    } else {
        Database::run(
            "UPDATE products SET name=?, brand=?, category_id=?, price=?, stock=?,
             `condition`=?, description=?, image=?, is_active=?, is_featured=?, updated_at=NOW()
             WHERE id=?",
            [$name, $brand, $categoryId, $price, $stock, $condition, $description, $mainImage, $isActive, $isFeatured, $id]
        );
        flash('success', 'Artigo atualizado com sucesso.');
    }

    applyExtraImages($id, array_slice($finalImages, 1));
    removeUnusedImageFiles($previousImages, $finalImages);
    redirect('/admin/dashboard');
}

/**
 * Junta as imagens enviadas no formulário (já existentes mantidas + novos
 * ficheiros carregados + novos URLs colados) na ordem final escolhida pelo
 * utilizador, descrita em "image_order" (tokens "e"/"u"/"n" que apontam,
 * por ordem de aparição, para existing_images[], new_urls[] e images[]).
 * Limita sempre a 4 imagens por artigo (1 principal + 3 extra).
 */
function collectSubmittedImages(): array
{
    $order    = array_values(array_filter(explode(',', (string) ($_POST['image_order'] ?? ''))));
    $existing = array_values(array_filter((array) ($_POST['existing_images'] ?? []), fn($v) => trim((string) $v) !== ''));
    $urls     = array_values((array) ($_POST['new_urls'] ?? []));
    $uploaded = handleImageUploads($_FILES['images'] ?? null);

    $final = [];
    $ei = 0; $ui = 0; $ni = 0;
    foreach ($order as $token) {
        if ($token === 'e' && isset($existing[$ei])) {
            $final[] = $existing[$ei];
            $ei++;
        } elseif ($token === 'u' && array_key_exists($ui, $urls)) {
            $urlIn = trim((string) $urls[$ui]);
            $ui++;
            if ($urlIn !== '') {
                if (preg_match('#^https?://#i', $urlIn)) {
                    $final[] = $urlIn;
                } else {
                    flash('error', 'O URL da imagem deve começar por http:// ou https://');
                }
            }
        } elseif ($token === 'n' && array_key_exists($ni, $uploaded)) {
            if ($uploaded[$ni] !== null) {
                $final[] = $uploaded[$ni];
            }
            $ni++;
        }
    }

    return array_slice($final, 0, 4);
}

/**
 * Substitui as imagens extra (2ª a 4ª) de um produto em product_images
 * pela lista final indicada (já sem a imagem principal), na mesma ordem.
 */
function applyExtraImages(int $productId, array $extras): void
{
    Database::run("DELETE FROM product_images WHERE product_id = ?", [$productId]);
    foreach (array_slice($extras, 0, 3) as $i => $image) {
        Database::run(
            "INSERT INTO product_images (product_id, image, sort_order) VALUES (?,?,?)",
            [$productId, $image, $i + 1]
        );
    }
}

/** Apaga do disco os ficheiros locais que já não constam da lista final de imagens. */
function removeUnusedImageFiles(array $previousImages, array $finalImages): void
{
    foreach (array_diff($previousImages, $finalImages) as $image) {
        if ($image && !isRemoteImage($image)) {
            @unlink(BASE_PATH . '/uploads/' . $image);
        }
    }
}

/**
 * Devolve as imagens extra (slots 2-4) de um produto, a partir de
 * product_images. Se a tabela ainda não existir (ex.: mesmo instante em
 * que a migration está a ser aplicada no deploy), devolve [] em vez de
 * rebentar a página — a foto principal continua a aparecer na mesma.
 */
function extraProductImages(int $productId): array
{
    try {
        return Database::all(
            "SELECT image FROM product_images WHERE product_id = ? ORDER BY sort_order",
            [$productId]
        );
    } catch (Throwable $e) {
        logError('extraProductImages', $e);
        return [];
    }
}

/**
 * Normaliza o array multi-ficheiro de $_FILES['images'] (upload múltiplo,
 * name="images[]") numa lista de nomes de ficheiro guardados, na mesma
 * ordem em que foram selecionados. Entradas inválidas/falhadas ficam a
 * null (mas mantêm a posição, para não desalinhar a ordem final).
 */
function handleImageUploads(?array $filesEntry): array
{
    if (!$filesEntry || empty($filesEntry['name'])) {
        return [];
    }
    $names = (array) $filesEntry['name'];
    $results = [];
    foreach ($names as $i => $name) {
        $single = [
            'name'     => $name,
            'type'     => $filesEntry['type'][$i] ?? '',
            'tmp_name' => $filesEntry['tmp_name'][$i] ?? '',
            'error'    => $filesEntry['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size'     => $filesEntry['size'][$i] ?? 0,
        ];
        if ($single['name'] === '' || $single['error'] !== UPLOAD_ERR_OK) {
            $results[] = null;
            continue;
        }
        $results[] = handleImageUpload($single);
    }
    return $results;
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

/**
 * Processa o checkout: valida o formulário, reserva stock numa única
 * transação (com SELECT ... FOR UPDATE sobre todos os artigos do carrinho
 * de uma vez, para nunca vender a mesma unidade a duas pessoas em
 * simultâneo), cria a encomenda e limpa o carrinho.
 */
function placeOrder(): void
{
    $itens = Cart::items();
    if (!$itens) {
        flash('error', 'O seu carrinho está vazio.');
        redirect('/carrinho');
    }

    $fulfillment = (string) ($_POST['fulfillment'] ?? '');
    if (!in_array($fulfillment, ['levantamento', 'envio'], true)) {
        flash('error', 'Escolha uma forma de entrega.');
        redirect('/checkout');
    }

    $shippingName    = trim((string) ($_POST['shipping_name'] ?? ''));
    $shippingAddress = trim((string) ($_POST['shipping_address'] ?? ''));
    $shippingPostal  = trim((string) ($_POST['shipping_postal'] ?? ''));
    $shippingCity    = trim((string) ($_POST['shipping_city'] ?? ''));
    $phone           = trim((string) ($_POST['phone'] ?? ''));
    $notes           = trim((string) ($_POST['notes'] ?? ''));

    if ($fulfillment === 'envio' && ($shippingName === '' || $shippingAddress === '' || $shippingPostal === '' || $shippingCity === '')) {
        flash('error', 'Preencha a morada de envio.');
        redirect('/checkout');
    }
    if ($phone === '') {
        flash('error', 'Indique um telefone de contacto.');
        redirect('/checkout');
    }

    $cliente = CustomerAuth::user();
    $pdo     = Database::pdo();
    $pdo->beginTransaction();

    try {
        $ids = array_map(fn($i) => (int) $i['product']['id'], $itens);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = Database::all("SELECT * FROM products WHERE id IN ($placeholders) FOR UPDATE", $ids);

        $lockedProducts = [];
        foreach ($rows as $row) {
            $lockedProducts[(int) $row['id']] = $row;
        }

        $insufficient = [];
        $total = 0.0;
        foreach ($itens as $item) {
            $pid     = (int) $item['product']['id'];
            $qty     = (int) $item['qty'];
            $current = $lockedProducts[$pid] ?? null;
            if (!$current || !$current['is_active'] || (int) $current['stock'] < $qty) {
                $insufficient[] = $item['product']['name'];
                continue;
            }
            $total += (float) $current['price'] * $qty;
        }

        if ($insufficient) {
            $pdo->rollBack();
            // Corrige o carrinho para o stock real, para o cliente ver logo o que mudou.
            foreach ($itens as $item) {
                $pid   = (int) $item['product']['id'];
                $fresh = Database::one('SELECT stock, is_active FROM products WHERE id = ?', [$pid]);
                if (!$fresh || !$fresh['is_active']) {
                    Cart::remove($pid);
                } elseif ((int) $fresh['stock'] < $item['qty']) {
                    Cart::setQty($pid, (int) $fresh['stock']);
                }
            }
            flash('error', 'Já não há stock suficiente de: ' . implode(', ', $insufficient) . '. O carrinho foi atualizado.');
            redirect('/carrinho');
        }

        $pdo->prepare(
            'INSERT INTO orders (customer_id, status, fulfillment, shipping_name, shipping_address, shipping_postal, shipping_city, phone, notes, total)
             VALUES (?, "pendente", ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $cliente['id'], $fulfillment,
            $fulfillment === 'envio' ? $shippingName : null,
            $fulfillment === 'envio' ? $shippingAddress : null,
            $fulfillment === 'envio' ? $shippingPostal : null,
            $fulfillment === 'envio' ? $shippingCity : null,
            $phone, $notes !== '' ? $notes : null, $total,
        ]);
        $orderId = (int) $pdo->lastInsertId();

        foreach ($itens as $item) {
            $pid     = (int) $item['product']['id'];
            $qty     = (int) $item['qty'];
            $current = $lockedProducts[$pid];
            $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, unit_price, qty) VALUES (?, ?, ?, ?, ?)'
            )->execute([$orderId, $pid, $current['name'], $current['price'], $qty]);
            $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?')->execute([$qty, $pid]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        logError('placeOrder', $e);
        flash('error', 'Não foi possível concluir a encomenda. Tente novamente.');
        redirect('/checkout');
    }

    Cart::clear();

    Mailer::send(
        $cliente['email'], $cliente['name'],
        'Confirmação da encomenda #' . $orderId . ' — Inforocasião',
        '<p>Olá ' . e($cliente['name']) . ',</p>'
        . '<p>Recebemos a sua encomenda #' . $orderId . ', no valor de ' . e(money($total)) . '.</p>'
        . '<p>' . ($fulfillment === 'levantamento'
            ? 'Vamos avisá-lo(a) assim que estiver pronta para levantamento na loja.'
            : 'Vamos avisá-lo(a) assim que for enviada.') . '</p>'
        . '<p>Pode acompanhar o estado em <a href="' . e(Seo::abs('/encomendas/' . $orderId)) . '">' . e(Seo::abs('/encomendas/' . $orderId)) . '</a>.</p>'
    );

    flash('success', 'Encomenda #' . $orderId . ' confirmada! É paga na loja/entrega, conforme escolheu.');
    redirect('/encomendas/' . $orderId);
}

/**
 * Muda o estado de uma encomenda (gestão). Ao cancelar, repõe o stock dos
 * artigos; ao reverter um cancelamento, tenta voltar a reservar o stock —
 * e recusa a mudança se já não houver stock suficiente.
 */
function updateOrderStatus(int $orderId, string $newStatus): void
{
    $validStatuses = ['pendente', 'confirmada', 'pronta', 'enviada', 'concluida', 'cancelada'];
    if (!in_array($newStatus, $validStatuses, true)) {
        flash('error', 'Estado inválido.');
        redirect('/admin/encomendas/' . $orderId);
    }

    $pdo = Database::pdo();
    $pdo->beginTransaction();
    try {
        $encomenda = Database::one('SELECT * FROM orders WHERE id = ? FOR UPDATE', [$orderId]);
        if (!$encomenda) {
            $pdo->rollBack();
            flash('error', 'Encomenda não encontrada.');
            redirect('/admin/encomendas');
        }

        $oldStatus = $encomenda['status'];
        $itens = Database::all('SELECT * FROM order_items WHERE order_id = ?', [$orderId]);

        if ($newStatus === 'cancelada' && $oldStatus !== 'cancelada') {
            foreach ($itens as $it) {
                $pdo->prepare('UPDATE products SET stock = stock + ? WHERE id = ?')
                    ->execute([(int) $it['qty'], (int) $it['product_id']]);
            }
        } elseif ($oldStatus === 'cancelada' && $newStatus !== 'cancelada') {
            $ids = array_map(fn($it) => (int) $it['product_id'], $itens);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $rows = Database::all("SELECT * FROM products WHERE id IN ($placeholders) FOR UPDATE", $ids);
            $byId = [];
            foreach ($rows as $row) {
                $byId[(int) $row['id']] = $row;
            }
            foreach ($itens as $it) {
                $p = $byId[(int) $it['product_id']] ?? null;
                if (!$p || (int) $p['stock'] < (int) $it['qty']) {
                    $pdo->rollBack();
                    flash('error', 'Não é possível reverter o cancelamento: já não há stock suficiente de "' . ($p['name'] ?? $it['product_name']) . '".');
                    redirect('/admin/encomendas/' . $orderId);
                }
            }
            foreach ($itens as $it) {
                $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?')
                    ->execute([(int) $it['qty'], (int) $it['product_id']]);
            }
        }

        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$newStatus, $orderId]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        logError('updateOrderStatus', $e);
        flash('error', 'Não foi possível atualizar o estado da encomenda.');
        redirect('/admin/encomendas/' . $orderId);
    }

    flash('success', 'Estado da encomenda atualizado.');
    redirect('/admin/encomendas/' . $orderId);
}
