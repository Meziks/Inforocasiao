<?php
$appName = $GLOBALS['config']['app']['name'] ?? 'Inforocasião';
$seo         = seo();
$metaTitle   = $title ? "$title — $appName" : "$appName — Informática, Telemóveis e Reparações em Cucujães";
$metaDesc    = $seo['description'] ?? Seo::DESCRIPTION;
$canonical   = Seo::abs($seo['canonical'] ?? ($_SERVER['REQUEST_URI'] ?? '/'));
// canonical sem query string, exceto filtros de produtos
$canonical   = strtok($canonical, '?') ?: $canonical;
$ogImage     = Seo::abs('assets/img/og-image.png');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle) ?></title>
    <meta name="description" content="<?= e($metaDesc) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#e2001a">
    <meta name="geo.region" content="PT-01">
    <meta name="geo.placename" content="Cucujães">

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($appName) ?>">
    <meta property="og:locale" content="pt_PT">
    <meta property="og:title" content="<?= e($metaTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDesc) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">

    <!-- Dados estruturados (Schema.org) -->
    <?= Seo::jsonLdTag(Seo::businessJsonLd()) ?>
    <?= Seo::jsonLdTag(Seo::websiteJsonLd()) ?>
    <?php foreach (($seo['jsonld'] ?? []) as $block): ?>
        <?= Seo::jsonLdTag($block) ?>
    <?php endforeach; ?>
</head>
<body>
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <span class="topbar-item">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                Cucujães
            </span>
            <a class="topbar-item" href="tel:+351912138094">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.4-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2Z"/></svg>
                912 138 094
            </a>
            <span class="topbar-item topbar-hours">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                Seg–Sex 09h30–19h00
            </span>
        </div>
        <div class="topbar-social">
            <a href="https://www.facebook.com/100017988694141/" target="_blank" rel="noopener" aria-label="Facebook">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.25-1.5 1.55-1.5H17V4.6c-.3 0-1.3-.1-2.45-.1-2.42 0-4.05 1.48-4.05 4.2v2.2H7.7V14h2.8v8h3z"/></svg>
            </a>
            <a href="https://www.instagram.com/inforocasiao.vendas/" target="_blank" rel="noopener" aria-label="Instagram">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M12 2.2c3.2 0 3.6 0 4.85.07 1.17.05 1.8.25 2.23.42.56.22.96.48 1.38.9.42.42.68.82.9 1.38.17.42.37 1.06.42 2.23.06 1.27.07 1.65.07 4.85s0 3.58-.07 4.85c-.05 1.17-.25 1.8-.42 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.17-1.06.37-2.23.42-1.27.06-1.65.07-4.85.07s-3.58 0-4.85-.07c-1.17-.05-1.8-.25-2.23-.42a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.17-.42-.37-1.06-.42-2.23C2.21 15.58 2.2 15.2 2.2 12s0-3.58.07-4.85c.05-1.17.25-1.8.42-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.17 1.06-.37 2.23-.42C8.42 2.21 8.8 2.2 12 2.2Zm0 3.05A6.75 6.75 0 1 0 18.75 12 6.75 6.75 0 0 0 12 5.25Zm0 1.8A4.95 4.95 0 1 1 7.05 12 4.95 4.95 0 0 1 12 7.05Zm5.15-.9a1.15 1.15 0 1 1-2.3 0 1.15 1.15 0 0 1 2.3 0Z"/></svg>
            </a>
        </div>
    </div>
