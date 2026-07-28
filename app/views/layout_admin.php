<?php $appName = $GLOBALS['config']['app']['name'] ?? 'Inforocasião'; $isAuth = Auth::check(); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ? "$title · Gestão" : "Gestão") ?> · <?= e($appName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body class="admin">
<?php if ($isAuth): ?>
<header class="admin-header">
    <div class="container header-inner">
        <a href="<?= e(url('/admin/dashboard')) ?>" class="brand">
            <img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($appName) ?>" class="brand-logo">
            <span class="brand-text">Gestão</span>
        </a>
        <button class="nav-toggle" type="button" aria-label="Abrir menu" aria-controls="admin-nav" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>
        <nav class="main-nav" id="admin-nav">
            <a href="<?= e(url('/')) ?>" target="_blank">Ver site ↗</a>
            <form action="<?= e(url('/admin/logout')) ?>" method="post" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-ghost btn-sm">Sair</button>
            </form>
        </nav>
    </div>
</header>
<?php endif; ?>

<main class="admin-main">
    <?php if ($msg = flash('success')): ?>
        <div class="container"><div class="alert alert-success"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="container"><div class="alert alert-error"><?= e($msg) ?></div></div>
    <?php endif; ?>
    <?= $content ?>
</main>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
