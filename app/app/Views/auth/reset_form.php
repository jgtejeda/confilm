<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña — Comisión Film</title>
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
        .alert--error { background: #533483; border: 1px solid #e94560; color: #e94560; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Nueva Contraseña</h1>
        <p>Ingresa tu nueva contraseña.</p>

        <?php if (session()->get('errors')): ?>
            <div class="alert alert--error">
                <?php foreach ((array) session('errors') as $e): ?>
                    <div><?= esc($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('reset') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">
            <label>Nueva contraseña</label>
            <input type="password" name="new_password" placeholder="Mínimo 8 caracteres" required minlength="8">
            <label>Confirmar contraseña</label>
            <input type="password" name="confirm_password" placeholder="Repite tu contraseña" required>
            <button type="submit">Cambiar contraseña</button>
        </form>

        <a href="<?= site_url('login') ?>" class="link">Volver al login</a>
    </div>
</body>
</html>
