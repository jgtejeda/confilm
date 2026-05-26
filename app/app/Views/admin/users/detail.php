<div class="content-header">
    <h1>Detalle de Usuario</h1>
    <a href="<?= site_url('admin/usuarios') ?>" class="btn btn-secondary">Volver</a>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error"><?= esc(session('error')) ?></div>
<?php endif; ?>

<?php if (session('info')): ?>
    <div class="alert alert-info"><?= esc(session('info')) ?></div>
<?php endif; ?>

<div class="detail-grid">
    <div class="detail-card">
        <h2>Datos Personales</h2>
        <dl class="detail-list">
            <dt>Nombres</dt>
            <dd><?= esc($user['nombres']) ?></dd>

            <dt>Apellido Paterno</dt>
            <dd><?= esc($user['apellido_pat']) ?></dd>

            <dt>Apellido Materno</dt>
            <dd><?= esc($user['apellido_mat'] ?: '—') ?></dd>

            <dt>Email</dt>
            <dd>
                <?= esc($user['email']) ?>
                <?php if ($user['email_verified']): ?>
                    <span class="badge badge-active">Verificado</span>
                <?php else: ?>
                    <span class="badge badge-pending">Sin verificar</span>
                <?php endif; ?>
            </dd>

            <dt>Telefono</dt>
            <dd><?= esc($user['phone'] ?: '—') ?></dd>

            <dt>Rol</dt>
            <dd><span class="badge badge-role-<?= $user['role'] ?>"><?= esc(ucfirst($user['role'])) ?></span></dd>

            <dt>Estado</dt>
            <dd><span class="badge badge-<?= $user['status'] ?>"><?= esc(ucfirst($user['status'])) ?></span></dd>

            <dt>Ultimo acceso</dt>
            <dd><?= $user['last_login'] ? esc(date('d/m/Y H:i', strtotime($user['last_login']))) : '—' ?></dd>

            <dt>Registrado</dt>
            <dd><?= esc(date('d/m/Y H:i', strtotime($user['created_at']))) ?></dd>
        </dl>

        <div class="detail-actions">
            <a href="<?= site_url('admin/usuarios/' . $user['id'] . '/editar') ?>" class="btn btn-primary">Editar Usuario</a>
            <button type="button" class="btn btn-danger" onclick="confirmDeleteUser()">Eliminar Usuario</button>
        </div>
    </div>

    <div class="detail-card">
        <h2>Documentos</h2>
        <?php if (empty($documents)): ?>
            <p class="text-muted">No hay documentos cargados</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= esc($doc['doc_type_name']) ?></td>
                            <td>
                                <span class="badge badge-<?= $doc['status'] ?>">
                                    <?= esc(ucfirst($doc['status'])) ?>
                                </span>
                            </td>
                            <td><?= esc(date('d/m/Y', strtotime($doc['uploaded_at']))) ?></td>
                            <td class="actions">
                                <?php
                                    $isPending    = $doc['status'] === 'pending';
                                    $docActionUrl = site_url('admin/usuarios/' . $user['id'] . '/documento/' . $doc['id']);
                                    $viewerActions = $isPending
                                        ? ", {approveUrl: '{$docActionUrl}', rejectDocId: {$doc['id']}, rejectUserId: {$user['id']}}"
                                        : '';
                                ?>
                                <button type="button" class="btn btn-sm btn-secondary"
                                    onclick="DocumentViewer.open(<?= $doc['id'] ?>, '<?= site_url('admin/documentos/ver/') ?>'<?= $viewerActions ?>)">
                                    Ver
                                </button>
                                <?php if ($isPending): ?>
                                    <button type="button" class="btn btn-sm btn-danger reject-btn"
                                        data-doc-id="<?= $doc['id'] ?>"
                                        data-user-id="<?= $user['id'] ?>">
                                        Rechazar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if ($inscription): ?>
    <?php
        $insStatus = $inscription['status'];
        $insLabels = [
            'incomplete'   => ['text' => 'Incompleta',      'color' => '#aaa',    'hint' => 'El usuario aún no envió sus documentos para revisión.'],
            'under_review' => ['text' => 'En revisión',     'color' => '#f39c12', 'hint' => 'El usuario envió sus documentos. Pendiente de aprobación.'],
            'approved'     => ['text' => 'Aprobada',        'color' => '#2ecc71', 'hint' => 'Inscripción aprobada. El usuario tiene acceso completo.'],
            'rejected'     => ['text' => 'Rechazada',       'color' => '#e74c3c', 'hint' => 'Inscripción rechazada.'],
        ];
        $insInfo = $insLabels[$insStatus] ?? ['text' => ucfirst($insStatus), 'color' => '#aaa', 'hint' => ''];
        $canReview = in_array($insStatus, ['incomplete', 'under_review']);
    ?>
    <div class="detail-card">
        <h2>Inscripción Actual</h2>
        <dl class="detail-list">
            <dt>Estado</dt>
            <dd>
                <span class="badge badge-<?= $insStatus ?>" style="font-size:0.85rem;">
                    <?= esc($insInfo['text']) ?>
                </span>
                <span style="display:block;font-size:0.78rem;color:rgba(245,240,232,0.45);margin-top:0.3rem;">
                    <?= esc($insInfo['hint']) ?>
                </span>
            </dd>
            <dt>Enviada el</dt>
            <dd><?= $inscription['submitted_at'] ? esc(date('d/m/Y H:i', strtotime($inscription['submitted_at']))) : '—' ?></dd>
            <dt>Registrada el</dt>
            <dd><?= esc(date('d/m/Y H:i', strtotime($inscription['created_at']))) ?></dd>
            <?php if (!empty($inscription['rejection_note'])): ?>
            <dt>Motivo rechazo</dt>
            <dd style="color:#f08080;"><?= esc($inscription['rejection_note']) ?></dd>
            <?php endif; ?>
        </dl>

        <?php if ($canReview): ?>
        <div style="display:flex;gap:0.75rem;margin-top:1.25rem;">
            <!-- Aprobar inscripción -->
            <form method="POST" action="<?= site_url('admin/usuarios/' . $user['id'] . '/validate-inscription') ?>"
                  style="flex:1"
                  onsubmit="return confirm('¿Aprobar la inscripción de <?= esc(addslashes($user['nombres'] . ' ' . $user['apellido_pat'])) ?>?\n\nEsto marcará su cuenta como Activa y le enviará un correo de confirmación.')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-success" style="width:100%;font-size:0.92rem;">
                    ✓ Aprobar inscripción
                </button>
            </form>
            <!-- Rechazar inscripción -->
            <button type="button" class="btn btn-danger" style="flex:1;font-size:0.92rem;"
                    onclick="openRejectInscriptionModal()">
                ✗ Rechazar inscripción
            </button>
        </div>
        <p style="font-size:0.78rem;color:rgba(245,240,232,0.4);margin:0.6rem 0 0;text-align:center;">
            Al aprobar se enviará un correo y notificación al usuario.
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div class="detail-actions-bar">
    <div class="status-actions">
        <span>Cambiar estado:</span>
        <button type="button" class="btn btn-sm btn-success" onclick="changeStatus('active')">Activar</button>
        <button type="button" class="btn btn-sm btn-warning" onclick="changeStatus('suspended')">Suspender</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="changeStatus('rejected')">Rechazar</button>
    </div>
    <div class="password-actions">
        <form method="POST" action="<?= site_url('admin/usuarios/' . $user['id'] . '/reset-password') ?>" style="display:inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('¿Resetear contraseña y enviar por correo?')">Resetear Contraseña</button>
        </form>
    </div>
