## Why

El admin necesita ver, editar y gestionar todos los datos de los usuarios: cambiar status, role, datos personales, y hacer reset de contraseña. El username no es editable (es el identificador único del sistema).

## What Changes

- **NUEVO** `Controllers/Admin/UserController.php` — index, detail, edit, update, changeStatus, resetPassword, validateInscription
- **NUEVO** `views/admin/users/index.php`, `detail.php`, `edit.php`

## Capabilities

### New Capabilities

- `user-management`: Lista paginada con filtros, detalle completo, edición (sin username), cambio de status/role, reset de contraseña con PasswordGenerator

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `Admin/UserController.php`, 3 vistas en `admin/users/`
- DB: UPDATE en `users` — tabla existe (P02), UserModel existe (P02)
- Usa MailService (P09) para correo de nueva contraseña
- Rutas ya en Routes.php (P07)
