<div class="content-header">
    <h1><?= $period ? 'Editar Periodo' : 'Nuevo Periodo' ?></h1>
    <a href="<?= site_url('admin/periodos') ?>" class="btn btn-secondary">Volver</a>
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

<form method="POST" action="<?= $period ? site_url('admin/periodos/' . $period['id']) : site_url('admin/periodos') ?>" class="form-card">
    <div class="form-group">
        <label for="name">Nombre *</label>
        <input type="text" id="name" name="name" value="<?= old('name', $period['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea id="description" name="description" rows="3"><?= old('description', $period['description'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="start_date">Fecha de inicio *</label>
            <input type="datetime-local" id="start_date" name="start_date" value="<?= old('start_date', isset($period['start_date']) ? date('Y-m-d\TH:i', strtotime($period['start_date'])) : '') ?>" required>
        </div>

        <div class="form-group">
            <label for="end_date">Fecha de fin *</label>
            <input type="datetime-local" id="end_date" name="end_date" value="<?= old('end_date', isset($period['end_date']) ? date('Y-m-d\TH:i', strtotime($period['end_date'])) : '') ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label>Estado</label>
        <div class="checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="active" value="1" <?= old('active', $period['active'] ?? '1') == '1' ? 'checked' : '' ?>>
                Activo
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Tipos de documento permitidos</label>
        <?php
        $grouped = [];
        foreach ($docTypes as $dt) {
            $grouped[$dt['category']][] = $dt;
        }
        ?>
        <?php foreach ($grouped as $category => $types): ?>
            <fieldset style="margin-bottom: 1rem; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                <legend style="font-weight: 600; padding: 0 0.5rem;"><?= esc(ucfirst($category)) ?></legend>
                <div class="checkbox-group">
                    <?php foreach ($types as $dt): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="doc_types[]" value="<?= $dt['id'] ?>" <?= in_array($dt['id'], $assignedDocIds) ? 'checked' : '' ?>>
                            <?= esc($dt['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
        <?php if (empty($docTypes)): ?>
            <p class="text-muted">No hay tipos de documento activos. <a href="<?= site_url('admin/tipos-documento/nuevo') ?>">Crear uno</a></p>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $period ? 'Actualizar' : 'Crear' ?></button>
        <a href="<?= site_url('admin/periodos') ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
