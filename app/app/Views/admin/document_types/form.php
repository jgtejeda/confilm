<div class="content-header">
    <h1><?= $docType ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento' ?></h1>
    <a href="<?= site_url('admin/tipos-documento') ?>" class="btn btn-secondary">Volver</a>
</div>

<?php if (session('errors')): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error"><?= esc(session('error')) ?></div>
<?php endif; ?>

<form method="POST" action="<?= $docType ? site_url('admin/tipos-documento/' . $docType['id']) : site_url('admin/tipos-documento') ?>" class="form-card">
    <div class="form-group">
        <label for="name">Nombre *</label>
        <input type="text" id="name" name="name" value="<?= old('name', $docType['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea id="description" name="description" rows="3"><?= old('description', $docType['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label>Categoría *</label>
        <div class="radio-group">
            <label class="radio-label">
                <input type="radio" name="category" value="inicial" <?= old('category', $docType['category'] ?? '') === 'inicial' ? 'checked' : '' ?> required>
                Inicial
            </label>
            <label class="radio-label">
                <input type="radio" name="category" value="complementario" <?= old('category', $docType['category'] ?? '') === 'complementario' ? 'checked' : '' ?>>
                Complementario
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Tipos de archivo permitidos * (mínimo 1)</label>
        <div class="checkbox-group">
            <?php $validTypes = ['pdf', 'docx', 'xlsx', 'pptx', 'jpg', 'png']; ?>
            <?php $currentAllowed = $docType['allowed_types'] ?? []; ?>
            <?php foreach ($validTypes as $type): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="allowed_types[]" value="<?= $type ?>" <?= in_array($type, $currentAllowed) ? 'checked' : '' ?>>
                    <?= strtoupper($type) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="max_size_mb">Tamaño máximo (MB)</label>
            <input type="number" id="max_size_mb" name="max_size_mb" value="<?= old('max_size_mb', $docType['max_size_mb'] ?? 5) ?>" min="1">
        </div>

        <div class="form-group">
            <label for="max_months">Meses máximo (opcional)</label>
            <input type="number" id="max_months" name="max_months" value="<?= old('max_months', $docType['max_months'] ?? '') ?>" min="1">
        </div>

        <div class="form-group">
            <label for="sort_order">Orden</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= old('sort_order', $docType['sort_order'] ?? 0) ?>">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $docType ? 'Actualizar' : 'Crear' ?></button>
        <a href="<?= site_url('admin/tipos-documento') ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
