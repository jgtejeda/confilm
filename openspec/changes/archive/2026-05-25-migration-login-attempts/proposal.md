## Why

El sistema tiene rate limiting de login: máx 5 intentos fallidos por 15 minutos por ip+identifier. Esta tabla sin FK es la más independiente del esquema — ideal para crear cerca del final del orden de migraciones.

## What Changes

- Crear migración CI4 `2025-01-01-000008_CreateLoginAttemptsTable.php`
- Sin FK — tabla independiente de auditoría
- Índices en `(identifier, ip_address)` y `attempted_at` para las queries de rate limiting

## Capabilities

### New Capabilities
- `create-login-attempts-table`: Migración de auditoría de intentos de login sin dependencias FK

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000008_CreateLoginAttemptsTable.php`
- Sin dependencias FK — puede ejecutarse en cualquier momento después de la migración inicial
- El `down()` es trivial: solo dropTable