</div>

<!-- Form oculto: eliminar usuario -->
<form id="deleteUserForm" method="POST" action="<?= site_url('admin/usuarios/' . $user['id'] . '/eliminar') ?>" style="display:none">
    <?= csrf_field() ?>
</form>

<!-- Modal de rechazo de INSCRIPCIÓN -->
<div id="rejectInscriptionModal" class="modal" style="display:none">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <h3>Rechazar Inscripción</h3>
        <div class="form-group">
            <label for="inscription_rejection_note">Motivo del rechazo <span style="color:rgba(245,240,232,0.5);font-size:0.85rem;">(mínimo 30 caracteres)</span></label>
            <textarea id="inscription_rejection_note" rows="4" placeholder="Describe el motivo del rechazo..."></textarea>
            <small id="insCharCount" style="display:block;margin-top:0.3rem;font-size:0.82rem;color:#ef4444;">0 / 30 mínimo</small>
            <small id="insRejectError" style="display:none;color:#ef4444;margin-top:0.3rem;"></small>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="cancelInsReject">Cancelar</button>
            <button type="button" class="btn btn-danger" id="submitInsReject">Rechazar</button>
        </div>
    </div>
</div>

<!-- Form oculto para rechazar inscripción (POST nativo) -->
<form id="rejectInscriptionForm" method="POST"
      action="<?= site_url('admin/usuarios/' . $user['id'] . '/validate-inscription') ?>"
      style="display:none">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="reject">
    <input type="hidden" name="rejection_note" id="insRejectionNoteInput">
