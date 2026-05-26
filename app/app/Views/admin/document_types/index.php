<div class="content-header">
    <h1>Tipos de Documento</h1>
    <a href="<?= site_url('admin/tipos-documento/nuevo') ?>" class="btn btn-primary">Nuevo Tipo</a>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error"><?= esc(session('error')) ?></div>
<?php endif; ?>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Tipos de archivo</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($docTypes)): ?>
                <tr>
                    <td colspan="5" class="text-center">No hay tipos de documento registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($docTypes as $dt): ?>
                    <tr>
                        <td><?= esc($dt['name']) ?></td>
                        <td><?= esc(ucfirst($dt['category'])) ?></td>
                        <td>
                            <?php
                            $types = json_decode($dt['allowed_types'], true) ?? [];
                            echo esc(implode(', ', $types));
                            ?>
                        </td>
                        <td>
                            <span class="badge <?= $dt['active'] ? 'badge-active' : 'badge-inactive' ?>" data-id="<?= $dt['id'] ?>">
                                <?= $dt['active'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= site_url('admin/tipos-documento/' . $dt['id'] . '/editar') ?>" class="btn btn-sm btn-secondary">Editar</a>
                            <button type="button" class="btn btn-sm <?= $dt['active'] ? 'btn-warning' : 'btn-success' ?>" onclick="toggleDocType(<?= $dt['id'] ?>)">
                                <?= $dt['active'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pager->getPageCount() > 1): ?>
    <div class="pagination">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>

<script>
function toggleDocType(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';

    fetch('<?= site_url('admin/tipos-documento/') ?>' + id + '/toggle', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            [csrfHeader]: csrfToken
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.querySelector('.badge[data-id="' + id + '"]');
            const btn = badge.closest('tr').querySelector('.actions button:last-child');

            if (data.active) {
                badge.textContent = 'Activo';
                badge.className = 'badge badge-active';
                btn.textContent = 'Desactivar';
                btn.className = 'btn btn-sm btn-warning';
            } else {
                badge.textContent = 'Inactivo';
                badge.className = 'badge badge-inactive';
                btn.textContent = 'Activar';
                btn.className = 'btn btn-sm btn-success';
            }
        } else {
            alert(data.error || 'Error al actualizar');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de conexión');
    });
}
</script>
