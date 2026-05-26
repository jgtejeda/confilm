# user-registration Specification

## Purpose
TBD - created by archiving change auth-register. Update Purpose after archive.
## Requirements
### Requirement: Registro bloqueado sin periodo activo
El sistema SHALL verificar si existe un periodo activo (`active=1 AND start_date<=NOW() AND end_date>=NOW()`) al cargar GET /registro y al recibir POST /registro. Si no existe, SHALL mostrar la vista `auth/no_period.php` con mensaje "No hay convocatoria abierta en este momento" y no procesar el formulario.

#### Scenario: GET /registro sin periodo activo muestra aviso
- **WHEN** no existe ningún periodo con `active=1` y fechas vigentes
- **THEN** `RegisterController::index()` retorna la vista `auth/no_period.php` con mensaje "No hay convocatoria abierta en este momento"

#### Scenario: POST /registro sin periodo activo es rechazado
- **WHEN** se envía POST /registro y no hay periodo activo en ese momento
- **THEN** el controller retorna error y no crea ningún registro en DB ni sube archivos a S3

---

### Requirement: Validación de datos personales
El sistema SHALL validar con CI4 Validation: `nombres` (required, min_length 2, max_length 100), `apellido_pat` (required, min_length 2, max_length 80), `apellido_mat` (optional, max_length 80), `phone` (required, regex_match /^\d{10}$/), `email` (required, valid_email, is_unique[users.email]).

#### Scenario: Email duplicado es rechazado
- **WHEN** se envía POST /registro con un email ya existente en la tabla `users`
- **THEN** CI4 Validation falla con error de unicidad y no se crea ningún registro

#### Scenario: Teléfono inválido es rechazado
- **WHEN** se envía `phone` con menos de 10 dígitos o con letras
- **THEN** CI4 Validation retorna error y no se procesa el registro

---

### Requirement: Creación de usuario con credenciales generadas
El sistema SHALL generar `username` con `UsernameGenerator::generate(nombres, apellido_pat)` (1ª letra nombre + apellido_pat sin acentos + _ + 4 chars aleatorios, anti-colisión 10 intentos) y `password` con `PasswordGenerator::generate()` (12 chars, 2 may + 2 min + 2 dígitos + 2 símbolos + 4 random). El `password_hash` SHALL usar `password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12])`. Los campos `email_verified=0`, `status='pending'`, `verify_token=bin2hex(random_bytes(32))`, `verify_exp=NOW()+24h` SHALL ser seteados al crear el usuario.

#### Scenario: Username generado sigue el patrón correcto
- **WHEN** se registra un usuario con nombres="Juan" y apellido_pat="González"
- **THEN** el username generado sigue el patrón `jgonzalez_XXXX` donde XXXX son 4 caracteres alfanuméricos, sin acentos

#### Scenario: Password cumple composición mínima
- **WHEN** PasswordGenerator::generate() produce una contraseña
- **THEN** la contraseña tiene exactamente 12 chars y contiene al menos 2 mayúsculas, 2 minúsculas, 2 dígitos y 2 símbolos de `!@#$%^&*`

#### Scenario: Password hasheada con bcrypt cost 12
- **WHEN** se inserta el usuario en la tabla `users`
- **THEN** el campo `password_hash` contiene un hash bcrypt (empieza con `$2y$12$`), nunca texto plano

---

### Requirement: Post-registro redirige a verificación pendiente
Tras un registro exitoso el sistema SHALL redirigir a `site_url('verificar-pendiente')`. SHALL intentar enviar correo de verificación y correo de bienvenida; si MailService no está disponible, SHALL loggear con `log_message('info', ...)` y continuar sin error fatal.

#### Scenario: Registro exitoso redirige a verificar-pendiente
- **WHEN** POST /registro se completa con éxito (usuario creado, archivos en S3, inscripción creada)
- **THEN** el servidor retorna redirect 302 a `http://localhost/comisionfilm/verificar-pendiente`

