## Why

Después del registro el usuario queda con `email_verified=0`. Necesitamos el flujo de confirmación por token para verificar que el correo es real antes de permitir acceso al dashboard. Sin esto, cualquier persona puede registrarse con un email falso.

## What Changes

- **NUEVO** `app/app/Controllers/Auth/VerifyController.php` — métodos: `pending()`, `confirm($token)`, `resend()`
- **NUEVO** `app/app/Views/auth/verify_pending.php` — pantalla "Revisa tu correo" con botón reenviar y cuenta regresiva 60s
- **MODIFICAR** `app/app/Filters/AuthFilter.php` — agregar verificación `email_verified=1` además de sesión activa; redirigir a verificar-pendiente si no verificado
- **MODIFICAR** `app/app/Config/Routes.php` — agregar rutas de verificación

## Capabilities

### New Capabilities

- `email-verification`: Flujo completo de verificación: confirm por token (24h), reenvío con límite 3/hora, pantalla de espera con cuenta regresiva

### Modified Capabilities

- `auth-filter-verified`: AuthFilter actualizado para verificar `email_verified=1` en sesión además de `user_id`

## Impact

- Nuevos archivos: `Controllers/Auth/VerifyController.php`, `Views/auth/verify_pending.php`
- Modificados: `Filters/AuthFilter.php`, `Config/Routes.php`
- DB: UPDATE `users` (email_verified, verify_token, verify_exp) — columnas ya existen de P02
- Usa: UserModel (ya existe), MailService (log stub si P09 no está listo)
- Sin cambios en schema, migraciones, S3, ni otros módulos
