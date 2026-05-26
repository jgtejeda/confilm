## Context

`inscriptions` es la entidad central del proceso: un usuario tiene una inscripción por periodo. El flujo de estados es: `incomplete` → `under_review` → `approved`/`rejected`. `submitted_at` registra cuándo el usuario envió sus documentos para revisión.

## Goals / Non-Goals

**Goals:**
- UNIQUE KEY `uq_user_period (user_id, period_id)` — un usuario, una inscripción POR PERIODO
- Status ENUM de 4 estados del proceso
- `reviewed_by INT UNSIGNED NULL` — el admin que aprobó/rechazó (puede ser NULL si aún no revisado)
- `submitted_at DATETIME NULL` — timestamp del envío a revisión
- Timestamps `created_at` y `updated_at`

**Non-Goals:**
- No lógica del flujo de estados (va en controladores)
- No el formulario de envío (Propuesta 20)

## Decisions

**UNIQUE KEY compuesta `(user_id, period_id)` — no solo `user_id`**
→ El negocio permite que un usuario participe en múltiples convocatorias (distintos periodos). La restricción es que solo puede tener una inscripción POR periodo. Un UNIQUE en solo `user_id` impediría la participación en futuras convocatorias.

**`user_id ON DELETE CASCADE`**
→ Si se elimina un usuario, su inscripción desaparece. No tiene sentido mantener inscripciones de usuarios eliminados.

**`period_id` sin CASCADE (RESTRICT implícito)**
→ No se debe poder eliminar un periodo si tiene inscripciones — protege la integridad histórica.

**`submitted_at DATETIME NULL`**
→ La inscripción se crea en el registro (status: incomplete). Solo cuando el usuario hace click en "Enviar" se llena `submitted_at` y cambia a `under_review`.

## Risks / Trade-offs

- [Riesgo] Intentar eliminar un periodo con inscripciones activas fallará por FK RESTRICT.
  → Mitigación: el controlador admin debe verificar esto y mostrar un error descriptivo.
