## Why

El sistema necesita un usuario admin inicial para poder acceder al panel y configurar periodos y tipos de documento. El seeder crea este usuario de forma segura con bcrypt y sin almacenar la contraseña en texto plano.

## What Changes

- Crear `app/app/Database/Seeds/AdminUserSeeder.php`
- El seeder usa `password_hash()` con `PASSWORD_BCRYPT` cost 12 — nunca texto plano
- Usuario: role='admin', status='active', email_verified=1 (el admin no necesita verificar correo)
- La contraseña inicial debe definirse como constante en el seeder (hardcodeada para dev, documentada para cambio en prod)

## Capabilities

### New Capabilities
- `admin-user-seeder`: Seeder CI4 que crea el usuario admin con bcrypt cost 12 y email_verified=1

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Seeds/AdminUserSeeder.php`
- Depende de: tabla `users` creada por `migration-users`
- CRÍTICO: NO almacenar la contraseña en texto plano en ningún campo de la BD
