## Why

El sistema necesita la tabla `users` como base de toda la estructura de autenticación y relaciones FK. Es la primera migración del proyecto y no depende de ninguna otra tabla.

## What Changes

- Crear migración CI4 `2025-01-01-000001_CreateUsersTable.php` en `app/app/Database/Migrations/`
- La tabla incluye campos para autenticación estándar, verificación de correo electrónico (`verify_token`, `verify_exp`) y recuperación de contraseña (`recovery_token`, `recovery_exp`)
- Soporta roles (`user`, `admin`, `superadmin`) y estados (`pending`, `active`, `rejected`, `suspended`)
- Índices en email, username, status y verify_token para queries eficientes

## Capabilities

### New Capabilities
- `create-users-table`: Migración CI4 que crea la tabla `users` con todos los campos requeridos por ARQUITECTURA.md §5, incluyendo verify_token/verify_exp para el flujo de verificación de correo

### Modified Capabilities
<!-- ninguna — tabla nueva -->

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000001_CreateUsersTable.php`
- Habilita la creación de las migraciones siguientes: `periods`, `document_types`, `documents`, `inscriptions`, `notifications` (todas tienen FK a `users`)
- Sin dependencias de otras migraciones — debe ser la primera en ejecutarse
