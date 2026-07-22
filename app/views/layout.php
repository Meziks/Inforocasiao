<?php $appName = $GLOBALS['config']['app']['name'] ?? 'Inforocasião'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ? "$title — $appName" : "$appName — Informática, Telemóveis e Reparações") ?></title>
    <meta name="description" content="<?= e($appName) ?> — Venda de computadores, telemóveis e componentes electrónicos, novos e recondicionados. Serviço de reparações.">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= e(url('/')) ?>" class="brand">
            <img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($appName) ?>" class="brand-logo">
        </a>
        <nav class="main-nav">
            <a href="<?= e(url('/')) ?>">Início</a>
            <a href="<?= e(url('/produtos')) ?>">Produtos</a>
            <a href="<?= e(url('/servicos')) ?>">Reparações</a>
            <a href="<?= e(url('/contactos')) ?>">Contactos</a>
        </nav>
    </div>
</header>

<main>
    <?php if ($msg = flash('success')): ?>
        <div class="container"><div class="alert alert-success"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h4><?= e($appName) ?></h4>
            <p>Aparelhos eletrónicos novos e recondicionados. Serviços de reparação eletrónica com garantia de qualidade.</p>
        </div>
        <div>
            <h4>Contactos</h4>
            <p>Rua do Clube Desportivo de Cucujães, 275<br>3720-385 Cucujães</p>
            <a href="tel:+351912138094">912 138 094</a>
            <a href="tel:+351256842306">256 842 306</a>
        </div>
        <div>
            <h4>Navegação</h4>
            <a href="<?= e(url('/produtos')) ?>">Produtos</a>
            <a href="<?= e(url('/servicos')) ?>">Reparações</a>
            <a href="<?= e(url('/contactos')) ?>">Contactos</a>
            <a href="<?= e(url('/admin')) ?>">Gestão da loja</a>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">© <?= date('Y') ?> <?= e($appName) ?>. Todos os direitos reservados.</div>
    </div>
</footer>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
