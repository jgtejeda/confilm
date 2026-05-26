<div class="content-header">
    <h1>Usuarios</h1>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error"><?= esc(session('error')) ?></div>
<?php endif; ?>

<div class="filters-bar">
    <form method="get" action="<?= site_url('admin/usuarios') ?>" class="filters-form">
        <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Buscar por nombre o email..." class="input-search">
        <select name="status" class="select-filter">
            <option value="">Todos los estados</option>
            <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pendiente</option>
            <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Activo</option>
            <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazado</option>
            <option value="suspended" <?= ($statusFilter ?? '') === 'suspended' ? 'selected' : '' ?>>Suspendido</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
        <?php if ($search || $statusFilter): ?>
            <a href="<?= site_url('admin/usuarios') ?>" class="btn btn-sm">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre completo</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Fecha registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="text-center">No hay usuarios registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= esc(trim($user['nombres'] . ' ' . $user['apellido_pat'] . ' ' . $user['apellido_mat'])) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $user['status'] ?>">
                                <?php
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'active' => 'Activo',
                                    'rejected' => 'Rechazado',
                                    'suspended' => 'Suspendido'
                                ];
                                echo $statusLabels[$user['status']] ?? $user['status'];
                                ?>
                            </span>
                        </td>
                        <td><?= esc(date('d/m/Y', strtotime($user['created_at']))) ?></td>
                        <td class="actions">
                            <a href="<?= site_url('admin/usuarios/' . $user['id']) ?>" class="btn btn-sm btn-secondary">Ver</a>
                            <a href="<?= site_url('admin/usuarios/' . $user['id'] . '/editar') ?>" class="btn btn-sm btn-primary">Editar</a>
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
