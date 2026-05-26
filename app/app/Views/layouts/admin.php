<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Comisión Film</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">CF</div>
            <div>
                <div class="sidebar-brand-text">Comisión Film</div>
                <div class="sidebar-brand-sub">Panel Admin</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="<?= site_url('admin') ?>" class="sidebar-nav-link <?= uri_string() === 'admin' ? 'is-active' : '' ?>">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?= site_url('admin/usuarios') ?>" class="sidebar-nav-link <?= uri_string() === 'admin/usuarios' ? 'is-active' : '' ?>">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Usuarios
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?= site_url('admin/tipos-documento') ?>" class="sidebar-nav-link <?= uri_string() === 'admin/tipos-documento' ? 'is-active' : '' ?>">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Tipos de Documento
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?= site_url('admin/periodos') ?>" class="sidebar-nav-link <?= uri_string() === 'admin/periodos' ? 'is-active' : '' ?>">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Periodos
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?= site_url('admin/notificaciones') ?>" class="sidebar-nav-link <?= uri_string() === 'admin/notificaciones' ? 'is-active' : '' ?>">
                        <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        Notificaciones
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Header -->
    <header class="admin-header">
        <div class="header-user">
            <div class="header-user-avatar">
                <?= strtoupper(substr(session('nombres'), 0, 1)) ?>
            </div>
            <span class="header-user-name"><?= esc(session('nombres')) ?></span>
        </div>
        <a href="<?= site_url('logout') ?>" class="header-logout">Cerrar sesión</a>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <?= $content ?>
    </main>

    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    
    <!-- Notifications -->
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
    
    <!-- PDF.js CDN (3.x legacy build → expone window.pdfjsLib como global) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <!-- Document Viewer -->
    <script src="<?= base_url('assets/js/document-viewer.js') ?>"></script>
    
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>
