<?php
$statusLabels = [
    'pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'pronta' => 'Pronta para levantamento',
    'enviada' => 'Enviada', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada',
];
?>
<section class="container admin-section narrow">
    <a href="<?= e(url('/admin/encomendas')) ?>" class="muted small">← Voltar às encomendas</a>
    <h1>Encomenda #<?= (int) $encomenda['id'] ?></h1>

    <div class="card">
        <h3 class="form-section-title">Cliente</h3>
        <p><?= e($encomenda['customer_name']) ?><br>
            <a href="mailto:<?= e($encomenda['customer_email']) ?>"><?= e($encomenda['customer_email']) ?></a><br>
            <?php if (!empty($encomenda['customer_phone'])): ?><?= e($encomenda['customer_phone']) ?><br><?php endif; ?>
        </p>

        <h3 class="form-section-title">Entrega</h3>
        <p><?= $encomenda['fulfillment'] === 'envio' ? 'Envio' : 'Levantamento na loja (Cucujães)' ?></p>
        <?php if ($encomenda['fulfillment'] === 'envio'): ?>
            <p>
                <?= e($encomenda['shipping_name']) ?><br>
                <?= e($encomenda['shipping_address']) ?><br>
                <?= e($encomenda['shipping_postal']) ?> <?= e($encomenda['shipping_city']) ?>
            </p>
        <?php endif; ?>
        <p><strong>Telefone de contacto:</strong> <?= e($encomenda['phone']) ?></p>
        <?php if (!empty($encomenda['notes'])): ?>
            <p><strong>Notas:</strong> <?= nl2br(e($encomenda['notes'])) ?></p>
        <?php endif; ?>

        <h3 class="form-section-title">Estado</h3>
        <form method="post" action="<?= e(url('/admin/encomendas/' . $encomenda['id'])) ?>" class="form-row" style="align-items:end">
            <?= csrf_field() ?>
            <label>Estado atual
                <select name="status" class="input">
                    <?php foreach ($statusLabels as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $encomenda['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="btn btn-primary">Atualizar estado</button>
        </form>
        <?php if ($encomenda['status'] !== 'cancelada'): ?>
            <p class="muted small" style="margin-top:10px">Ao cancelar, o stock dos artigos é reposto automaticamente.</p>
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
    </div>
</section>
