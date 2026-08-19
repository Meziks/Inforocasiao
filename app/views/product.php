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
        <?php $galeria = $galeria ?? []; ?>
        <div class="detail-media" id="product-gallery">
            <?php if (!empty($galeria)): ?>
                <div class="detail-media-main">
                    <button type="button" class="gallery-zoom" aria-label="Ver imagem em tamanho grande">
                        <img id="gallery-main" src="<?= e(uploadUrl($galeria[0])) ?>" alt="<?= e($produto['name']) ?>">
                        <span class="gallery-zoom-hint">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M11 8v6M8 11h6"/></svg>
                            Ampliar
                        </span>
                    </button>
                </div>
                <?php if (count($galeria) > 1): ?>
                    <div class="detail-thumbs">
                        <?php foreach ($galeria as $i => $img): ?>
                            <button type="button" class="detail-thumb <?= $i === 0 ? 'active' : '' ?>"
                                    data-src="<?= e(uploadUrl($img)) ?>" data-index="<?= $i ?>" aria-label="Ver imagem <?= $i + 1 ?>">
                                <img src="<?= e(uploadUrl($img)) ?>" alt="">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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

            <div class="detail-actions">
                <?php if ((int) $produto['stock'] > 0): ?>
                    <form method="post" action="<?= e(url('/carrinho/adicionar')) ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $produto['id'] ?>">
                        <input type="hidden" name="redirect" value="<?= e(url('/produto/' . $produto['id'])) ?>">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            Adicionar ao carrinho
                        </button>
                    </form>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-lg" disabled>Esgotado</button>
                <?php endif; ?>
                <a class="btn btn-whatsapp btn-lg"
                   href="https://wa.me/351912138094?text=<?= rawurlencode('Olá! Tenho interesse no artigo "' . $produto['name'] . '" (' . money($produto['price']) . '). Podem dar-me mais informações?') ?>"
                   target="_blank" rel="noopener">
                    <svg viewBox="0 0 32 32" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M16.04 3C9.4 3 4 8.4 4 15.04c0 2.12.56 4.19 1.62 6.02L4 29l8.13-1.58a12 12 0 0 0 3.9.65h.01c6.64 0 12.04-5.4 12.04-12.03C28.08 8.4 22.68 3 16.04 3Zm0 21.9h-.01c-1.2 0-2.38-.32-3.4-.93l-.24-.14-4.82.94.96-4.7-.16-.25a9.9 9.9 0 0 1-1.52-5.27c0-5.48 4.46-9.94 9.95-9.94 2.66 0 5.15 1.04 7.03 2.92a9.87 9.87 0 0 1 2.91 7.03c0 5.48-4.46 9.94-9.94 9.94Zm5.46-7.44c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.8.37-.27.3-1.05 1.02-1.05 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35Z"/></svg>
                    WhatsApp
                </a>
            </div>
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

    <?php if (!empty($galeria)): ?>
        <div class="lightbox" id="lightbox" aria-hidden="true">
            <button type="button" class="lightbox-close" aria-label="Fechar">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
            <?php if (count($galeria) > 1): ?>
                <button type="button" class="lightbox-nav lightbox-prev" aria-label="Imagem anterior">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
            <?php endif; ?>
            <img id="lightbox-img" src="" alt="<?= e($produto['name']) ?>">
            <?php if (count($galeria) > 1): ?>
                <button type="button" class="lightbox-nav lightbox-next" aria-label="Próxima imagem">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <span class="lightbox-count" id="lightbox-count"></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
