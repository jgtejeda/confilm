<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-label">Total Usuarios</div>
        <span class="stat-value" data-value="<?= $stats['total_users'] ?>">0</span>
        <div class="stat-card-description">Usuarios registrados en el sistema</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-label">Pendientes de Revisión</div>
        <span class="stat-value" data-value="<?= $stats['pending_review'] ?>">0</span>
        <div class="stat-card-description">Usuarios esperando aprobación</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-label">Documentos Pendientes</div>
        <span class="stat-value" data-value="<?= $stats['docs_pending'] ?>">0</span>
        <div class="stat-card-description">Documentos por revisar</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-label">Inscripciones Aprobadas</div>
        <span class="stat-value" data-value="<?= $stats['inscriptions_approved'] ?>">0</span>
        <div class="stat-card-description">Inscripciones confirmadas</div>
    </div>
</div>
