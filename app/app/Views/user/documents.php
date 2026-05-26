<h1 style="color:#d4a04a;font-size:1.8rem;margin-bottom:0.5rem;font-family:'Cormorant Garamond',serif;">Documentos Complementarios</h1>
<p class="text-muted" style="margin-bottom:2rem;">Carga los documentos complementarios requeridos para tu inscripción</p>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error"><?= esc(session('error')) ?></div>
<?php endif; ?>

<?php if (!$hasInscription): ?>
    <div class="card">
        <p class="text-muted">No tienes una inscripción activa. Debes inscribirte en un periodo para cargar documentos complementarios.</p>
    </div>
<?php elseif (empty($docTypes)): ?>
    <div class="card">
        <p class="text-muted">No hay documentos complementarios configurados para tu periodo.</p>
    </div>
<?php else: ?>
    <div class="doc-grid">
        <?php foreach ($docTypes as $doc): ?>
            <?php
            $hasDoc = !empty($doc['doc_id']);
            $status = $doc['doc_status'] ?? null;
            $rawTypes = $doc['allowed_types'] ?? '';
            $decoded = json_decode($rawTypes, true);
            $allowedTypes = is_array($decoded) ? $decoded : (empty($rawTypes) ? [] : array_map('trim', explode(',', $rawTypes)));
            $allowedExtsStr  = implode(', ', $allowedTypes);
            $allowedExtsAttr = implode(',', $allowedTypes);
            ?>
            <div class="doc-slot" data-doc-type-id="<?= $doc['id'] ?>">
                <div class="doc-slot-header">
                    <h3 class="doc-slot-name"><?= esc($doc['name']) ?></h3>
                    <?php if ($hasDoc): ?>
                        <span class="doc-status-badge badge-<?= esc($status ?? 'pending') ?>">
                            <?= esc(ucfirst($status ?? 'pending')) ?>
                        </span>
                    <?php else: ?>
                        <span class="doc-status-badge badge-no-loaded">No cargado</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($doc['description'])): ?>
                    <p class="doc-slot-desc text-muted"><?= esc($doc['description']) ?></p>
                <?php endif; ?>

                <?php if (($doc['doc_status'] ?? null) === 'rejected' && !empty($doc['rejection_note'])): ?>
                    <div class="doc-rejection-note">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:2px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span><strong>Motivo del rechazo:</strong> <?= esc($doc['rejection_note']) ?></span>
                    </div>
                <?php endif; ?>

                <p class="doc-slot-types">
                    <strong>Tipos aceptados:</strong> <?= esc($allowedExtsStr ?: 'Consultar') ?>
                </p>

                <!-- Zona drag & drop -->
                <div class="doc-drop-zone <?= $hasDoc ? 'has-document' : '' ?>"
                     data-doc-type-id="<?= $doc['id'] ?>"
                     data-allowed-exts="<?= esc($allowedExtsAttr) ?>">
                    <div class="drop-placeholder">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m0 0l-3 3m0 0l-3-3m0 0l-3 3"></path>
                        </svg>
                        <span><?= $hasDoc ? 'Arrastra otro archivo para reemplazar' : 'Arrastra tu archivo aquí' ?></span>
                    </div>
                    <input type="file"
                           class="doc-file-input"
                           data-doc-type-id="<?= $doc['id'] ?>"
                           data-allowed-exts="<?= esc($allowedExtsAttr) ?>"
                           accept="<?= !empty($allowedTypes) ? '.' . implode(',.', $allowedTypes) : '' ?>"
                           style="display:none;">
                    <button type="button" class="btn btn-sm btn-upload" onclick="this.previousElementSibling.click()">
                        Seleccionar archivo
                    </button>
                </div>

                <!-- Botón Ver (habilitado si tiene doc) -->
                <?php if ($hasDoc): ?>
                    <button type="button"
                            class="btn btn-sm btn-secondary doc-view-btn"
                            data-doc-id="<?= $doc['doc_id'] ?>"
                            onclick="DocumentViewer.open(<?= $doc['doc_id'] ?>, '<?= site_url('dashboard/documentos/ver/') ?>')">
                        Ver documento
                    </button>
                <?php else: ?>
                    <button type="button"
                            class="btn btn-sm btn-secondary doc-view-btn"
                            disabled
                            style="opacity:0.5;cursor:not-allowed;">
                        Ver documento
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Botón Enviar para revisión -->
    <div class="doc-submit-section">
        <?php
        $allLoaded = true;
        foreach ($docTypes as $doc) {
            if (empty($doc['doc_id'])) {
                $allLoaded = false;
                break;
            }
        }
        ?>
        <button type="button"
                id="submit-docs-btn"
                class="btn btn-primary <?= $allLoaded ? '' : 'btn-disabled' ?>"
                <?= $allLoaded ? '' : 'disabled' ?>>
            Enviar para revisión
        </button>
        <p class="text-muted submit-hint">
            <?= $allLoaded ? 'Todos los documentos están cargados. Puedes enviarlos para revisión.' : 'Carga todos los documentos complementarios antes de enviar.' ?>
        </p>
    </div>

    <!-- Inline CSS para la vista -->
    <style>
        .doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .doc-slot {
            background: #1a1a1a;
            border: 1px solid rgba(212, 160, 74, 0.15);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .doc-slot-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .doc-slot-name {
            color: #d4a04a;
            font-size: 1.1rem;
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
        }

        .doc-slot-desc {
            font-size: 0.85rem;
            margin: 0 0 0.5rem 0;
            color: rgba(245, 240, 232, 0.6);
        }

        .doc-slot-types {
            font-size: 0.8rem;
            color: rgba(245, 240, 232, 0.5);
            margin: 0 0 1rem 0;
        }

        .doc-drop-zone {
            border: 2px dashed rgba(212, 160, 74, 0.3);
            border-radius: 6px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
        }

        .doc-drop-zone.drag-over {
            border-color: #d4a04a;
            background: rgba(212, 160, 74, 0.05);
        }

        .doc-drop-zone.has-document {
            border-color: rgba(46, 204, 113, 0.3);
        }

        .drop-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: rgba(245, 240, 232, 0.5);
            font-size: 0.85rem;
        }

        .drop-placeholder svg {
            color: rgba(212, 160, 74, 0.5);
        }

        .doc-status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-pending {
            background: rgba(243, 156, 18, 0.2);
            color: #f39c12;
        }

        .badge-approved {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .badge-rejected {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .badge-no-loaded {
            background: rgba(245, 240, 232, 0.1);
            color: rgba(245, 240, 232, 0.5);
        }

        .doc-rejection-note {
            display: flex;
            align-items: flex-start;
            gap: 0.45rem;
            background: rgba(231, 76, 60, 0.08);
            border: 1px solid rgba(231, 76, 60, 0.3);
            border-radius: 5px;
            padding: 0.55rem 0.75rem;
            margin: 0 0 0.75rem 0;
            font-size: 0.82rem;
            color: #f08080;
            line-height: 1.5;
        }

        .doc-rejection-note svg {
            color: #e74c3c;
        }

        .btn-upload {
            background: transparent;
            border: 1px solid rgba(212, 160, 74, 0.3);
            color: #d4a04a;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .btn-upload:hover {
            background: rgba(212, 160, 74, 0.1);
        }

        .doc-view-btn {
            width: 100%;
        }

        .doc-submit-section {
            text-align: center;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(212, 160, 74, 0.15);
        }

        .btn-primary {
            background: #d4a04a;
            color: #0a0a0a;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover:not(:disabled) {
            background: #c4933a;
        }

        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .submit-hint {
            font-size: 0.85rem;
            margin-top: 0.75rem;
        }
    </style>

    <!-- document-upload.js -->
    <script src="<?= base_url('assets/js/document-upload.js') ?>"></script>
    <script>
        var baseUrl = '<?= site_url() ?>';
        var csrfToken = '<?= csrf_hash() ?>';

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof DocumentUpload !== 'undefined') {
                DocumentUpload.init(baseUrl, csrfToken);
            }
        });
    </script>
<?php endif; ?>
