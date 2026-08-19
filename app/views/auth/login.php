<section class="auth-wrap auth-wrap-page">
    <div class="auth-card">
        <div class="auth-brand"><img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($GLOBALS['config']['app']['name'] ?? 'Inforocasião') ?>" style="height:46px;width:auto"></div>
        <h1>Entrar</h1>
        <p class="muted">Entre na sua conta para ver as suas encomendas.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/login')) ?>" class="form">
            <?= csrf_field() ?>
            <label>Email
                <input type="email" name="email" required autofocus autocomplete="email" class="input">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password" class="input">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
        <div class="auth-card-links">
            <a href="<?= e(url('/recuperar-password')) ?>">Esqueceu-se da password?</a>
            <a href="<?= e(url('/registo')) ?>">Ainda não tem conta? Criar conta</a>
        </div>
        <a href="<?= e(url('/')) ?>" class="muted small back-link">← Voltar ao site</a>
    </div>
</section>
