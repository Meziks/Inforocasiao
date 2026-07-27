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

            <?php if (!empty($produto['image2']) || !empty($produto['image3']) || !empty($produto['image4'])): ?>
                <div class="detail-thumbnails">
                    <?php if (!empty($produto['image2'])): ?>
                        <img src="<?= e(uploadUrl($produto['image2'])) ?>" alt="<?= e($produto['name']) ?> - 2">
                    <?php endif; ?>
                    <?php if (!empty($produto['image3'])): ?>
                        <img src="<?= e(uploadUrl($produto['image3'])) ?>" alt="<?= e($produto['name']) ?> - 3">
                    <?php endif; ?>
                    <?php if (!empty($produto['image4'])): ?>
                        <img src="<?= e(uploadUrl($produto['image4'])) ?>" alt="<?= e($produto['name']) ?> - 4">
                    <?php endif; ?>
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
            <p class="muted small">Reserve ou peça mais informações. Respondemos rapidamente.</p>

            <ul class="detail-fulfil">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/></svg>
                    <span><strong>Levantamento grátis na loja</strong> em Cucujães</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg>
                    <span><strong>Envio para todo o país</strong> · portes calculados conforme o artigo</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/></svg>
                    <span>Devolução até <?= Seo::RETURN_DAYS ?> dias · Garantia incluída</span>
                </li>
            </ul>
        </div>
    </div>
</section>
