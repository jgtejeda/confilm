## Why

Los usuarios necesitan un mecanismo seguro para recuperar el acceso cuando olvidan su contraseña. El token de reset debe expirar en 1 hora y la respuesta del servidor siempre ser genérica para no revelar si el email existe.

## What Changes

- **NUEVO** `app/app/Controllers/Auth/RecoveryController.php` — métodos: `index()`, `sendLink()`, `resetForm($hash)`, `resetProcess()`
- **NUEVO** `app/app/Views/auth/recovery.php` — formulario de email para solicitar reset
- **NUEVO** `app/app/Views/auth/reset_form.php` — formulario nueva contraseña + confirmación
- **MODIFICAR** `app/app/Config/Routes.php` — agregar rutas en grupo noauth

## Capabilities

### New Capabilities

- `password-recovery`: Flujo completo: solicitar link por email, token 1h, resetear contraseña, limpiar token; respuesta siempre genérica

### Modified Capabilities

(ninguna)

## Impact

- Archivos nuevos: `RecoveryController.php`, `Views/auth/recovery.php`, `Views/auth/reset_form.php`
- Modificados: `Config/Routes.php`
- DB: UPDATE `users.recovery_token`, `users.recovery_exp` (columnas ya existen de P02), UPDATE `password_hash`
- Usa: UserModel, MailService (log stub si P09 no listo)