</form>

<!-- Modal de rechazo de DOCUMENTO -->
<div id="rejectModal" class="modal" style="display:none">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <h3>Rechazar Documento</h3>
        <div class="form-group">
            <label for="rejection_note">Motivo del rechazo <span style="color:rgba(245,240,232,0.5);font-size:0.85rem;">(mínimo 20 caracteres)</span></label>
            <textarea id="rejection_note" rows="4" placeholder="Describe el motivo del rechazo..."></textarea>
            <small id="charCount" style="display:block;margin-top:0.3rem;font-size:0.82rem;color:#ef4444;">0 / 20 mínimo</small>
            <small id="rejectError" style="display:none;color:#ef4444;margin-top:0.3rem;"></small>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="cancelReject">Cancelar</button>
            <button type="button" class="btn btn-danger" id="submitReject">Rechazar</button>
        </div>
    </div>
</div>

<script>
// ─── Modal de rechazo ────────────────────────────────────────────────────────
const rejectModal    = document.getElementById('rejectModal');
const rejectionNote  = document.getElementById('rejection_note');
const charCount      = document.getElementById('charCount');
const rejectError    = document.getElementById('rejectError');
const submitReject   = document.getElementById('submitReject');
const cancelReject   = document.getElementById('cancelReject');

let currentRejectUrl = '';   // URL de la acción actual, seteada al abrir el modal

// Abrir modal de rechazo (llamado desde botones .reject-btn y desde el document viewer)
function openRejectModal(docId, userId) {
    currentRejectUrl = '<?= site_url('admin/usuarios/') ?>' + userId + '/documento/' + docId;
    rejectionNote.value = '';
    rejectError.style.display = 'none';
    updateCharCount();
    submitReject.disabled = false;
    submitReject.textContent = 'Rechazar';
    rejectModal.style.display = 'flex';
    setTimeout(function() { rejectionNote.focus(); }, 50);
}

document.querySelectorAll('.reject-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        openRejectModal(this.dataset.docId, this.dataset.userId);
    });
});

cancelReject.addEventListener('click', function() {
    rejectModal.style.display = 'none';
});

rejectModal.querySelector('.modal-backdrop').addEventListener('click', function() {
    rejectModal.style.display = 'none';
});

rejectionNote.addEventListener('input', updateCharCount);

function updateCharCount() {
    const len = rejectionNote.value.trim().length;
    charCount.textContent = len + ' / 20 mínimo';
    charCount.style.color = len >= 20 ? '#22c55e' : '#ef4444';
}

