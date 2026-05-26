<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Comisión Film</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/user.css') ?>">
</head>
<body>
    <!-- Navbar Horizontal -->
    <nav class="user-navbar">
        <a href="<?= site_url('dashboard') ?>" class="user-navbar-brand">Comisión Film</a>
        
        <div class="user-navbar-links">
            <a href="<?= site_url('dashboard') ?>" class="user-navbar-link <?= uri_string() === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
            <a href="<?= site_url('dashboard/documentos') ?>" class="user-navbar-link <?= strpos(uri_string(), 'dashboard/documentos') === 0 ? 'is-active' : '' ?>">Mis Documentos</a>
        </div>
        
        <div class="user-navbar-user">
            <a href="<?= site_url('dashboard/notificaciones') ?>" class="user-navbar-notif">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span id="notif-badge">0</span>
            </a>
            
            <div class="user-navbar-avatar"><?= strtoupper(substr(session('nombres'), 0, 1)) ?></div>
            <span class="user-navbar-name"><?= esc(session('nombres')) ?></span>
            
            <a href="<?= site_url('logout') ?>" class="user-navbar-logout">Salir</a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="user-main">
        <?= $content ?>
    </main>
    
    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    
    <!-- PDF.js CDN (3.x legacy build → expone window.pdfjsLib como global) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <!-- Notifications JS -->
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
    
    <!-- Document Viewer JS -->
    <script src="<?= base_url('assets/js/document-viewer.js') ?>"></script>
    
    <!-- Polling de notificaciones -->
    <script>
        var baseUrl = '<?= site_url() ?>';
        
        // Polling cada 30 segundos
        setInterval(function() {
            fetch(baseUrl + 'dashboard/notificaciones/count')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var badge = document.getElementById('notif-badge');
                    badge.textContent = data.count;
                    if (data.count > 0) {
                        badge.classList.add('has-notifs');
                    } else {
                        badge.classList.remove('has-notifs');
                    }
                })
                .catch(function(err) {
                    console.error('Error fetching notifications:', err);
                });
        }, 30000);
        
        // Carga inicial del badge
        (function() {
            fetch(baseUrl + 'dashboard/notificaciones/count')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var badge = document.getElementById('notif-badge');
                    badge.textContent = data.count;
                    if (data.count > 0) {
                        badge.classList.add('has-notifs');
                    } else {
                        badge.classList.remove('has-notifs');
                    }
                });
        })();
    </script>
</body>
</html>