</div>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= e(url('/')) ?>" class="brand">
            <img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($appName) ?>" class="brand-logo">
        </a>
        <button class="nav-toggle" type="button" aria-label="Abrir menu" aria-controls="main-nav" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>
        <nav class="main-nav" id="main-nav">
            <a href="<?= e(url('/')) ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10.5 9-7 9 7"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/></svg>
                Início
            </a>
            <a href="<?= e(url('/produtos')) ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                Produtos
            </a>
            <a href="<?= e(url('/servicos')) ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4l-6 6a1.4 1.4 0 0 0 2 2l6-6a4 4 0 0 0 5.4-5.4l-2.6 2.6-2-2 2.6-2.6Z"/></svg>
                Reparações
            </a>
            <a href="<?= e(url('/contactos')) ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.4-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2Z"/></svg>
                Contactos
            </a>
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
            <div class="footer-social">
                <a href="https://www.facebook.com/100017988694141/" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M13.5 22v-8h2.7l.4-3.1h-3.1V8.9c0-.9.25-1.5 1.55-1.5H17V4.6c-.3 0-1.3-.1-2.45-.1-2.42 0-4.05 1.48-4.05 4.2v2.2H7.7V14h2.8v8h3z"/></svg>
                </a>
                <a href="https://www.instagram.com/inforocasiao.vendas/" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 2.2c3.2 0 3.6 0 4.85.07 1.17.05 1.8.25 2.23.42.56.22.96.48 1.38.9.42.42.68.82.9 1.38.17.42.37 1.06.42 2.23.06 1.27.07 1.65.07 4.85s0 3.58-.07 4.85c-.05 1.17-.25 1.8-.42 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.17-1.06.37-2.23.42-1.27.06-1.65.07-4.85.07s-3.58 0-4.85-.07c-1.17-.05-1.8-.25-2.23-.42a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.17-.42-.37-1.06-.42-2.23C2.21 15.58 2.2 15.2 2.2 12s0-3.58.07-4.85c.05-1.17.25-1.8.42-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.17 1.06-.37 2.23-.42C8.42 2.21 8.8 2.2 12 2.2Zm0 1.8c-3.14 0-3.5 0-4.74.07-.9.04-1.38.19-1.7.32-.43.16-.74.36-1.06.68-.32.32-.52.63-.68 1.06-.13.32-.28.8-.32 1.7C3.2 8.5 3.2 8.86 3.2 12s0 3.5.07 4.74c.04.9.19 1.38.32 1.7.16.43.36.74.68 1.06.32.32.63.52 1.06.68.32.13.8.28 1.7.32C8.5 20.8 8.86 20.8 12 20.8s3.5 0 4.74-.07c.9-.04 1.38-.19 1.7-.32.43-.16.74-.36 1.06-.68.32-.32.52-.63.68-1.06.13-.32.28-.8.32-1.7.07-1.24.07-1.6.07-4.74s0-3.5-.07-4.74c-.04-.9-.19-1.38-.32-1.7a2.86 2.86 0 0 0-.68-1.06 2.86 2.86 0 0 0-1.06-.68c-.32-.13-.8-.28-1.7-.32C15.5 4 15.14 4 12 4Zm0 3.05A4.95 4.95 0 1 1 12 17a4.95 4.95 0 0 1 0-9.9Zm0 1.8a3.15 3.15 0 1 0 0 6.3 3.15 3.15 0 0 0 0-6.3Zm5.15-.9a1.15 1.15 0 1 1-2.3 0 1.15 1.15 0 0 1 2.3 0Z"/></svg>
                </a>
            </div>
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
<!-- Botão flutuante de WhatsApp (contacto rápido) -->
<a class="whatsapp-fab"
   href="https://wa.me/351912138094?text=<?= rawurlencode('Olá! Vim através do site e gostava de mais informações.') ?>"
   target="_blank" rel="noopener" aria-label="Falar connosco no WhatsApp">
    <svg viewBox="0 0 32 32" width="30" height="30" fill="currentColor" aria-hidden="true">
        <path d="M16.04 3C9.4 3 4 8.4 4 15.04c0 2.12.56 4.19 1.62 6.02L4 29l8.13-1.58a12 12 0 0 0 3.9.65h.01c6.64 0 12.04-5.4 12.04-12.03C28.08 8.4 22.68 3 16.04 3Zm0 21.9h-.01c-1.2 0-2.38-.32-3.4-.93l-.24-.14-4.82.94.96-4.7-.16-.25a9.9 9.9 0 0 1-1.52-5.27c0-5.48 4.46-9.94 9.95-9.94 2.66 0 5.15 1.04 7.03 2.92a9.87 9.87 0 0 1 2.91 7.03c0 5.48-4.46 9.94-9.94 9.94Zm5.46-7.44c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.8.37-.27.3-1.05 1.02-1.05 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35Z"/>
    </svg>
</a>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
