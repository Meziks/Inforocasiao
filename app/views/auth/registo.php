<section class="auth-wrap auth-wrap-page">
    <div class="auth-card">
        <div class="auth-brand"><img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($GLOBALS['config']['app']['name'] ?? 'Inforocasião') ?>" style="height:46px;width:auto"></div>
        <h1>Criar conta</h1>
        <p class="muted">Crie uma conta para poder fazer encomendas.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/registo')) ?>" class="form">
            <?= csrf_field() ?>
            <label>Nome
                <input type="text" name="name" required autofocus autocomplete="name" class="input" value="<?= e($_POST['name'] ?? '') ?>">
            </label>
            <label>Email
                <input type="email" name="email" required autocomplete="email" class="input" value="<?= e($_POST['email'] ?? '') ?>">
            </label>
            <label>Telemóvel <span class="muted small">(opcional)</span>
                <input type="tel" name="phone" autocomplete="tel" class="input" value="<?= e($_POST['phone'] ?? '') ?>">
            </label>
            <label>Password <span class="muted small">(mínimo 8 caracteres)</span>
                <input type="password" name="password" required minlength="8" autocomplete="new-password" class="input">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Criar conta</button>
        </form>
        <div class="auth-card-links">
            <a href="<?= e(url('/login')) ?>">Já tem conta? Entrar</a>
        </div>
        <a href="<?= e(url('/')) ?>" class="muted small back-link">← Voltar ao site</a>
    </div>
</section>
