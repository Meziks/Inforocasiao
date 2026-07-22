<section class="auth-wrap">
    <div class="auth-card">
        <div class="auth-brand"><span class="brand-mark">IO</span></div>
        <h1>Gestão da loja</h1>
        <p class="muted">Introduza as suas credenciais para continuar.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/admin/login')) ?>" class="form">
            <?= csrf_field() ?>
            <label>Utilizador
                <input type="text" name="username" required autofocus autocomplete="username" class="input">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password" class="input">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
        <a href="<?= e(url('/')) ?>" class="muted small back-link">← Voltar ao site</a>
    </div>
</section>
