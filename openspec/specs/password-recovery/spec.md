# password-recovery Specification

## Purpose
TBD - created by archiving change auth-recovery. Update Purpose after archive.
## Requirements
### Requirement: Solicitud de reset siempre genérica
POST /recuperar SHALL buscar el email en `users`. Si existe: generar `recovery_token=bin2hex(random_bytes(32))`, `recovery_exp=NOW()+1h`, enviar correo (o log). SIEMPRE retornar mensaje genérico: "Si ese correo está registrado, recibirás un link en breve."

#### Scenario: Email existente genera token y envía correo
- **WHEN** POST /recuperar con email registrado
- **THEN** `users.recovery_token` y `users.recovery_exp` se actualizan, se intenta enviar correo, respuesta genérica

#### Scenario: Email inexistente retorna misma respuesta genérica
- **WHEN** POST /recuperar con email no registrado
- **THEN** no se modifica ningún registro en DB y la respuesta es idéntica a la del email existente

---

### Requirement: Reset de contraseña con token válido
GET /reset/(:hash) SHALL buscar por `recovery_token`, verificar `recovery_exp > NOW()`. Si OK: mostrar formulario. POST /reset SHALL validar nueva contraseña (min 8 chars, confirmación), hashear con bcrypt cost 12, actualizar `password_hash`, limpiar `recovery_token=NULL, recovery_exp=NULL`, redirigir a /login.

#### Scenario: Token válido muestra formulario
- **WHEN** GET /reset/{token} con token existente y no expirado
- **THEN** se muestra el formulario de nueva contraseña

#### Scenario: Token expirado muestra error
- **WHEN** GET /reset/{token} con token cuyo `recovery_exp < NOW()`
- **THEN** se muestra error "El link ha expirado. Solicita uno nuevo."

#### Scenario: Reset exitoso limpia token y redirige
- **WHEN** POST /reset con nueva contraseña válida y token correcto
- **THEN** `password_hash` se actualiza con bcrypt cost 12, `recovery_token=NULL`, `recovery_exp=NULL`, redirect a /login

