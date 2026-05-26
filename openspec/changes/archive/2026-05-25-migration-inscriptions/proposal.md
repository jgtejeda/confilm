## Why

La inscripción vincula a un usuario con un periodo y lleva el estado del proceso de validación. La UNIQUE KEY en `(user_id, period_id)` — no solo en `user_id` — permite que un usuario pueda inscribirse en distintos periodos pero solo una vez por periodo.

## What Changes

- Crear migración CI4 `2025-01-01-000006_CreateInscriptionsTable.php`
- UNIQUE KEY COMPUESTA `uq_user_period (user_id, period_id)` — NO solo user_id
- Status ENUM con 4 estados del proceso: incomplete, under_review, approved, rejected
- 3 FK: user_id→users CASCADE, period_id→periods, reviewed_by→users SET NULL

## Capabilities

### New Capabilities
- `create-inscriptions-table`: Migración con UNIQUE compuesta (user_id, period_id) que permite N inscripciones por usuario en distintos periodos

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000006_CreateInscriptionsTable.php`
- Depende de: users, periods
- CRÍTICO: UNIQUE KEY es `(user_id, period_id)` — NO solo `user_id`
