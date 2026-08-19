<?php
$statusLabels = [
    'pendente'   => 'Pendente',
    'confirmada' => 'Confirmada',
    'pronta'     => 'Pronta para levantamento',
    'enviada'    => 'Enviada',
    'concluida'  => 'Concluída',
    'cancelada'  => 'Cancelada',
];
?>
<section class="container admin-section narrow">
    <a href="<?= e(url('/conta')) ?>" class="muted small">← Voltar à minha conta</a>
    <h1>Encomenda #<?= (int) $encomenda['id'] ?></h1>

    <div class="card">
        <h3 class="form-section-title">Detalhes</h3>
        <p><strong>Estado:</strong> <span class="tag tag-on"><?= e($statusLabels[$encomenda['status']] ?? $encomenda['status']) ?></span></p>
        <p><strong>Entrega:</strong> <?= $encomenda['fulfillment'] === 'envio' ? 'Envio' : 'Levantamento na loja (Cucujães)' ?></p>
        <?php if ($encomenda['fulfillment'] === 'envio'): ?>
            <p><strong>Morada:</strong><br>
                <?= e($encomenda['shipping_name']) ?><br>
                <?= e($encomenda['shipping_address']) ?><br>
                <?= e($encomenda['shipping_postal']) ?> <?= e($encomenda['shipping_city']) ?>
            </p>
        <?php endif; ?>
        <p><strong>Telefone:</strong> <?= e($encomenda['phone']) ?></p>
        <?php if (!empty($encomenda['notes'])): ?>
            <p><strong>Notas:</strong> <?= nl2br(e($encomenda['notes'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="card" style="margin-top:18px">
        <h3 class="form-section-title">Artigos</h3>
        <ul class="checkout-summary-list">
            <?php foreach ($itens as $it): ?>
                <li>
                    <span><?= (int) $it['qty'] ?>× <?= e($it['product_name']) ?></span>
                    <span><?= money((float) $it['unit_price'] * (int) $it['qty']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="checkout-summary-total"><span>Total</span><span><?= money($encomenda['total']) ?></span></div>
        <p class="muted small">Pagamento no levantamento ou na entrega.</p>
    </div>
</section>
