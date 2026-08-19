<section class="container admin-section narrow">
    <h1>Checkout</h1>

    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
        <form method="post" action="<?= e(url('/checkout')) ?>" class="form card checkout-form">
            <?= csrf_field() ?>
            <div class="form-section">
                <h3 class="form-section-title">Entrega</h3>
                <div class="form-checks">
                    <label class="check"><input type="radio" name="fulfillment" value="levantamento" checked> Levantamento na loja (Cucujães) — grátis</label>
                    <label class="check"><input type="radio" name="fulfillment" value="envio"> Envio para morada — Portugal continental</label>
                </div>
            </div>
            <div class="form-section" id="shipping-fields">
                <h3 class="form-section-title">Morada de envio</h3>
                <label>Nome para entrega
                    <input type="text" name="shipping_name" class="input" value="<?= e($cliente['name'] ?? '') ?>">
                </label>
                <label>Morada
                    <input type="text" name="shipping_address" class="input" placeholder="Rua, número, andar…">
                </label>
                <div class="form-row">
                    <label>Código postal
                        <input type="text" name="shipping_postal" class="input" placeholder="0000-000">
                    </label>
                    <label>Localidade
                        <input type="text" name="shipping_city" class="input">
                    </label>
                </div>
            </div>
            <div class="form-section form-section-last">
                <h3 class="form-section-title">Contacto</h3>
                <label>Telefone
                    <input type="tel" name="phone" required class="input">
                </label>
                <label>Notas <span class="muted small">(opcional)</span>
                    <textarea name="notes" rows="3" class="input" placeholder="Alguma indicação especial para a entrega?"></textarea>
                </label>
            </div>
            <div class="form-actions form-actions-sticky">
                <button type="submit" class="btn btn-primary">Confirmar encomenda</button>
                <a href="<?= e(url('/carrinho')) ?>" class="btn btn-ghost">Voltar ao carrinho</a>
            </div>
        </form>

        <aside class="checkout-summary card">
            <h3 class="form-section-title">Resumo</h3>
            <ul class="checkout-summary-list">
                <?php foreach ($itens as $item): ?>
                    <li>
                        <span><?= (int) $item['qty'] ?>× <?= e($item['product']['name']) ?></span>
                        <span><?= money($item['subtotal']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="checkout-summary-total"><span>Total</span><span><?= money($total) ?></span></div>
            <p class="muted small">Pagamento no levantamento ou na entrega — ainda não há pagamento online.</p>
        </aside>
    </div>
</section>
