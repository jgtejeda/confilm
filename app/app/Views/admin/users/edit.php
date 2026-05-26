<div class="content-header">
    <h1>Editar Usuario</h1>
    <a href="<?= site_url('admin/usuarios/' . $user['id']) ?>" class="btn btn-secondary">Volver</a>
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

<div class="form-card-container">
    <form method="POST" action="<?= site_url('admin/usuarios/' . $user['id']) ?>" class="form-card">
        <div class="form-section">
            <h2>Datos Personales</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombres">Nombres *</label>
                    <input type="text" id="nombres" name="nombres" value="<?= old('nombres', $user['nombres']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellido_pat">Apellido Paterno *</label>
                    <input type="text" id="apellido_pat" name="apellido_pat" value="<?= old('apellido_pat', $user['apellido_pat']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellido_mat">Apellido Materno</label>
                    <input type="text" id="apellido_mat" name="apellido_mat" value="<?= old('apellido_mat', $user['apellido_mat'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                    <?php if (!$user['email_verified']): ?>
                        <span class="form-hint">Email sin verificar</span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone">Telefono</label>
                    <input type="text" id="phone" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Configuracion</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select id="role" name="role">
                        <option value="user" <?= old('role', $user['role']) === 'user' ? 'selected' : '' ?>>Usuario</option>
                        <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <option value="superadmin" <?= old('role', $user['role']) === 'superadmin' ? 'selected' : '' ?>>Super Administrador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Estado</label>
                    <select id="status" name="status">
                        <option value="pending" <?= old('status', $user['status']) === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="active" <?= old('status', $user['status']) === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="rejected" <?= old('status', $user['status']) === 'rejected' ? 'selected' : '' ?>>Rechazado</option>
                        <option value="suspended" <?= old('status', $user['status']) === 'suspended' ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= site_url('admin/usuarios/' . $user['id']) ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
