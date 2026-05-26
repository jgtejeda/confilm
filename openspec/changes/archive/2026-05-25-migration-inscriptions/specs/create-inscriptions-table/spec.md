## ADDED Requirements

### Requirement: Migración CreateInscriptionsTable crea la tabla correctamente
El sistema SHALL disponer del archivo `2025-01-01-000006_CreateInscriptionsTable.php`.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate` con users y periods ya creados
- **THEN** la tabla `inscriptions` existe sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de inscriptions
- **THEN** la tabla se elimina sin errores

### Requirement: UNIQUE KEY es compuesta en (user_id, period_id)
La tabla SHALL tener UNIQUE KEY `uq_user_period` en el par `(user_id, period_id)`. NO SHALL existir un UNIQUE solo en `user_id`.

#### Scenario: Un usuario puede tener una inscripción por periodo
- **WHEN** se inserta una inscripción con user_id=1 y period_id=1
- **THEN** el registro se inserta correctamente

#### Scenario: Duplicate en mismo periodo es rechazado
- **WHEN** se intenta insertar una segunda inscripción con user_id=1 y period_id=1
- **THEN** MySQL lanza error de duplicate key

#### Scenario: Mismo usuario en distintos periodos es permitido
- **WHEN** se inserta inscripción con user_id=1, period_id=1 y luego user_id=1, period_id=2
- **THEN** ambos registros se insertan correctamente

### Requirement: Status ENUM tiene los 4 estados del proceso
La tabla SHALL tener `status ENUM('incomplete','under_review','approved','rejected') DEFAULT 'incomplete'`.

#### Scenario: Status default es incomplete
- **WHEN** se inserta una inscripción sin especificar status
- **THEN** status es 'incomplete'

### Requirement: submitted_at es nullable
La tabla SHALL tener `submitted_at DATETIME NULL` para registrar el momento en que el usuario envía a revisión.

#### Scenario: submitted_at puede ser NULL
- **WHEN** se crea una inscripción en el registro del usuario
- **THEN** submitted_at es NULL (aún no enviada a revisión)
