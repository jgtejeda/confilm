## Context

`periods` es la segunda tabla en el grafo de dependencias. Su campo `created_by` referencia a `users.id` con `ON DELETE SET NULL` — si se elimina el admin creador, el periodo permanece pero sin referencia al creador. El índice compuesto `(active, start_date, end_date)` es crítico para la query que determina si hay un periodo activo: `WHERE active=1 AND start_date<=NOW() AND end_date>=NOW()`.

## Goals / Non-Goals

**Goals:**
- Crear tabla `periods` con el esquema exacto de ARQUITECTURA.md §5
- FK `created_by → users.id ON DELETE SET NULL`
- Índice compuesto `idx_active_dates` en `(active, start_date, end_date)`
- `down()` que hace `dropTable('periods')` — en rollback se ejecuta antes que users

**Non-Goals:**
- No crear el controlador PeriodController (Propuesta 12)
- No seed de periodos (el admin los crea desde el panel)
- No lógica de "periodo activo" (va en el modelo/controlador)

## Decisions

**`created_by ON DELETE SET NULL` en lugar de CASCADE**
→ Si se elimina el admin creador, el periodo debe mantenerse activo. Los usuarios ya registrados en ese periodo no deben perder su inscripción.

**Índice compuesto `(active, start_date, end_date)`**
→ La query de periodo activo siempre filtra por las tres columnas. Un índice compuesto es más eficiente que tres índices separados para esta query específica.

**`active TINYINT(1) DEFAULT 1`**
→ Los periodos nuevos se crean activos por defecto; el admin puede desactivarlos manualmente o dejar que expiren por fechas.

## Risks / Trade-offs

- [Riesgo] El `down()` fallará si `period_document_types`, `documents` o `inscriptions` tienen registros con FK a `periods`.
  → Mitigación: el rollback completo debe seguir el orden inverso: 9→1 (ci_sessions → users).
