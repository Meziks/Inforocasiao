<section class="page-head">
    <div class="container">
        <h1>Produtos</h1>
        <p class="muted">Computadores, telemóveis e componentes — novos e recondicionados.</p>
    </div>
</section>

<section class="container section">
    <form method="get" action="<?= e(url('/produtos')) ?>" class="filters">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar por nome ou marca…" class="input">
        <select name="categoria" class="input">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= e($c['slug']) ?>" <?= $categoria === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <?php if (empty($produtos)): ?>
        <p class="muted empty-state">Não foram encontrados produtos com esses critérios.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($produtos as $p): ?>
                <?php require BASE_PATH . '/app/views/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
