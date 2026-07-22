<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <p class="eyebrow">Informática · Telemóveis · Reparações</p>
            <h1>Aparelhos eletrónicos novos e <span>recondicionados</span>.</h1>
            <p class="lead">Na infor ocasião encontra computadores, telemóveis e componentes — e um serviço de reparação eletrónica com garantia de qualidade.</p>
            <div class="hero-actions">
                <a href="<?= e(url('/produtos')) ?>" class="btn btn-primary">Ver produtos</a>
                <a href="<?= e(url('/servicos')) ?>" class="btn btn-outline">Preciso de reparar</a>
            </div>
        </div>
        <div class="hero-card">
            <ul class="hero-highlights">
                <li><strong>+ Recondicionados</strong><span>Testados e com garantia</span></li>
                <li><strong>Reparações</strong><span>Telemóveis e computadores</span></li>
                <li><strong>Aconselhamento</strong><span>Atendimento personalizado</span></li>
            </ul>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <h2>Categorias</h2>
        <a href="<?= e(url('/produtos')) ?>" class="link-more">Ver tudo →</a>
    </div>
    <div class="category-grid">
        <a href="<?= e(url('/produtos?categoria=computadores')) ?>" class="cat-card"><span class="cat-ico">💻</span>Computadores</a>
        <a href="<?= e(url('/produtos?categoria=portateis')) ?>" class="cat-card"><span class="cat-ico">🖥️</span>Portáteis</a>
        <a href="<?= e(url('/produtos?categoria=telemoveis')) ?>" class="cat-card"><span class="cat-ico">📱</span>Telemóveis</a>
        <a href="<?= e(url('/produtos?categoria=componentes')) ?>" class="cat-card"><span class="cat-ico">🔧</span>Componentes</a>
        <a href="<?= e(url('/produtos?categoria=acessorios')) ?>" class="cat-card"><span class="cat-ico">🎧</span>Acessórios</a>
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <h2>Em destaque</h2>
        <a href="<?= e(url('/produtos')) ?>" class="link-more">Ver todos os produtos →</a>
    </div>
    <?php if (empty($destaques)): ?>
        <p class="muted">Ainda não há produtos em destaque. Adicione artigos na área de gestão e marque-os como "destaque".</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($destaques as $p): ?>
                <?php require BASE_PATH . '/app/views/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <h2>O seu equipamento avariou?</h2>
            <p>Fazemos diagnóstico e orçamento sem compromisso.</p>
        </div>
        <a href="<?= e(url('/contactos')) ?>" class="btn btn-light">Falar connosco</a>
    </div>
</section>
