## ADDED Requirements

### Requirement: Migración CreateUsersTable existe y crea la tabla correctamente
El sistema SHALL disponer de un archivo de migración CI4 en `app/app/Database/Migrations/2025-01-01-000001_CreateUsersTable.php` que al ejecutarse cree la tabla `users` con todos los campos definidos en ARQUITECTURA.md §5.

#### Scenario: Migración corre sin errores
- **WHEN** se ejecuta `php spark migrate`
- **THEN** la tabla `users` existe en la base de datos sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta `php spark migrate:rollback` (siendo la única migración aplicada)
- **THEN** la tabla `users` es eliminada sin errores de FK

### Requirement: Tabla users tiene todos los campos requeridos
La tabla `users` SHALL contener exactamente los campos definidos en ARQUITECTURA.md §5, sin omisiones ni campos adicionales no especificados.

#### Scenario: Campos de identidad presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen las columnas: `id`, `username`, `email`, `phone`, `password_hash`, `nombres`, `apellido_pat`, `apellido_mat`

#### Scenario: Campos de rol y estado presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen `role` ENUM('user','admin','superadmin') DEFAULT 'user' y `status` ENUM('pending','active','rejected','suspended') DEFAULT 'pending'

#### Scenario: Campos de verificación de correo presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen `email_verified TINYINT(1) DEFAULT 0`, `verify_token VARCHAR(100) NULL` y `verify_exp DATETIME NULL`

#### Scenario: Campos de recuperación de contraseña presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen `recovery_token VARCHAR(100) NULL` y `recovery_exp DATETIME NULL`

#### Scenario: Timestamps y último login presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen `last_login DATETIME NULL`, `created_at DATETIME DEFAULT CURRENT_TIMESTAMP` y `updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`

### Requirement: Tabla users tiene los índices correctos
La tabla `users` SHALL tener índices en las columnas de búsqueda frecuente para garantizar performance en queries de autenticación y administración.

#### Scenario: Índices de unicidad presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen UNIQUE KEY en `username` y UNIQUE KEY en `email`

#### Scenario: Índices de búsqueda presentes
- **WHEN** se inspecciona el esquema de `users`
- **THEN** existen INDEX `idx_status` en `status` e INDEX `idx_verify_token` en `verify_token`
