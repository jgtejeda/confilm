<div class="notifications-container">
    <h1 class="page-title">Notificaciones</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de envío -->
    <div class="notification-form-card">
        <h2>Enviar notificación</h2>

        <form action="<?= site_url('admin/notificaciones/send') ?>" method="POST" class="notification-form">
            <?= csrf_field() ?>

            <!-- Tipo de destino -->
            <div class="form-group">
                <label for="target_type">Enviar a:</label>
                <select name="target_type" id="target_type" required onchange="toggleTargetFields()">
                    <option value="">Seleccionar...</option>
                    <option value="user">Usuario individual</option>
                    <option value="group">Grupo por estado</option>
                </select>
            </div>

            <!-- Campo usuario individual -->
            <div class="form-group" id="user-field" style="display: none;">
                <label for="target_id">Buscar usuario por email:</label>
                <input type="email" name="target_email" id="target_email" placeholder="email@ejemplo.com">
                <input type="hidden" name="target_id" id="target_id">
            </div>

            <!-- Campo grupo por estado -->
            <div class="form-group" id="group-field" style="display: none;">
                <label for="target_status">Estado:</label>
                <select name="target_status" id="target_status">
                    <option value="">Seleccionar estado...</option>
                    <option value="pending">Pendiente</option>
                    <option value="active">Activo</option>
                    <option value="rejected">Rechazado</option>
                    <option value="suspended">Suspendido</option>
                </select>
            </div>

            <!-- Título -->
            <div class="form-group">
                <label for="title">Título:</label>
                <input type="text" name="title" id="title" required maxlength="200" placeholder="Título de la notificación">
            </div>

            <!-- Cuerpo -->
            <div class="form-group">
                <label for="body">Mensaje:</label>
                <textarea name="body" id="body" required rows="5" placeholder="Cuerpo del mensaje..."></textarea>
            </div>

            <!-- Checkbox email -->
            <div class="form-group form-check">
                <input type="checkbox" name="send_email" id="send_email" value="1">
                <label for="send_email">Enviar también por correo electrónico</label>
            </div>

            <button type="submit" class="btn btn-primary">Enviar notificación</button>
        </form>
    </div>

    <!-- Historial de notificaciones enviadas -->
    <div class="notification-history-card">
        <h2>Historial de notificaciones enviadas</h2>

        <?php if (empty($notifications)): ?>
            <p class="text-muted">No has enviado notificaciones aún.</p>
        <?php else: ?>
            <table class="notification-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Email</th>
                        <th>Enviado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifications as $notif): ?>
                        <tr>
                            <td><?= esc(date('d/m/Y H:i', strtotime($notif['created_at']))) ?></td>
                            <td><?= esc($notif['title']) ?></td>
                            <td>
                                <span class="badge badge-<?= esc($notif['type']) ?>">
                                    <?= esc($notif['type']) ?>
                                </span>
                            </td>
                            <td><?= $notif['send_email'] ? 'Sí' : 'No' ?></td>
                            <td>
                                <?php if ($notif['email_sent_at']): ?>
                                    <span class="text-success">✓ <?= esc(date('d/m/Y H:i', strtotime($notif['email_sent_at']))) ?></span>
                                <?php elseif ($notif['send_email']): ?>
                                    <span class="text-warning">Pendiente</span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleTargetFields() {
    const targetType = document.getElementById('target_type').value;
    const userField = document.getElementById('user-field');
    const groupField = document.getElementById('group-field');

    userField.style.display = targetType === 'user' ? 'block' : 'none';
    groupField.style.display = targetType === 'group' ? 'block' : 'none';
}
</script>
