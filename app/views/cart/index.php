<section class="container admin-section">
    <div class="admin-head">
        <div>
            <h1>Carrinho</h1>
        </div>
    </div>

    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <?php if (!$itens): ?>
        <div class="empty-state card">
            <p>O seu carrinho está vazio.</p>
            <a href="<?= e(url('/produtos')) ?>" class="btn btn-primary">Ver produtos</a>
        </div>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($itens as $item): $p = $item['product']; $overStock = $item['qty'] > (int) $p['stock']; ?>
                <div class="cart-row">
                    <div class="cart-row-thumb">
                        <img src="<?= e(uploadUrl($p['image'])) ?>" alt="">
                    </div>
                    <div class="cart-row-info">
                        <a href="<?= e(url('/produto/' . $p['id'])) ?>" class="row-title"><?= e($p['name']) ?></a>
                        <p class="cart-row-price"><?= money($p['price']) ?> cada</p>
                        <?php if ($overStock): ?>
                            <p class="image-manager-warning">Só há <?= (int) $p['stock'] ?> em stock — ajuste a quantidade.</p>
                        <?php endif; ?>
                    </div>
                    <div class="cart-row-actions">
                        <form method="post" action="<?= e(url('/carrinho/atualizar')) ?>" class="cart-qty-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                            <input type="number" name="qty" value="<?= (int) $item['qty'] ?>" min="1" max="<?= (int) $p['stock'] ?>" class="input input-qty">
                            <button type="submit" class="btn btn-outline btn-sm">Atualizar</button>
                        </form>
                        <span class="cart-row-subtotal"><?= money($item['subtotal']) ?></span>
                        <form method="post" action="<?= e(url('/carrinho/remover')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <span class="cart-summary-total">Total: <?= money($total) ?></span>
            <a href="<?= e(url('/checkout')) ?>" class="btn btn-primary btn-lg">Finalizar compra</a>
        </div>
    <?php endif; ?>
</section>
