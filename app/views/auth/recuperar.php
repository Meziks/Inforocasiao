<section class="auth-wrap auth-wrap-page">
    <div class="auth-card">
        <div class="auth-brand"><img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($GLOBALS['config']['app']['name'] ?? 'Inforocasião') ?>" style="height:46px;width:auto"></div>
        <h1>Recuperar password</h1>
        <p class="muted">Indique o email da sua conta e enviamos um link para definir uma nova password.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/recuperar-password')) ?>" class="form">
            <?= csrf_field() ?>
            <label>Email
                <input type="email" name="email" required autofocus autocomplete="email" class="input">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Enviar link</button>
        </form>
        <div class="auth-card-links">
            <a href="<?= e(url('/login')) ?>">← Voltar ao login</a>
        </div>
    </div>
</section>
