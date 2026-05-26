# login-rate-limiting Specification

## Purpose
TBD - created by archiving change auth-login-security. Update Purpose after archive.
## Requirements
### Requirement: Rate limiting — máx 5 intentos fallidos en 15 min
El sistema SHALL verificar en `login_attempts` la cantidad de intentos con `success=0` del mismo `identifier` + `ip_address` en los últimos 15 minutos ANTES de validar credenciales. Si `COUNT >= 5`: retornar mensaje genérico sin revelar si el usuario existe.

#### Scenario: 5 intentos fallidos bloquean el login
- **WHEN** se envían 5 intentos fallidos desde la misma IP con el mismo identifier en menos de 15 minutos
- **THEN** el 6to intento retorna error "Demasiados intentos fallidos. Intenta en 15 minutos." sin verificar contraseña

#### Scenario: Mensaje siempre genérico
- **WHEN** se intenta login con email/username inexistente
- **THEN** el mensaje de error es "Credenciales incorrectas" — nunca "Usuario no encontrado" ni "Email incorrecto"

#### Scenario: Intento exitoso se registra en login_attempts
- **WHEN** login es exitoso
- **THEN** se inserta en `login_attempts` con `success=1` y se actualiza `users.last_login`

---

### Requirement: Búsqueda por email o username
El sistema SHALL buscar el usuario en `users` por `email = $credential OR username = $credential` en una sola query usando `groupStart()/groupEnd()` de CI4 Query Builder.

#### Scenario: Login con email funciona
- **WHEN** se envía el email del usuario como identifier
- **THEN** el sistema encuentra al usuario y verifica la contraseña

#### Scenario: Login con username funciona
- **WHEN** se envía el username del usuario como identifier
- **THEN** el sistema encuentra al usuario y verifica la contraseña

#### Scenario: Usuario suspendido no puede loguearse
- **WHEN** login correcto con credenciales de usuario con `status='suspended'`
- **THEN** retorna error genérico y `logAttempt(success=0)`

