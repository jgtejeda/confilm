<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta — Comisión Film</title>
    <style>
        body { margin: 0; padding: 2rem; background: #1a1a2e; font-family: sans-serif; color: white; }
        .card { background: #16213e; padding: 2rem; border-radius: 8px; max-width: 500px; margin: 0 auto; }
        h1 { margin: 0 0 1rem 0; color: #e94560; }
        p { color: #ccc; margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: #aaa; }
        input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; background: #0f3460; border: 1px solid #533483; color: white; border-radius: 4px; }
        button { width:

100%; padding: 0.75rem; background: #e94560; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .link { display: block; text-align: center; margin-top: 1rem; color: #00d9ff; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Crear Cuenta</h1>
        <p>Completa tus datos para registrarte</p>
        <form method="POST" action="<?= site_url('registro') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <label>Nombre</label>
            <input type="text" name="nombres" placeholder="Tu nombre" required>
            <label>Apellido</label>
            <input type="text" name="apellido_pat" placeholder="Apellido" required>
            <label>Teléfono (10 dígitos)</label>
            <input type="tel" name="phone" placeholder="5512345678" required>
            <label>Correo</label>
            <input type="email" name="email" placeholder="tu@email.com" required>
            <button type="submit">Crear cuenta</button>
            <a href="<?= site_url('login') ?>" class="link">¿Ya tienes cuenta? Inicia sesión</a>
        </form>
    </div>
</body>
</html>
