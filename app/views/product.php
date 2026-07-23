<section class="container section">
    <nav class="breadcrumb">
        <a href="<?= e(url('/produtos')) ?>">Produtos</a>
        <?php if (!empty($produto['category_name'])): ?>
            <span>/</span>
            <a href="<?= e(url('/produtos?categoria=' . urlencode($produto['category_slug'] ?? ''))) ?>"><?= e($produto['category_name']) ?></a>
        <?php endif; ?>
        <span>/</span><span><?= e($produto['name']) ?></span>
    </nav>

    <div class="product-detail">
        <div class="detail-media">
            <?php if (!empty($produto['image'])): ?>
                <img src="<?= e(uploadUrl($produto['image'])) ?>" alt="<?= e($produto['name']) ?>">
            <?php else: ?>
                <div class="thumb-gen large">
                    <span class="thumb-gen-mark"><?= e($produto['brand'] ?: 'inforocasião') ?></span>
                    <div class="thumb-gen-fore">
                        <span class="thumb-gen-ico"><?= deviceIconSvg($produto['category_name'] ?? '') ?></span>
                        <span class="thumb-gen-name"><?= e($produto['name']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="detail-info">
            <?php if (!empty($produto['category_name'])): ?>
                <span class="product-cat"><?= e($produto['category_name']) ?></span>
            <?php endif; ?>
            <h1><?= e($produto['name']) ?></h1>
            <?php if (!empty($produto['brand'])): ?><p class="detail-brand"><?= e($produto['brand']) ?></p><?php endif; ?>

            <div class="detail-badges">
                <span class="badge"><?= e($produto['condition'] ?? 'Novo') ?></span>
                <?php if ((int)$produto['stock'] > 0): ?>
                    <span class="badge badge-ok">Disponível</span>
                <?php else: ?>
                    <span class="badge badge-out">Esgotado</span>
                <?php endif; ?>
            </div>

            <div class="detail-price"><?= money($produto['price']) ?></div>

            <?php if (!empty($produto['description'])): ?>
                <div class="detail-desc"><?= nl2br(e($produto['description'])) ?></div>
            <?php endif; ?>

            <a href="<?= e(url('/contactos')) ?>" class="btn btn-primary btn-lg">Contactar para comprar</a>
            <p class="muted small">Reserve ou peça mais informações — respondemos rapidamente.</p>
        </div>
    </div>
</section>
