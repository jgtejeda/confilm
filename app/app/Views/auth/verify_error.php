<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — Comisión Film</title>
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

    <div class="brand" style="opacity:1; animation:none;">
        <div class="brand-icon">CF</div>
        <div class="brand-text">
            <span class="brand-name">Comisión Film</span>
            <span class="brand-sub">México</span>
        </div>
    </div>

    <div class="glass-card" style="max-width:420px; position:relative;">
        <span class="verify-icon"><?= $show_resend ? '⏰' : '⚠️' ?></span>

        <h1 class="panel-title" style="text-align:center;"><?= esc($title) ?></h1>

        <p class="verify-email-text"><?= esc($message) ?></p>

        <?php if ($show_resend): ?>
            <form method="POST" action="<?= site_url('verificar/reenviar') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-primary">Reenviar correo de verificación</button>
            </form>
        <?php endif; ?>

        <a class="auth-link" href="<?= site_url('login') ?>">Volver al inicio de sesión</a>
    </div>

</div>

</body>
</html>
