<?php /** Requer $p (array do produto). */ ?>
<a href="<?= e(url('/produto/' . $p['id'])) ?>" class="product-card">
    <div class="product-thumb">
        <?php if (!empty($p['image'])): ?>
            <img src="<?= e(uploadUrl($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        <?php else: ?>
            <div class="thumb-gen">
                <span class="thumb-gen-mark"><?= e($p['brand'] ?: 'inforocasião') ?></span>
                <div class="thumb-gen-fore">
                    <span class="thumb-gen-ico"><?= deviceIconSvg($p['category_name'] ?? '') ?></span>
                    <span class="thumb-gen-name"><?= e($p['name']) ?></span>
                </div>
            </div>
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
