## Why

Cada archivo subido por un usuario se registra en `documents`. Esta tabla es crítica: vincula el archivo en S3 con el usuario, el tipo de documento y el periodo al que pertenece. Es la quinta migración.

## What Changes

- Crear migración CI4 `2025-01-01-000005_CreateDocumentsTable.php`
- Campos S3: `s3_key VARCHAR(500)`, `s3_bucket VARCHAR(150)`, `file_extension VARCHAR(10)`
- Campos de nombre: `original_name VARCHAR(255)` y `stored_name VARCHAR(255)` — NO filename_orig ni filename_stored
- `period_id INT UNSIGNED NOT NULL` — FK a periods, NO nullable
- 4 FK: user_id→users CASCADE, doc_type_id→document_types, period_id→periods, reviewed_by→users SET NULL

## Capabilities

### New Capabilities
- `create-documents-table`: Migración que crea `documents` con los campos S3, nombres correctos (original_name/stored_name) y period_id NOT NULL

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000005_CreateDocumentsTable.php`
- Depende de: users, document_types, periods
- CRÍTICO: columnas de nombre son `original_name` y `stored_name` — NO `filename_orig` ni `filename_stored`
- CRÍTICO: `period_id` es NOT NULL — todo documento pertenece a un periodo
