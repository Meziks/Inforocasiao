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
        <?php if ($isEdit): ?>
            <a href="<?= e(url('/produto/' . $produto['id'])) ?>" target="_blank" class="btn btn-outline btn-sm">Ver no site ↗</a>
        <?php endif; ?>
    </div>

    <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form card">
        <?= csrf_field() ?>

        <div class="form-section">
            <h3 class="form-section-title">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                Informação básica
            </h3>
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
            <label>Descrição
                <textarea name="description" rows="4" class="input" placeholder="Características, estado, garantia…"><?= $val('description') ?></textarea>
            </label>
        </div>

        <div class="form-section">
            <h3 class="form-section-title">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Preço e disponibilidade
            </h3>
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
        </div>

        <div class="form-section">
            <h3 class="form-section-title">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.3 9 5.2M3.3 7.5 12 12.5l8.7-5M12 22V12.5"/><path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                Fotografias
            </h3>
            <div class="image-slots-grid">
                <?php
                $extraImages = $extraImages ?? [];
                for ($slot = 1; $slot <= 4; $slot++):
                    $isMain    = $slot === 1;
                    $current   = $isMain ? ($produto['image'] ?? null) : ($extraImages[$slot - 1] ?? null);
                    $urlKey    = $isMain ? 'image_url' : "image_url{$slot}";
                    $fileKey   = $isMain ? 'image' : "image{$slot}";
                    $curUrlVal = ($current && isRemoteImage($current)) ? $current : '';
                ?>
                <div class="image-slot">
                    <span class="image-slot-label"><?= $isMain ? 'Principal' : "Imagem $slot" ?></span>
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
                    <input type="url" name="<?= e($urlKey) ?>" class="input" placeholder="…ou cole um URL"
                           value="<?= e($curUrlVal) ?>">
                </div>
                <?php endfor; ?>
            </div>
            <span class="muted small">Pode carregar um ficheiro ou colar um URL (útil para imagens de licença livre).</span>
        </div>

        <div class="form-section form-section-last">
            <h3 class="form-section-title">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                Visibilidade
            </h3>
            <div class="form-checks">
                <label class="check"><input type="checkbox" name="is_active" value="1" <?= ($produto['is_active'] ?? 1) ? 'checked' : '' ?>> Visível no site</label>
                <label class="check"><input type="checkbox" name="is_featured" value="1" <?= ($produto['is_featured'] ?? 0) ? 'checked' : '' ?>> Mostrar em destaque na página inicial</label>
            </div>
        </div>

        <div class="form-actions form-actions-sticky">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Guardar alterações' : 'Criar artigo' ?></button>
            <a href="<?= e(url('/admin/dashboard')) ?>" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</section>
