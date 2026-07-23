<?php
$rev    = Reviews::data();
$rating = (float) $rev['rating'];
$total  = (int) $rev['total'];
$pct    = max(0, min(100, $rating / 5 * 100));
$gLogo  = '<svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true">'
    . '<path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>'
    . '<path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>'
    . '<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>'
    . '<path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>';
?>
<section class="section reviews-section">
    <div class="container">
        <div class="section-head">
            <div><p class="eyebrow">Avaliações</p><h2>O que dizem os nossos clientes</h2></div>
        </div>
        <div class="reviews-wrap">
            <aside class="review-summary">
                <div class="rev-google"><?= $gLogo ?><span>Avaliações Google</span></div>
                <div class="rev-score"><?= number_format($rating, 1, ',', '.') ?></div>
                <span class="stars" style="--rate:<?= $pct ?>%">★★★★★</span>
                <p class="rev-total"><strong><?= $total ?></strong> avaliações</p>
                <a href="<?= e($rev['url']) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Ver no Google</a>
            </aside>

            <?php if (!empty($rev['reviews'])): ?>
                <div class="review-cards">
                    <?php foreach (array_slice($rev['reviews'], 0, 4) as $r): ?>
                        <article class="review-card">
                            <header class="review-card-head">
                                <?php if (!empty($r['photo'])): ?>
                                    <img class="rev-avatar" src="<?= e($r['photo']) ?>" alt="" referrerpolicy="no-referrer" loading="lazy">
                                <?php else: ?>
                                    <span class="rev-avatar rev-avatar-ph"><?= e(mb_substr($r['author'] ?: '?', 0, 1)) ?></span>
                                <?php endif; ?>
                                <div class="rev-meta">
                                    <strong><?= e($r['author']) ?></strong>
                                    <span class="stars stars-sm" style="--rate:<?= max(0, min(100, $r['rating'] / 5 * 100)) ?>%">★★★★★</span>
                                </div>
                                <span class="rev-g"><?= $gLogo ?></span>
                            </header>
                            <?php if (!empty($r['text'])): ?>
                                <p class="rev-text"><?= e(mb_strimwidth($r['text'], 0, 230, '…')) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($r['when'])): ?>
                                <time class="rev-when"><?= e($r['when']) ?></time>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="review-empty">
                    <p>Clientes satisfeitos por toda a região. Veja as avaliações reais — e deixe a sua — na nossa página do Google.</p>
                    <a href="<?= e($rev['url']) ?>" target="_blank" rel="noopener" class="btn btn-primary">Ler as avaliações no Google</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
