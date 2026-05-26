# create-ci-sessions-table Specification

## Purpose
TBD - created by archiving change migration-ci-sessions. Update Purpose after archive.
## Requirements
### Requirement: Migración CreateCiSessionsTable crea la tabla con estructura exacta de CI4
El sistema SHALL disponer del archivo `2025-01-01-000009_CreateCiSessionsTable.php` que crea `ci_sessions` con la estructura EXACTA requerida por CodeIgniter 4 DatabaseHandler.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate`
- **THEN** la tabla `ci_sessions` existe sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de ci_sessions
- **THEN** la tabla se elimina sin errores

### Requirement: Estructura exacta compatible con CI4 DatabaseHandler
La tabla SHALL tener exactamente: id VARCHAR(128) NOT NULL PRIMARY KEY, ip_address VARCHAR(45) NOT NULL, timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP, data BLOB NOT NULL.

#### Scenario: Columna id es PRIMARY KEY de tipo VARCHAR
- **WHEN** se inspecciona con `SHOW CREATE TABLE ci_sessions`
- **THEN** id es VARCHAR(128) NOT NULL y es la PRIMARY KEY (no AUTO_INCREMENT)

#### Scenario: Columna data es BLOB
- **WHEN** se inspecciona `DESCRIBE ci_sessions`
- **THEN** data es de tipo blob NOT NULL

#### Scenario: CI4 puede escribir y leer sesiones
- **WHEN** un usuario hace login y CI4 escribe la sesión
- **THEN** aparece un registro en ci_sessions y el usuario puede navegar con sesión activa

