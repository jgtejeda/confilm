# create-periods-table Specification

## Purpose
TBD - created by archiving change migration-periods. Update Purpose after archive.
## Requirements
### Requirement: Migración CreatePeriodsTable crea la tabla correctamente
El sistema SHALL disponer del archivo `app/app/Database/Migrations/2025-01-01-000002_CreatePeriodsTable.php` que crea la tabla `periods` con todos los campos de ARQUITECTURA.md §5.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate` con users ya creado
- **THEN** la tabla `periods` existe en la base de datos sin errores

#### Scenario: Rollback respeta orden FK
- **WHEN** se ejecuta rollback en orden inverso (periods antes que users)
- **THEN** la tabla `periods` se elimina sin errores de FK

### Requirement: Tabla periods tiene todos los campos requeridos
La tabla `periods` SHALL contener: id, name, description, start_date, end_date, active, created_by, created_at, updated_at.

#### Scenario: Campos de periodo presentes
- **WHEN** se inspecciona el esquema de `periods`
- **THEN** existen `name VARCHAR(200) NOT NULL`, `description TEXT NULL`, `start_date DATETIME NOT NULL`, `end_date DATETIME NOT NULL`

#### Scenario: Campo active con default correcto
- **WHEN** se inserta un periodo sin especificar `active`
- **THEN** el valor de `active` es 1

#### Scenario: FK created_by con SET NULL
- **WHEN** se elimina el usuario admin creador de un periodo
- **THEN** `periods.created_by` se convierte en NULL sin eliminar el periodo

### Requirement: Índice compuesto idx_active_dates presente
La tabla `periods` SHALL tener un índice compuesto en `(active, start_date, end_date)` para optimizar la query de periodo activo.

#### Scenario: Índice compuesto existe
- **WHEN** se inspecciona con `SHOW INDEX FROM periods`
- **THEN** aparece el índice `idx_active_dates` sobre las columnas active, start_date, end_date

