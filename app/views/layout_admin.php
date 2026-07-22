<?php $appName = $GLOBALS['config']['app']['name'] ?? 'Inforocasião'; $isAuth = Auth::check(); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ? "$title — Gestão" : "Gestão") ?> · <?= e($appName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body class="admin">
<?php if ($isAuth): ?>
<header class="admin-header">
    <div class="container header-inner">
        <a href="<?= e(url('/admin/dashboard')) ?>" class="brand">
            <span class="brand-mark">IO</span>
            <span class="brand-text">Gestão · <?= e($appName) ?></span>
        </a>
        <nav class="main-nav">
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
