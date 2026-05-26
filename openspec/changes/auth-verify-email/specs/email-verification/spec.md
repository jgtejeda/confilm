## ADDED Requirements

### Requirement: Confirmación de token válido
El sistema SHALL buscar el usuario por `verify_token`, verificar que `verify_exp > NOW()` y que `email_verified=0`. Si válido: `UPDATE users SET email_verified=1, verify_token=NULL, verify_exp=NULL`. Redirigir a `site_url('login')` con mensaje "Correo verificado. Ya puedes iniciar sesión".

#### Scenario: Token válido verifica el correo
- **WHEN** GET /verificar/{token} con token existente y no expirado
- **THEN** `users.email_verified` se actualiza a 1, `verify_token` y `verify_exp` quedan NULL, redirige a /login con mensaje de éxito

#### Scenario: Token expirado muestra error con opción reenviar
- **WHEN** GET /verificar/{token} con token cuyo `verify_exp < NOW()`
- **THEN** retorna vista con error "El link de verificación ha expirado" y botón para reenviar

#### Scenario: Token inexistente retorna 404 o error
- **WHEN** GET /verificar/{token} con token que no existe en ningún usuario
- **THEN** retorna vista de error "Link inválido"

---

### Requirement: Pantalla de espera y reenvío
`VerifyController::pending()` SHALL retornar la vista `auth/verify_pending.php` mostrando el email del usuario (de sesión) y un botón "Reenviar correo" deshabilitado con cuenta regresiva de 60 segundos en JS. El botón hace POST a `/verificar/reenviar`.

#### Scenario: Vista verify_pending muestra email enmascarado
- **WHEN** usuario autenticado con email_verified=0 accede a GET /verificar-pendiente
- **THEN** la vista muestra el email de sesión (o enmascarado: `j***@dominio.com`) y el botón de reenvío

#### Scenario: Botón reenviar habilitado después de 60s
- **WHEN** la página carga y transcurren 60 segundos
- **THEN** el botón "Reenviar correo" se habilita (JS cuenta regresiva)

---

### Requirement: Reenvío con límite de 3 por hora
`VerifyController::resend()` SHALL verificar que el usuario no ha superado 3 reenvíos en la última hora. Si OK: regenerar `verify_token` y `verify_exp`, enviar correo (o log stub), retornar mensaje de éxito. Si límite superado: retornar error "Límite de reenvíos alcanzado. Intenta en una hora."

#### Scenario: Reenvío exitoso regenera token y expira en 24h
- **WHEN** POST /verificar/reenviar con usuario que tiene < 3 reenvíos en la hora
- **THEN** se genera nuevo `verify_token=bin2hex(random_bytes(32))` y `verify_exp=NOW()+24h`, se intenta enviar correo

#### Scenario: 4to reenvío en menos de 1 hora es rechazado
- **WHEN** POST /verificar/reenviar y el usuario ya hizo 3 reenvíos en la última hora
- **THEN** retorna error "Límite de reenvíos alcanzado" sin regenerar token ni enviar correo
