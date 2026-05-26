## ADDED Requirements

### Requirement: AdminUserSeeder crea el usuario admin con contraseña hasheada
El sistema SHALL disponer de `app/app/Database/Seeds/AdminUserSeeder.php` que inserta el usuario admin usando `password_hash()` con bcrypt cost 12. La contraseña en texto plano NO SHALL almacenarse en la base de datos.

#### Scenario: Seeder ejecuta sin errores
- **WHEN** se ejecuta `php spark db:seed AdminUserSeeder`
- **THEN** el usuario admin aparece en la tabla users sin errores

#### Scenario: Contraseña almacenada como hash bcrypt
- **WHEN** se inspecciona el campo password_hash del admin en la BD
- **THEN** el valor empieza con '$2y$12$' (formato bcrypt cost 12) — nunca texto plano

#### Scenario: Seeder es idempotente
- **WHEN** se ejecuta AdminUserSeeder dos veces
- **THEN** no se crea un usuario duplicado ni lanza error (usa verificación de existencia)

### Requirement: Usuario admin tiene los flags correctos desde el inicio
El usuario admin SHALL tener: email_verified=1, status='active', role='admin' para poder hacer login inmediatamente sin verificación de correo.

#### Scenario: Admin puede hacer login inmediatamente
- **WHEN** el admin usa sus credenciales en el formulario de login
- **THEN** el login es exitoso sin pasar por el flujo de verificación de correo

#### Scenario: Flags de acceso correctos
- **WHEN** se inspecciona el registro del admin en users
- **THEN** email_verified=1, status='active', role='admin'
