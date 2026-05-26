<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña — Comisión Film</title>
    <style>
        body { margin: 0; padding: 2rem; background: #1a1a2e; font-family: sans-serif; color: white; }
        .card { background: #16213e; padding: 2rem; border-radius: 8px; max-width: 500px; margin: 0 auto; }
        h1 { margin: 0 0 1rem 0; color: #e94560; }
        p { color: #ccc; margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: #aaa; }
        input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; background: #0f3460; border: 1px solid #533483; color: white; border-radius: 4px; }
        button { width: 100%; padding: 0.75rem; background: #e94560; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .link { display: block; text-align: center; margin-top: 1rem; color: #00d9ff; }
        .alert { padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert--info { background: #0f3460; border: 1px solid #00d9ff; color: #00d9ff; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Recuperar Contraseña</h1>
        <p>Ingresa tu correo electrónico y te enviaremos un link para restablecer tu contraseña.</p>

        <?php if (session()->get('info')): ?>
            <div class="alert alert--info"><?= esc(session()->get('info')) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('recuperar') ?>">
            <?= csrf_field() ?>
            <label>Correo electrónico</label>
            <input type="email" name="email" placeholder="tu@email.com" required>
            <button type="submit">Enviar link</button>
        </form>

        <a href="<?= site_url('login') ?>" class="link">Volver al login</a>
    </div>
</body>
</html>
