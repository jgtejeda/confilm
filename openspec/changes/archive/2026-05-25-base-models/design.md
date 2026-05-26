## Context

Los modelos de CI4 usan `$allowedFields` para proteger contra mass assignment. Si un campo no está en `$allowedFields`, el modelo lo ignorará silenciosamente en INSERT/UPDATE. Esto causaría bugs difíciles de detectar — por ejemplo, si `period_id` no está en `$allowedFields` de DocumentModel, los documentos se insertarían sin period_id.

## Goals / Non-Goals

**Goals:**
- 6 modelos con los `$allowedFields` exactos de cada tabla según ARQUITECTURA.md §5
- `$useTimestamps = true` donde la tabla tiene `created_at` / `updated_at`
- `$useTimestamps = false` donde no (documents tiene `uploaded_at`, no timestamps estándar)
- `$returnType = 'array'` para consistencia

**Non-Goals:**
- No métodos de negocio (getActivePeriod(), etc.) — van en los controllers o en propuestas posteriores
- No scopes ni callbacks — solo la estructura base
- No relaciones (no hay ORM en CI4 por defecto)

## Decisions

**`$returnType = 'array'`**
→ Devolver arrays en lugar de objetos evita ambigüedad en el código PHP. CI4 soporta ambos; array es más explícito para este proyecto.

**`$useTimestamps = false` en DocumentModel**
→ La tabla `documents` no tiene `created_at`/`updated_at` — tiene `uploaded_at` como timestamp único. Si se habilita `$useTimestamps`, CI4 intentaría llenar `created_at` y `updated_at` que no existen.

**`$useTimestamps = false` en NotificationModel**
→ `notifications` solo tiene `created_at` (no `updated_at`). Para evitar que CI4 intente escribir `updated_at`, mejor deshabilitar y manejar `created_at` manualmente, o configurar `$updatedField = ''`.

**`$allowedFields` de DocumentModel — lista completa crítica:**
```php
protected $allowedFields = [
    'user_id', 'doc_type_id', 'period_id',
    'original_name', 'stored_name',
    's3_key', 's3_bucket',
    'file_size', 'mime_type', 'file_extension',
    'status', 'rejection_note',
    'reviewed_by', 'reviewed_at', 'uploaded_at'
];
```

## Risks / Trade-offs

- [Riesgo] Si un campo crítico se omite de `$allowedFields`, CI4 lo ignorará silenciosamente en INSERT.
  → Mitigación: verificar cada modelo contra el esquema de ARQUITECTURA.md §5 campo por campo.
