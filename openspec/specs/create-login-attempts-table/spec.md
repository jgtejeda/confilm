# create-login-attempts-table Specification

## Purpose
TBD - created by archiving change migration-login-attempts. Update Purpose after archive.
## Requirements
### Requirement: Migración CreateLoginAttemptsTable crea la tabla correctamente
El sistema SHALL disponer del archivo `2025-01-01-000008_CreateLoginAttemptsTable.php` sin FK a otras tablas.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate`
- **THEN** la tabla `login_attempts` existe sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de login_attempts
- **THEN** la tabla se elimina sin errores

### Requirement: Tabla registra intentos con identifier e ip_address
La tabla SHALL tener: `identifier VARCHAR(150) NOT NULL`, `ip_address VARCHAR(45) NOT NULL`, `success TINYINT(1) DEFAULT 0`, `attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP`.

#### Scenario: Registro de intento fallido
- **WHEN** se inserta un intento con success=0
- **THEN** el registro se guarda con attempted_at automático

#### Scenario: Registro de intento exitoso
- **WHEN** se inserta un intento con success=1
- **THEN** el registro se guarda correctamente

### Requirement: Índices optimizan la query de rate limiting
La tabla SHALL tener INDEX en `(identifier, ip_address)` e INDEX en `attempted_at`.

#### Scenario: Índices presentes
- **WHEN** se ejecuta `SHOW INDEX FROM login_attempts`
- **THEN** aparecen idx_identifier_ip y idx_attempted_at

