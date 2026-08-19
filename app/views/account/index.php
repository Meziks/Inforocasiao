<section class="container admin-section narrow">
    <div class="admin-head">
        <div>
            <h1>A minha conta</h1>
            <p class="muted">Bem-vindo(a), <?= e($cliente['name']) ?>.</p>
        </div>
        <form action="<?= e(url('/logout')) ?>" method="post" class="inline-form">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline btn-sm">Sair</button>
        </form>
    </div>

    <div class="card">
        <h3 class="form-section-title">Dados da conta</h3>
        <p><strong>Nome:</strong> <?= e($cliente['name']) ?></p>
        <p><strong>Email:</strong> <?= e($cliente['email']) ?></p>
    </div>

    <div class="card" style="margin-top:18px">
        <h3 class="form-section-title">As minhas encomendas</h3>
        <p class="muted">Ainda não é possível fazer encomendas online — esta parte está a ser preparada.</p>
    </div>
</section>