// Envío por AJAX (mismo approach que Aprobar)
submitReject.addEventListener('click', function() {
    const note = rejectionNote.value.trim();

    if (note.length < 20) {
        rejectError.textContent = 'El motivo debe tener al menos 20 caracteres (' + note.length + '/20).';
        rejectError.style.display = 'block';
        rejectionNote.focus();
        return;
    }

    if (!currentRejectUrl) {
        rejectError.textContent = 'Error interno: URL de rechazo no definida.';
        rejectError.style.display = 'block';
        return;
    }

    rejectError.style.display = 'none';
    submitReject.disabled = true;
    submitReject.textContent = 'Rechazando…';

    const body = new URLSearchParams({ action: 'reject', rejection_note: note });
    const csrfInput = document.querySelector('input[name="csrf_test_name"]');
    if (csrfInput) body.append(csrfInput.name, csrfInput.value);

    fetch(currentRejectUrl, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: body,
        redirect: 'follow'
    })
    .then(function(r) {
        if (r.ok || r.redirected) {
            rejectModal.style.display = 'none';
            location.reload();
        } else {
            rejectError.textContent = 'Error del servidor (' + r.status + '). Intenta de nuevo.';
            rejectError.style.display = 'block';
            submitReject.disabled = false;
            submitReject.textContent = 'Rechazar';
        }
    })
    .catch(function(err) {
        console.error('rejectDocument error:', err);
        rejectError.textContent = 'Error de conexión. Intenta de nuevo.';
        rejectError.style.display = 'block';
        submitReject.disabled = false;
        submitReject.textContent = 'Rechazar';
    });
});

// Eliminar usuario
function confirmDeleteUser() {
    if (confirm('¿Eliminar a <?= esc(addslashes($user['nombres'] . ' ' . $user['apellido_pat'])) ?>?\nEsto borrará su cuenta, documentos e inscripción. Esta acción no se puede deshacer.')) {
        document.getElementById('deleteUserForm').submit();
    }
}

// ─── Modal rechazo de inscripción ────────────────────────────────────────────
const insModal     = document.getElementById('rejectInscriptionModal');
const insNoteArea  = document.getElementById('inscription_rejection_note');
const insCharCount = document.getElementById('insCharCount');
const insError     = document.getElementById('insRejectError');

function openRejectInscriptionModal() {
    insNoteArea.value = '';
    insError.style.display = 'none';
    updateInsCharCount();
    document.getElementById('submitInsReject').disabled = false;
    document.getElementById('submitInsReject').textContent = 'Rechazar';
    insModal.style.display = 'flex';
    setTimeout(function() { insNoteArea.focus(); }, 50);
}

document.getElementById('cancelInsReject').addEventListener('click', function() {
    insModal.style.display = 'none';
});
insModal.querySelector('.modal-backdrop').addEventListener('click', function() {
    insModal.style.display = 'none';
});
insNoteArea.addEventListener('input', updateInsCharCount);

function updateInsCharCount() {
    const len = insNoteArea.value.trim().length;
    insCharCount.textContent = len + ' / 30 mínimo';
    insCharCount.style.color = len >= 30 ? '#22c55e' : '#ef4444';
}

document.getElementById('submitInsReject').addEventListener('click', function() {
    const note = insNoteArea.value.trim();
    if (note.length < 30) {
        insError.textContent = 'El motivo debe tener al menos 30 caracteres (' + note.length + '/30).';
        insError.style.display = 'block';
        insNoteArea.focus();
        return;
    }
    insError.style.display = 'none';
    document.getElementById('insRejectionNoteInput').value = note;
    document.getElementById('rejectInscriptionForm').submit();
});

// ─── Cambio de status de usuario ─────────────────────────────────────────────
function changeStatus(newStatus) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';

    fetch('<?= site_url('admin/usuarios/' . $user['id'] . '/status') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            [csrfHeader]: csrfToken
        },
        body: JSON.stringify({ status: newStatus })
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
        alert('Error de conexion');
    });
}
</script>
