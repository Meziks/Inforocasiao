<?php
$statusLabels = [
    'pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'pronta' => 'Pronta',
    'enviada' => 'Enviada', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada',
];
$statusTagClass = [
    'pendente' => 'tag-off', 'confirmada' => 'tag-on', 'pronta' => 'tag-on',
    'enviada' => 'tag-on', 'concluida' => 'tag-star', 'cancelada' => 'tag-off',
];
?>
<section class="container admin-section">
    <div class="admin-head">
        <div>
            <h1>Encomendas</h1>
            <p class="muted"><?= count($encomendas) ?> encomenda(s)<?= $estadoFiltro !== '' ? ' — ' . e($statusLabels[$estadoFiltro] ?? $estadoFiltro) : '' ?>.</p>
        </div>
    </div>

    <form method="get" action="<?= e(url('/admin/encomendas')) ?>" class="filters admin-filters">
        <select name="estado" class="input" onchange="this.form.submit()">
            <option value="">Todos os estados</option>
            <?php foreach ($statusLabels as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= $estadoFiltro === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($estadoFiltro !== ''): ?>
            <a href="<?= e(url('/admin/encomendas')) ?>" class="btn btn-ghost">Limpar</a>
        <?php endif; ?>
    </form>

    <?php if (empty($encomendas)): ?>
        <div class="empty-state card">
            <p>Não há encomendas<?= $estadoFiltro !== '' ? ' com este estado' : '' ?>.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Cliente</th><th>Entrega</th><th>Total</th><th>Estado</th><th>Data</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($encomendas as $enc): ?>
                        <tr>
                            <td><a href="<?= e(url('/admin/encomendas/' . $enc['id'])) ?>" class="row-title">#<?= (int) $enc['id'] ?></a></td>
                            <td><?= e($enc['customer_name']) ?><br><span class="muted small"><?= e($enc['customer_email']) ?></span></td>
                            <td><?= $enc['fulfillment'] === 'envio' ? 'Envio' : 'Levantamento' ?></td>
                            <td><?= money($enc['total']) ?></td>
                            <td><span class="tag <?= $statusTagClass[$enc['status']] ?? '' ?>"><?= e($statusLabels[$enc['status']] ?? $enc['status']) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($enc['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-cards">
            <?php foreach ($encomendas as $enc): ?>
                <a href="<?= e(url('/admin/encomendas/' . $enc['id'])) ?>" class="admin-card order-admin-card">
                    <div class="admin-card-body" style="grid-column: 1 / -1">
                        <span class="row-title">Encomenda #<?= (int) $enc['id'] ?></span>
                        <p class="muted small admin-card-sub"><?= e($enc['customer_name']) ?> · <?= date('d/m/Y', strtotime($enc['created_at'])) ?></p>
                        <div class="admin-card-meta">
                            <span class="admin-card-price"><?= money($enc['total']) ?></span>
                            <span><?= $enc['fulfillment'] === 'envio' ? 'Envio' : 'Levantamento' ?></span>
                            <span class="tag <?= $statusTagClass[$enc['status']] ?? '' ?>"><?= e($statusLabels[$enc['status']] ?? $enc['status']) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
