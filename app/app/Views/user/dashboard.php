<?php
// $data viene del controller con: user, noInscription, hasActivePeriod, inscription, period, initialDocTypes, timelineStep
?>

<?php if ($noInscription): ?>
    <!-- Sin inscripción -->
    <div class="no-inscription">
        <h2 class="no-inscription-title">No tienes inscripciones activas</h2>
        <p class="no-inscription-text">
            <?php if ($hasActivePeriod): ?>
                Para comenzar el proceso, necesitas inscribirte en la convocatoria activa.
            <?php else: ?>
                No hay convocatoria abierta en este momento.
            <?php endif; ?>
        </p>
        <?php if ($hasActivePeriod): ?>
            <a href="<?= site_url('registro') ?>" class="no-inscription-link">Ir a registro</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Dashboard con inscripción -->
    
    <!-- Mis Datos -->
    <section class="dashboard-card">
        <h2 class="dashboard-card-title">Mis Datos</h2>
        <div class="user-data-grid">
            <div class="user-data-item">
                <span class="user-data-label">Nombre completo</span>
                <span class="user-data-value"><?= esc($user['nombres'] . ' ' . ($user['apellido_pat'] ?? '') . ' ' . ($user['apellido_mat'] ?? '')) ?></span>
            </div>
            <div class="user-data-item">
                <span class="user-data-label">Email</span>
                <span class="user-data-value"><?= esc($user['email']) ?></span>
            </div>
            <div class="user-data-item">
                <span class="user-data-label">Teléfono</span>
                <span class="user-data-value"><?= esc($user['phone'] ?? 'No registrado') ?></span>
            </div>
            <div class="user-data-item">
                <span class="user-data-label">Fecha de registro</span>
                <span class="user-data-value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>
    </section>
    
    <!-- Documentos Iniciales -->
    <section class="dashboard-card">
        <h2 class="dashboard-card-title">Documentos Iniciales</h2>
        <?php if (!empty($initialDocTypes)): ?>
            <div class="initial-docs-grid">
                <?php foreach ($initialDocTypes as $doc): ?>
                    <?php
                    $hasDoc      = !empty($doc['doc_id']);
                    $status      = $doc['status'] ?? null;
                    $canReupload = ($status === null || $status === 'rejected');
                    $rawTypes        = $doc['allowed_types'] ?? '';
                    $decoded         = json_decode($rawTypes, true);
                    $allowedTypes    = is_array($decoded) ? $decoded : (empty($rawTypes) ? [] : array_map('trim', explode(',', $rawTypes)));
                    $allowedExtsAttr = implode(',', $allowedTypes);
                    $allowedExtsStr  = implode(', ', $allowedTypes);

                    if ($status === null)       { $badgeClass = 'no-loaded'; $badgeText = 'No cargado'; }
                    elseif ($status === 'pending')  { $badgeClass = 'pending';   $badgeText = 'Pendiente';  }
                    elseif ($status === 'approved') { $badgeClass = 'approved';  $badgeText = 'Aprobado';   }
                    elseif ($status === 'rejected') { $badgeClass = 'rejected';  $badgeText = 'Rechazado';  }
                    else                            { $badgeClass = 'no-loaded'; $badgeText = ucfirst($status); }
                    ?>
                    <div class="initial-doc-slot" data-doc-type-id="<?= $doc['id'] ?>">

                        <!-- Cabecera: nombre + badge -->
                        <div class="initial-doc-header">
                            <span class="initial-doc-name"><?= esc($doc['name']) ?></span>
                            <span class="doc-chip-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                        </div>

                        <!-- Archivo cargado actualmente -->
                        <?php if ($hasDoc): ?>
                            <div class="initial-doc-file">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span><?= esc($doc['original_name'] ?? 'Archivo') ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Nota de rechazo -->
                        <?php if ($status === 'rejected' && !empty($doc['rejection_note'])): ?>
                            <div class="initial-rejection-note">
                                <strong>Motivo del rechazo:</strong> <?= esc($doc['rejection_note']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Acciones -->
                        <div class="initial-doc-actions">
                            <!-- Ver documento (si existe) -->
                            <?php if ($hasDoc): ?>
                                <button type="button"
                                        class="btn btn-sm btn-secondary"
                                        onclick="DocumentViewer.open(<?= $doc['doc_id'] ?>, '<?= site_url('dashboard/documentos/ver/') ?>')">
                                    Ver documento
                                </button>
                            <?php endif; ?>

                            <!-- Zona re-subir (solo si rechazado o no cargado) -->
                            <?php if ($canReupload): ?>
                                <div class="initial-drop-zone"
                                     data-doc-type-id="<?= $doc['id'] ?>"
                                     data-allowed-exts="<?= esc($allowedExtsAttr) ?>">
                                    <div class="initial-drop-placeholder">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3"/></svg>
                                        <span><?= $hasDoc ? 'Arrastra un archivo para reemplazar' : 'Arrastra tu archivo aquí' ?></span>
                                        <?php if (!empty($allowedExtsStr)): ?>
                                            <small><?= esc($allowedExtsStr) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file"
                                           class="initial-file-input"
                                           data-doc-type-id="<?= $doc['id'] ?>"
                                           data-allowed-exts="<?= esc($allowedExtsAttr) ?>"
                                           accept="<?= !empty($allowedTypes) ? '.' . implode(',.', $allowedTypes) : '' ?>"
                                           style="display:none">
                                    <button type="button"
                                            class="btn btn-sm btn-upload"
                                            onclick="this.previousElementSibling.click()">
                                        Seleccionar archivo
                                    </button>
                                </div>
                            <?php elseif ($status === 'pending'): ?>
                                <p class="initial-status-msg">En revisión por el administrador.</p>
                            <?php elseif ($status === 'approved'): ?>
                                <p class="initial-status-msg" style="color:#2ecc71;">✓ Documento aprobado.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay documentos iniciales configurados para este periodo.</p>
        <?php endif; ?>
    </section>

    <!-- CSS para la sección de documentos iniciales -->
    <style>
        .initial-docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
        }
        .initial-doc-slot {
            background: #111;
            border: 1px solid rgba(212,160,74,0.15);
            border-radius: 8px;
            padding: 1rem 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }
        .initial-doc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .initial-doc-name {
            font-family: 'Cormorant Garamond', serif;
            color: #d4a04a;
            font-size: 1rem;
            font-weight: 600;
        }
        .initial-doc-file {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: rgba(245,240,232,0.55);
            overflow: hidden;
        }
        .initial-doc-file span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .initial-rejection-note {
            font-size: 0.8rem;
            background: rgba(231,76,60,0.1);
            border: 1px solid rgba(231,76,60,0.3);
            border-radius: 4px;
            padding: 0.5rem 0.7rem;
            color: #e74c3c;
            line-height: 1.4;
        }
        .initial-doc-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .initial-drop-zone {
            border: 2px dashed rgba(212,160,74,0.3);
            border-radius: 6px;
            padding: 0.85rem;
            text-align: center;
            transition: all 0.2s;
        }
        .initial-drop-zone.drag-over {
            border-color: #d4a04a;
            background: rgba(212,160,74,0.05);
        }
        .initial-drop-zone.has-file {
            border-color: rgba(46,204,113,0.4);
        }
        .initial-drop-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            color: rgba(245,240,232,0.45);
            font-size: 0.78rem;
            margin-bottom: 0.5rem;
        }
        .initial-drop-placeholder small {
            font-size: 0.72rem;
            color: rgba(245,240,232,0.3);
        }
        .initial-status-msg {
            font-size: 0.8rem;
            color: rgba(245,240,232,0.5);
            margin: 0;
        }
        /* reusar badge del dashboard */
        .doc-chip-badge.no-loaded  { background:rgba(245,240,232,0.08); color:rgba(245,240,232,0.45); padding:0.2rem 0.55rem; border-radius:10px; font-size:0.72rem; font-weight:600; text-transform:uppercase; white-space:nowrap; }
        .doc-chip-badge.pending    { background:rgba(243,156,18,0.18);  color:#f39c12;  padding:0.2rem 0.55rem; border-radius:10px; font-size:0.72rem; font-weight:600; text-transform:uppercase; white-space:nowrap; }
        .doc-chip-badge.approved   { background:rgba(46,204,113,0.18);  color:#2ecc71;  padding:0.2rem 0.55rem; border-radius:10px; font-size:0.72rem; font-weight:600; text-transform:uppercase; white-space:nowrap; }
        .doc-chip-badge.rejected   { background:rgba(231,76,60,0.18);   color:#e74c3c;  padding:0.2rem 0.55rem; border-radius:10px; font-size:0.72rem; font-weight:600; text-transform:uppercase; white-space:nowrap; }
        .btn-upload { background:transparent; border:1px solid rgba(212,160,74,0.3); color:#d4a04a; padding:0.35rem 0.75rem; border-radius:4px; cursor:pointer; font-size:0.78rem; }
        .btn-upload:hover { background:rgba(212,160,74,0.08); }
    </style>

    <!-- Script: drag & drop re-subida de documentos iniciales -->
    <script>
    (function() {
        'use strict';
        var uploadUrl = '<?= site_url('dashboard/documentos/inicial/subir') ?>';
        var csrfToken = '<?= csrf_hash() ?>';

        function uploadInitialFile(file, docTypeId, allowedExts, zone) {
            // Validación de extensión client-side
            if (allowedExts) {
                var exts = allowedExts.split(',').map(function(e){ return e.trim().toLowerCase(); });
                var fileExt = file.name.split('.').pop().toLowerCase();
                if (exts.length > 0 && exts.indexOf(fileExt) === -1) {
                    Notify.error('Tipo no permitido', 'Extensiones aceptadas: ' + allowedExts);
                    return;
                }
            }

            // Deshabilitar zona mientras sube
            zone.style.pointerEvents = 'none';
            zone.style.opacity = '0.5';
            var loadingToast = Notify._show('loading', 'Subiendo documento...');

            var fd = new FormData();
            fd.append('file', file);
            fd.append('doc_type_id', docTypeId);
            fd.append('csrf_test_name', csrfToken);

            fetch(uploadUrl, { method: 'POST', body: fd })
                .then(function(r) {
                    return r.json().then(function(data) {
                        return { ok: r.ok, status: r.status, data: data };
                    });
                })
                .then(function(result) {
                    zone.style.pointerEvents = '';
                    zone.style.opacity = '';

                    if (!result.ok || result.data.error) {
                        Notify._updateToast(loadingToast, 'error', result.data.error || 'Error al subir');
                        setTimeout(function(){ Notify._dismiss(loadingToast); }, 5000);
                        return;
                    }

                    // Éxito — recargar la página para reflejar el nuevo estado desde el servidor
                    Notify._updateToast(loadingToast, 'success', 'Documento cargado — actualizando...');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1200);
                })
                .catch(function(err) {
                    zone.style.pointerEvents = '';
                    zone.style.opacity = '';
                    Notify._updateToast(loadingToast, 'error', 'Error de conexión. Intenta de nuevo.');
                    setTimeout(function(){ Notify._dismiss(loadingToast); }, 5000);
                    console.error('uploadInitial error:', err);
                });
        }

        // Bind drop zones
        document.querySelectorAll('.initial-drop-zone').forEach(function(zone) {
            var docTypeId   = zone.getAttribute('data-doc-type-id');
            var allowedExts = zone.getAttribute('data-allowed-exts') || '';

            zone.addEventListener('dragover', function(e){ e.preventDefault(); zone.classList.add('drag-over'); });
            zone.addEventListener('dragleave', function(e){ e.preventDefault(); zone.classList.remove('drag-over'); });
            zone.addEventListener('drop', function(e){
                e.preventDefault(); zone.classList.remove('drag-over');
                var file = e.dataTransfer.files[0];
                if (file) uploadInitialFile(file, docTypeId, allowedExts, zone);
            });
        });

        // Bind file inputs
        document.querySelectorAll('.initial-file-input').forEach(function(input) {
            input.addEventListener('change', function(){
                var file = input.files[0];
                if (!file) return;
                var docTypeId   = input.getAttribute('data-doc-type-id');
                var allowedExts = input.getAttribute('data-allowed-exts') || '';
                var zone        = input.closest('.initial-doc-actions').querySelector('.initial-drop-zone');
                uploadInitialFile(file, docTypeId, allowedExts, zone);
            });
        });
    })();
    </script>
    
    <!-- Timeline del Proceso -->
    <section class="dashboard-card">
        <h2 class="dashboard-card-title">Progreso del Registro</h2>
        <div class="timeline">
            <div class="timeline-step <?= $timelineStep >= 1 ? 'completed' : '' ?> <?= $timelineStep === 1 ? 'active' : '' ?>">
                <h3 class="timeline-step-title">Registro completado</h3>
                <p class="timeline-step-desc">Tu cuenta ha sido creada exitosamente</p>
            </div>
            <div class="timeline-step <?= $timelineStep >= 2 ? 'completed' : '' ?> <?= $timelineStep === 2 ? 'active' : '' ?>">
                <h3 class="timeline-step-title">Correo verificado</h3>
                <p class="timeline-step-desc">Has verificado tu dirección de email</p>
            </div>
            <div class="timeline-step <?= $timelineStep >= 3 ? 'completed' : '' ?> <?= $timelineStep === 3 ? 'active' : '' ?>">
                <h3 class="timeline-step-title">Documentos enviados</h3>
                <p class="timeline-step-desc">Has cargado todos los documentos iniciales</p>
            </div>
            <div class="timeline-step <?= $timelineStep >= 4 ? 'completed' : '' ?> <?= $timelineStep === 4 ? 'active' : '' ?>">
                <h3 class="timeline-step-title">En revisión</h3>
                <p class="timeline-step-desc">Un administrador está revisando tu solicitud</p>
            </div>
            <div class="timeline-step <?= $timelineStep >= 5 ? 'completed' : '' ?> <?= $timelineStep === 5 ? 'active' : '' ?>">
                <h3 class="timeline-step-title">Aprobado</h3>
                <p class="timeline-step-desc">Tu registro ha sido aprobado</p>
            </div>
        </div>
    </section>
    
    <!-- GSAP Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            gsap.from('.timeline-step', {
                opacity: 0,
                y: 30,
                stagger: 0.15,
                ease: 'power2.out',
                duration: 0.5
            });
        });
    </script>
<?php endif; ?>
