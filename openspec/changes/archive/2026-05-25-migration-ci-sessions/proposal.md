## Why

CI4 usa `DatabaseHandler` para sesiones — requiere una tabla `ci_sessions` con una estructura EXACTA definida por el framework. Si la estructura no coincide, las sesiones no funcionan.

## What Changes

- Crear migración CI4 `2025-01-01-000009_CreateCiSessionsTable.php`
- Estructura EXACTA requerida por CI4 DatabaseHandler:
  - `id VARCHAR(128) NOT NULL` como PRIMARY KEY
  - `ip_address VARCHAR(45) NOT NULL`
  - `timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP`
  - `data BLOB NOT NULL`
- Sin FK — tabla gestionada exclusivamente por CI4

## Capabilities

### New Capabilities
- `create-ci-sessions-table`: Migración de la tabla de sesiones con la estructura exacta requerida por CodeIgniter 4 DatabaseHandler

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000009_CreateCiSessionsTable.php`
- Sin dependencias FK — puede crearse en cualquier orden
- CRÍTICO: la estructura debe ser EXACTA o CI4 no puede gestionar sesiones
