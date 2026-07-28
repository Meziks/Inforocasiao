<section class="container admin-section">
    <div class="admin-head">
        <div>
            <h1>Artigos</h1>
            <p class="muted"><?= (int) $stats['total'] ?> artigo(s) no catálogo.</p>
        </div>
        <a href="<?= e(url('/admin/produtos/novo')) ?>" class="btn btn-primary">+ Novo artigo</a>
    </div>

    <div class="stats-row">
        <a href="<?= e(url('/admin/dashboard')) ?>" class="stat-tile <?= $estado === '' ? 'active' : '' ?>">
            <strong><?= (int) $stats['total'] ?></strong><span>Total</span>
        </a>
        <a href="<?= e(url('/admin/dashboard?estado=visivel')) ?>" class="stat-tile stat-ok <?= $estado === 'visivel' ? 'active' : '' ?>">
            <strong><?= (int) $stats['visiveis'] ?></strong><span>Visíveis</span>
        </a>
        <a href="<?= e(url('/admin/dashboard?estado=destaque')) ?>" class="stat-tile stat-star <?= $estado === 'destaque' ? 'active' : '' ?>">
            <strong><?= (int) $stats['destaque'] ?></strong><span>Em destaque</span>
        </a>
        <a href="<?= e(url('/admin/dashboard?estado=esgotado')) ?>" class="stat-tile stat-danger <?= $estado === 'esgotado' ? 'active' : '' ?>">
            <strong><?= (int) $stats['esgotados'] ?></strong><span>Esgotados</span>
        </a>
    </div>

    <form method="get" action="<?= e(url('/admin/dashboard')) ?>" class="filters admin-filters">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar por nome ou marca…" class="input">
        <select name="categoria" class="input">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $catFiltro === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="estado" class="input">
            <option value="">Todos os estados</option>
            <option value="visivel" <?= $estado === 'visivel' ? 'selected' : '' ?>>Visíveis</option>
            <option value="oculto" <?= $estado === 'oculto' ? 'selected' : '' ?>>Ocultos</option>
            <option value="destaque" <?= $estado === 'destaque' ? 'selected' : '' ?>>Em destaque</option>
            <option value="esgotado" <?= $estado === 'esgotado' ? 'selected' : '' ?>>Esgotados</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <?php if ($q !== '' || $catFiltro > 0 || $estado !== ''): ?>
            <a href="<?= e(url('/admin/dashboard')) ?>" class="btn btn-ghost">Limpar</a>
        <?php endif; ?>
    </form>

    <?php if ((int) $stats['total'] === 0): ?>
        <div class="empty-state card">
            <p>Ainda não há artigos. Comece por adicionar o primeiro.</p>
            <a href="<?= e(url('/admin/produtos/novo')) ?>" class="btn btn-primary">Adicionar artigo</a>
        </div>
    <?php elseif (empty($produtos)): ?>
        <div class="empty-state card">
            <p>Não há artigos que correspondam a estes filtros.</p>
            <a href="<?= e(url('/admin/dashboard')) ?>" class="btn btn-outline">Limpar filtros</a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th></th><th>Nome</th><th class="col-cat">Categoria</th><th>Preço</th>
                        <th class="col-stock">Stock</th><th>Estado</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td class="cell-thumb">
                                <?php if (!empty($p['image'])): ?>
                                    <img src="<?= e(uploadUrl($p['image'])) ?>" alt="">
                                <?php else: ?>
                                    <span class="mini-placeholder"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#9298a6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.3 9 5.2M3.3 7.5 12 12.5l8.7-5M12 22V12.5"/><path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= e(url('/admin/produtos/' . $p['id'] . '/editar')) ?>" class="row-title"><?= e($p['name']) ?></a>
                                <?php if (!empty($p['is_featured'])): ?><span class="tag tag-star">★ destaque</span><?php endif; ?>
                                <?php if (!empty($p['brand'])): ?><br><span class="muted small"><?= e($p['brand']) ?></span><?php endif; ?>
                            </td>
                            <td class="col-cat"><?= e($p['category_name'] ?? 'Sem categoria') ?></td>
                            <td><?= money($p['price']) ?></td>
                            <td class="col-stock"><span class="<?= (int) $p['stock'] === 0 ? 'stock-zero' : '' ?>"><?= (int) $p['stock'] ?></span></td>
                            <td>
                                <?php if (!empty($p['is_active'])): ?>
                                    <span class="tag tag-on">Visível</span>
                                <?php else: ?>
                                    <span class="tag tag-off">Oculto</span>
                                <?php endif; ?>
                            </td>
                            <td class="cell-actions">
                                <a href="<?= e(url('/admin/produtos/' . $p['id'] . '/editar')) ?>" class="btn btn-sm btn-outline">Editar</a>
                                <form action="<?= e(url('/admin/produtos/' . $p['id'] . '/apagar')) ?>" method="post" class="inline-form"
                                      onsubmit="return confirm('Apagar o artigo &quot;<?= e($p['name']) ?>&quot;? Esta ação não pode ser desfeita.');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Apagar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
