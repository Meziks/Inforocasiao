<?php /** Requer $p (array do produto). */ ?>
<a href="<?= e(url('/produto/' . $p['id'])) ?>" class="product-card">
    <div class="product-thumb">
        <?php if (!empty($p['image'])): ?>
            <img src="<?= e(uploadUrl($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        <?php else: ?>
            <div class="thumb-placeholder"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.3 9 5.2M3.3 7.5 12 12.5l8.7-5M12 22V12.5"/><path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg></div>
        <?php endif; ?>
        <?php if (!empty($p['condition']) && $p['condition'] !== 'Novo'): ?>
            <span class="badge badge-condition"><?= e($p['condition']) ?></span>
        <?php endif; ?>
    </div>
    <div class="product-info">
        <?php if (!empty($p['category_name'])): ?>
            <span class="product-cat"><?= e($p['category_name']) ?></span>
        <?php endif; ?>
        <h3><?= e($p['name']) ?></h3>
        <?php if (!empty($p['brand'])): ?><span class="product-brand"><?= e($p['brand']) ?></span><?php endif; ?>
        <div class="product-foot">
            <span class="price"><?= money($p['price']) ?></span>
            <?php if ((int)($p['stock'] ?? 0) <= 0): ?>
                <span class="stock stock-out">Esgotado</span>
            <?php else: ?>
                <span class="stock stock-in">Disponível</span>
            <?php endif; ?>
        </div>
    </div>
</a>
