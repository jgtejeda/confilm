## Context

Esta tabla resuelve la relación N:M entre periodos y tipos de documento. Cuando el admin crea un periodo, selecciona qué tipos de documento aplican (iniciales y complementarios). El campo `sort_order` permite controlar el orden de aparición en el formulario de registro.

## Goals / Non-Goals

**Goals:**
- Tabla pivote con FK `period_id → periods.id ON DELETE CASCADE` y `doc_type_id → document_types.id ON DELETE CASCADE`
- UNIQUE KEY compuesta `(period_id, doc_type_id)` — un tipo de documento aparece máximo una vez por periodo
- Campo `sort_order INT DEFAULT 0` para el orden de aparición

**Non-Goals:**
- No almacenar si es 'inicial' o 'complementario' aquí — eso viene de `document_types.category`
- No lógica de asignación (va en PeriodController)

## Decisions

**`ON DELETE CASCADE` en ambas FK**
→ Si se elimina un periodo, sus asignaciones de tipos desaparecen automáticamente. Si se elimina un tipo de documento, sus asignaciones a periodos desaparecen. En ambos casos la integridad se mantiene sin lógica adicional en el código.

**UNIQUE KEY compuesta, no PRIMARY separado por id**
→ La tabla tiene `id` propio por conveniencia con CI4 Query Builder, pero la unicidad real es `(period_id, doc_type_id)`.

## Risks / Trade-offs

- [Trade-off] CASCADE en FK de document_types significa que eliminar un tipo de documento activo elimina silenciosamente sus asignaciones a periodos.
  → Mitigación: el controlador debe verificar que no haya periodos activos antes de permitir eliminar un tipo de documento. La UI debe advertir al admin.
