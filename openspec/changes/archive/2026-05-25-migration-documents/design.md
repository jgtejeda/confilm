## Context

`documents` registra cada archivo subido a S3. No almacena el archivo físicamente — solo la metadata y la referencia al objeto en S3 (`s3_key`). El campo `period_id NOT NULL` es intencional: un documento siempre pertenece a un periodo (el activo al momento de subirlo). `reviewed_by` es el admin que validó el documento, no el usuario que lo subió.

## Goals / Non-Goals

**Goals:**
- Crear tabla `documents` con el esquema exacto de ARQUITECTURA.md §5
- `original_name VARCHAR(255)` (nombre original del archivo del usuario) — NO `filename_orig`
- `stored_name VARCHAR(255)` (nombre UUID.ext en S3) — NO `filename_stored`
- `s3_key VARCHAR(500)` — ruta completa en S3: `rcf/{period_id}/{user_id}/{categoria}/{uuid}.{ext}`
- `period_id INT UNSIGNED NOT NULL` con FK a periods — NO nullable
- `reviewed_by INT UNSIGNED NULL` con FK a users ON DELETE SET NULL
- Índices: `idx_user_period (user_id, period_id)` y `idx_status (status)`

**Non-Goals:**
- No almacenar el archivo en el servidor (va a S3)
- No lógica de upload (va en S3Service y DocumentController)

## Decisions

**`original_name` y `stored_name` — no `filename_orig`/`filename_stored`**
→ Los nombres definidos en ARQUITECTURA.md §5 son exactamente `original_name` y `stored_name`. El $allowedFields de DocumentModel también debe usar estos nombres.

**`period_id NOT NULL`**
→ Todo documento subido está ligado a un periodo. No existen documentos "sin periodo" — el sistema solo permite subir durante un periodo activo.

**`user_id ON DELETE CASCADE`**
→ Si se elimina un usuario, sus documentos también desaparecen (no tiene sentido mantener archivos huérfanos). La eliminación en S3 debe hacerse antes en el controlador.

**`doc_type_id` sin CASCADE**
→ Si se elimina un tipo de documento, sus documentos históricos deben mantenerse por auditoría. MySQL bloqueará la eliminación del tipo si hay documentos asociados (RESTRICT implícito).

**`reviewed_by ON DELETE SET NULL`**
→ Si se elimina el admin revisor, el documento mantiene su estado pero pierde la referencia al revisor.

## Risks / Trade-offs

- [Riesgo] Si se intenta eliminar un document_type que tiene documentos asociados, MySQL lanzará error FK.
  → Mitigación: el controlador admin debe verificar esto antes de permitir la eliminación.

- [Riesgo] `s3_key` como VARCHAR(500) puede truncarse si la ruta es muy larga.
  → Mitigación: la estructura de key está definida: `rcf/{period_id}/{user_id}/{categoria}/{uuid}.{ext}` — máximo ~60 chars; VARCHAR(500) es más que suficiente.
