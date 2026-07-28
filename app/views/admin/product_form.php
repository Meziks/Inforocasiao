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
                    <option value="">Sem categoria</option>
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

        <div class="image-slots">
            <p class="field-label">Fotografias (até 4)</p>
            <div class="image-slots-grid">
                <?php
                $extraImages = $extraImages ?? [];
                for ($slot = 1; $slot <= 4; $slot++):
                    $isMain   = $slot === 1;
                    $current  = $isMain ? ($produto['image'] ?? null) : ($extraImages[$slot - 1] ?? null);
                    $urlKey   = $isMain ? 'image_url' : "image{$slot}_url";
                    $fileKey  = $isMain ? 'image' : "image{$slot}";
                    $curUrlVal = ($current && isRemoteImage($current)) ? $current : '';
                ?>
                <div class="image-slot">
                    <span class="image-slot-label"><?= $isMain ? 'Imagem principal' : "Imagem $slot" ?></span>
                    <?php if ($current): ?>
                        <div class="image-slot-preview">
                            <img src="<?= e(uploadUrl($current)) ?>" alt="">
                            <?php if (!$isMain): ?>
                                <label class="check check-remove">
                                    <input type="checkbox" name="image<?= $slot ?>_remove" value="1"> Remover
                                </label>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="<?= e($fileKey) ?>" accept="image/*" class="input input-file">
                    <input type="url" name="<?= e($urlKey) ?>" class="input" placeholder="…ou cole um URL de imagem"
                           value="<?= e($curUrlVal) ?>">
                </div>
                <?php endfor; ?>
            </div>
            <span class="muted small">Pode carregar um ficheiro ou colar um URL (útil para imagens de licença livre).</span>
        </div>

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
