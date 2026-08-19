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
        <?php if (empty($encomendas)): ?>
            <p class="muted">Ainda não fez nenhuma encomenda. <a href="<?= e(url('/produtos')) ?>">Ver produtos</a></p>
        <?php else: ?>
            <?php $statusLabels = [
                'pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'pronta' => 'Pronta',
                'enviada' => 'Enviada', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada',
            ]; ?>
            <div class="cart-list">
                <?php foreach ($encomendas as $enc): ?>
                    <a href="<?= e(url('/encomendas/' . $enc['id'])) ?>" class="cart-row order-row">
                        <div class="cart-row-info">
                            <span class="row-title">Encomenda #<?= (int) $enc['id'] ?></span>
                            <p class="cart-row-price"><?= date('d/m/Y', strtotime($enc['created_at'])) ?> · <?= $enc['fulfillment'] === 'envio' ? 'Envio' : 'Levantamento' ?></p>
                        </div>
                        <div class="cart-row-actions">
                            <span class="tag tag-on"><?= e($statusLabels[$enc['status']] ?? $enc['status']) ?></span>
                            <span class="cart-row-subtotal"><?= money($enc['total']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
