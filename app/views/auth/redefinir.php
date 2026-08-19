<section class="auth-wrap auth-wrap-page">
    <div class="auth-card">
        <div class="auth-brand"><img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($GLOBALS['config']['app']['name'] ?? 'Inforocasião') ?>" style="height:46px;width:auto"></div>
        <h1>Nova password</h1>
        <p class="muted">Defina a nova password da sua conta.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/redefinir-password/' . $token)) ?>" class="form">
            <?= csrf_field() ?>
            <label>Nova password <span class="muted small">(mínimo 8 caracteres)</span>
                <input type="password" name="password" required minlength="8" autofocus autocomplete="new-password" class="input">
            </label>
            <button type="submit" class="btn btn-primary btn-block">Alterar password</button>
        </form>
    </div>
</section>
