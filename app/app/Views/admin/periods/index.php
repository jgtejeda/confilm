<div class="content-header">
    <h1>Periodos</h1>
    <a href="<?= site_url('admin/periodos/nuevo') ?>" class="btn btn-primary">Nuevo Periodo</a>
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
                <th>Inicio</th>
                <th>Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($periods)): ?>
                <tr>
                    <td colspan="5" class="text-center">No hay periodos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($periods as $period): ?>
                    <tr>
                        <td><?= esc($period['name']) ?></td>
                        <td><?= esc(date('d/m/Y H:i', strtotime($period['start_date']))) ?></td>
                        <td><?= esc(date('d/m/Y H:i', strtotime($period['end_date']))) ?></td>
                        <td>
                            <span class="badge <?= $period['status_badge']['class'] ?>" data-id="<?= $period['id'] ?>">
                                <?= $period['status_badge']['label'] ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= site_url('admin/periodos/' . $period['id'] . '/editar') ?>" class="btn btn-sm btn-secondary">Editar</a>
                            <button type="button" class="btn btn-sm <?= $period['active'] ? 'btn-warning' : 'btn-success' ?>" onclick="togglePeriod(<?= $period['id'] ?>)">
                                <?= $period['active'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function togglePeriod(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';

    fetch('<?= site_url('admin/periodos/') ?>' + id + '/toggle', {
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
            location.reload();
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
