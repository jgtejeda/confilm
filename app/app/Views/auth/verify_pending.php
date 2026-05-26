<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo — Comisión Film</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>

<div class="aurora" aria-hidden="true">
    <div class="orb orb--gold"></div>
    <div class="orb orb--wine"></div>
    <div class="orb orb--slate"></div>
    <div class="orb orb--accent"></div>
</div>

<div class="verify-wrapper">

    <div class="brand" style="opacity:1; animation: none;">
        <div class="brand-icon">CF</div>
        <div class="brand-text">
            <span class="brand-name">Comisión Film</span>
            <span class="brand-sub">México</span>
        </div>
    </div>

    <div class="glass-card" style="max-width:420px; position:relative;">
        <span class="verify-icon">✉️</span>

        <h1 class="panel-title" style="text-align:center;">¡Registro enviado!</h1>

        <p class="verify-email-text">
            Te enviamos dos correos a <strong><?= esc($email) ?></strong>:
        </p>
        <ul style="font-size:13px;color:rgba(245,240,232,0.65);line-height:1.9;padding-left:1.2rem;margin:0 0 18px;">
            <li>📋 Tus <strong>credenciales de acceso</strong> (usuario y contraseña)</li>
            <li>🔗 Un <strong>link para verificar tu correo</strong> y activar tu cuenta</li>
        </ul>
        <p class="verify-email-text" style="font-size:12px;color:rgba(245,240,232,0.4);">
            Revisa también tu carpeta de spam si no lo ves en unos minutos.
        </p>

        <?php if (session()->get('success')): ?>
            <div class="alert alert--success"><?= esc(session()->get('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->get('error')): ?>
            <div class="alert alert--error"><?= esc(session()->get('error')) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('verificar/reenviar') ?>">
            <?= csrf_field() ?>
            <button type="submit" id="btn-reenviar" class="btn-primary" disabled>
                Reenviar correo
            </button>
        </form>

        <a class="auth-link" href="<?= site_url('login') ?>">Volver al inicio de sesión</a>
    </div>

</div>

<script>
(function () {
    var s   = 60;
    var btn = document.getElementById('btn-reenviar');
    function tick() {
        if (s > 0) {
            btn.disabled    = true;
            btn.textContent = 'Reenviar (' + s + 's)';
            s--;
            setTimeout(tick, 1000);
        } else {
            btn.disabled    = false;
            btn.textContent = 'Reenviar correo de verificación';
        }
    }
    tick();
})();
</script>

</body>
</html>
