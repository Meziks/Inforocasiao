<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <p class="eyebrow">Informática · Telemóveis · Reparações</p>
            <h1>Aparelhos eletrónicos novos e <span>recondicionados</span>.</h1>
            <p class="lead">Na infor ocasião encontra computadores, telemóveis e componentes — e um serviço de reparação eletrónica com garantia de qualidade.</p>
            <div class="hero-actions">
                <a href="<?= e(url('/produtos')) ?>" class="btn btn-primary btn-lg">Ver produtos</a>
                <a href="<?= e(url('/servicos')) ?>" class="btn btn-outline btn-lg" style="background:transparent;color:#fff;border-color:rgba(255,255,255,.3)">Preciso de reparar</a>
            </div>
        </div>
        <div class="hero-card">
            <ul class="hero-highlights">
                <li>
                    <span class="hl-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span>
                    <div><strong>Recondicionados com garantia</strong><span>Testados e prontos a usar</span></div>
                </li>
                <li>
                    <span class="hl-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4l-6 6a1.4 1.4 0 0 0 2 2l6-6a4 4 0 0 0 5.4-5.4l-2.6 2.6-2-2 2.6-2.6Z"/></svg></span>
                    <div><strong>Reparação rápida</strong><span>Telemóveis e computadores</span></div>
                </li>
                <li>
                    <span class="hl-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg></span>
                    <div><strong>Aconselhamento</strong><span>Atendimento próximo e honesto</span></div>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="feature-strip">
    <div class="container feature-grid">
        <div class="feature-item">
            <span class="feature-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span>
            <div><strong>Garantia incluída</strong><span>Em todos os equipamentos</span></div>
        </div>
        <div class="feature-item">
            <span class="feature-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/></svg></span>
            <div><strong>Recondicionados testados</strong><span>Qualidade verificada</span></div>
        </div>
        <div class="feature-item">
            <span class="feature-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4l-6 6a1.4 1.4 0 0 0 2 2l6-6a4 4 0 0 0 5.4-5.4l-2.6 2.6-2-2 2.6-2.6Z"/></svg></span>
            <div><strong>Reparações rápidas</strong><span>Diagnóstico sem compromisso</span></div>
        </div>
        <div class="feature-item">
            <span class="feature-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3Z"/></svg></span>
            <div><strong>Aconselhamento</strong><span>Atendimento personalizado</span></div>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <div><h2>Categorias</h2><p class="sub">Encontre o que procura por área</p></div>
        <a href="<?= e(url('/produtos')) ?>" class="link-more">Ver tudo →</a>
    </div>
    <div class="category-grid">
        <a href="<?= e(url('/produtos?categoria=computadores')) ?>" class="cat-card">
            <span class="cat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></span>
            Computadores
        </a>
        <a href="<?= e(url('/produtos?categoria=portateis')) ?>" class="cat-card">
            <span class="cat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/></svg></span>
            Portáteis
        </a>
        <a href="<?= e(url('/produtos?categoria=telemoveis')) ?>" class="cat-card">
            <span class="cat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="3"/><path d="M12 18h.01"/></svg></span>
            Telemóveis
        </a>
        <a href="<?= e(url('/produtos?categoria=componentes')) ?>" class="cat-card">
            <span class="cat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="6" width="12" height="12" rx="1"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/></svg></span>
            Componentes
        </a>
        <a href="<?= e(url('/produtos?categoria=acessorios')) ?>" class="cat-card">
            <span class="cat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3Z"/></svg></span>
            Acessórios
        </a>
    </div>
</section>

<section class="container section" style="padding-top:0">
    <div class="section-head">
        <div><h2>Em destaque</h2><p class="sub">Seleção de artigos disponíveis</p></div>
        <a href="<?= e(url('/produtos')) ?>" class="link-more">Ver todos →</a>
    </div>
    <?php if (empty($destaques)): ?>
        <p class="muted empty-state">Ainda não há produtos em destaque. Adicione artigos na área de gestão e marque-os como "destaque".</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($destaques as $p): ?>
                <?php require BASE_PATH . '/app/views/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section" style="padding-top:0">
    <div class="cta-band">
        <div class="cta-inner">
            <div>
                <h2>O seu equipamento avariou?</h2>
                <p>Fazemos diagnóstico e orçamento sem compromisso.</p>
            </div>
            <a href="<?= e(url('/contactos')) ?>" class="btn btn-light btn-lg">Falar connosco</a>
        </div>
    </div>
</section>
