<?php
$isEdit  = $produto !== null;
$action  = $isEdit ? url('/admin/produtos/' . $produto['id']) : url('/admin/produtos');
$val = fn(string $k, $d = '') => e((string) ($produto[$k] ?? $d));
$conditions = ['Novo', 'Usado', 'Recondicionado'];
?>
<section class="container admin-section narrow">
    <div class="admin-head">
        <div>
            <a href="<?= e(url('/admin/dashboard')) ?>" class="muted small">← Voltar aos artigos</a>
            <h1><?= $isEdit ? 'Editar artigo' : 'Novo artigo' ?></h1>
        </div>
    </div>

    <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form card">
        <?= csrf_field() ?>

        <label>Nome do artigo *
            <input type="text" name="name" required value="<?= $val('name') ?>" class="input" placeholder="Ex: Portátil Lenovo IdeaPad 3">
        </label>

        <div class="form-row">
            <label>Marca
                <input type="text" name="brand" value="<?= $val('brand') ?>" class="input" placeholder="Ex: Lenovo">
            </label>
            <label>Categoria
                <select name="category_id" class="input">
                    <option value="">— Sem categoria —</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)($produto['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="form-row">
            <label>Preço (€) *
                <input type="text" name="price" required value="<?= $val('price', '0.00') ?>" class="input" placeholder="199,99">
            </label>
            <label>Stock (unidades)
                <input type="number" name="stock" min="0" value="<?= $val('stock', '1') ?>" class="input">
            </label>
            <label>Condição
                <select name="condition" class="input">
                    <?php foreach ($conditions as $cond): ?>
                        <option value="<?= e($cond) ?>" <?= ($produto['condition'] ?? 'Novo') === $cond ? 'selected' : '' ?>><?= e($cond) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <label>Descrição
            <textarea name="description" rows="5" class="input" placeholder="Características, estado, garantia…"><?= $val('description') ?></textarea>
        </label>

        <label>Imagem
            <?php if ($isEdit && !empty($produto['image'])): ?>
                <span class="current-image">
                    <img src="<?= e(uploadUrl($produto['image'])) ?>" alt="">
                    <span class="muted small">Imagem atual — envie uma nova para substituir.</span>
                </span>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" class="input">
        </label>

        <div class="form-checks">
            <label class="check"><input type="checkbox" name="is_active" value="1" <?= ($produto['is_active'] ?? 1) ? 'checked' : '' ?>> Visível no site</label>
            <label class="check"><input type="checkbox" name="is_featured" value="1" <?= ($produto['is_featured'] ?? 0) ? 'checked' : '' ?>> Mostrar em destaque na página inicial</label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Guardar alterações' : 'Criar artigo' ?></button>
            <a href="<?= e(url('/admin/dashboard')) ?>" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</section>
