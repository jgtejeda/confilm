# create-notifications-table Specification

## Purpose
TBD - created by archiving change migration-notifications. Update Purpose after archive.
## Requirements
### Requirement: Migración CreateNotificationsTable crea la tabla correctamente
El sistema SHALL disponer del archivo `2025-01-01-000007_CreateNotificationsTable.php`.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate` con users ya creado
- **THEN** la tabla `notifications` existe sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de notifications
- **THEN** la tabla se elimina sin errores (nada tiene FK a esta tabla)

### Requirement: sender_id NULL representa notificación automática del sistema
La tabla SHALL tener `sender_id INT UNSIGNED NULL` donde NULL indica que la notificación fue generada automáticamente por el sistema (no por un admin).

#### Scenario: Notificación automática tiene sender_id NULL
- **WHEN** se inserta una notificación sin sender_id
- **THEN** sender_id es NULL y la notificación se guarda correctamente

#### Scenario: Notificación manual tiene sender_id con valor
- **WHEN** se inserta una notificación con sender_id = id de un admin
- **THEN** la notificación se guarda con la referencia al admin

### Requirement: Type ENUM tiene los 6 tipos definidos
La tabla SHALL tener `type ENUM('info','success','warning','error','document','inscription') NOT NULL`.

#### Scenario: ENUM acepta los 6 tipos válidos
- **WHEN** se inserta una notificación con cada uno de los 6 tipos
- **THEN** todas se insertan correctamente

#### Scenario: ENUM rechaza tipo inválido
- **WHEN** se intenta insertar una notificación con type='alert'
- **THEN** MySQL lanza error de ENUM constraint

### Requirement: Índice idx_user_read optimiza el conteo de no leídas
La tabla SHALL tener INDEX `idx_user_read` en `(user_id, read_at)` para la query de badge.

#### Scenario: Índice compuesto presente
- **WHEN** se ejecuta `SHOW INDEX FROM notifications`
- **THEN** aparece idx_user_read sobre (user_id, read_at)

