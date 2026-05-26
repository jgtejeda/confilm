# user-management Specification

## Purpose
TBD - created by archiving change admin-users. Update Purpose after archive.
## Requirements
### Requirement: Edición de usuario sin username
El admin SHALL poder editar: nombres, apellido_pat, apellido_mat, phone, email, status, role. El campo `username` NO SHALL aparecer en el formulario de edición ni en los campos procesados por update().

#### Scenario: username no es editable
- **WHEN** admin edita usuario y envía un username diferente en el POST
- **THEN** `users.username` no cambia en la DB — no se procesa ese campo

#### Scenario: Email duplicado es rechazado al editar
- **WHEN** admin intenta cambiar email a uno que ya usa otro usuario
- **THEN** validación `is_unique[users.email,id,{id}]` falla y retorna error

#### Scenario: Cambio de email re-verifica el correo
- **WHEN** admin cambia el email de un usuario
- **THEN** `email_verified=0`, se genera nuevo verify_token y verify_exp, se envía correo de verificación

---

### Requirement: Reset de contraseña por admin
`UserController::resetPassword($id)` SHALL generar nueva contraseña con PasswordGenerator, hashear con bcrypt cost 12, actualizar en DB, y enviar correo de bienvenida con la nueva contraseña.

#### Scenario: Reset genera contraseña nueva y la envía
- **WHEN** admin hace POST /admin/usuarios/{id}/reset-password
- **THEN** `users.password_hash` se actualiza con bcrypt cost 12 y el usuario recibe correo con la nueva contraseña

---

### Requirement: Suspensión de usuario bloquea el login
Si admin cambia `status='suspended'`, el usuario al intentar login recibe error genérico (LoginController::process() verifica status='active').

#### Scenario: Usuario suspendido no puede loguearse
- **WHEN** admin cambia status a 'suspended' y el usuario intenta login
- **THEN** LoginController::process() detecta status != 'active' y retorna error

