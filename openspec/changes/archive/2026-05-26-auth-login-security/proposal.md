## Why

LoginController ya tiene un stub básico de login (P04). Necesitamos completarlo con rate limiting real, búsqueda por email O username, registro de intentos en `login_attempts`, regeneración de sesión, y los tres filtros (AuthFilter, AdminFilter, NoAuthFilter) completos y registrados. Sin esto el sistema no tiene seguridad en el login ni protección de rutas.

## What Changes

- **MODIFICAR** `app/app/Controllers/Auth/LoginController.php` — reemplazar stub por `process()` completo con rate limiting, password_verify, logAttempt, session regenerate, redirect por role
- **NUEVO** `app/app/Filters/AdminFilter.php` — verifica role in ['admin','superadmin']
- **NUEVO** `app/app/Filters/NoAuthFilter.php` — redirige usuarios ya logueados según su role
- **MODIFICAR** `app/app/Config/Filters.php` — registrar los tres aliases: 'auth', 'admin', 'noauth'
- **MODIFICAR** `app/app/Config/Routes.php` — aplicar filters 'noauth' a rutas de auth, 'auth' al dashboard, 'admin' al panel admin

## Capabilities

### New Capabilities

- `login-rate-limiting`: Máx 5 intentos fallidos por (ip_address + identifier) en 15 min; registro en `login_attempts`; mensaje genérico siempre
- `session-filters`: AdminFilter y NoAuthFilter completos y registrados; rutas protegidas correctamente

### Modified Capabilities

- `auth-filter-verified`: LoginController::process() guarda `email_verified` en sesión (refuerza lo de P06)

## Impact

- Modificados: `LoginController.php`, `Config/Filters.php`, `Config/Routes.php`
- Nuevos: `Filters/AdminFilter.php`, `Filters/NoAuthFilter.php`
- DB: INSERT en `login_attempts` (tabla ya existe de P02), UPDATE `users.last_login`
- Sin cambios en schema, S3, vistas ni migraciones
