## Context

El proyecto usa CodeIgniter 4 con su sistema de migraciones basado en `forge`. La tabla `users` es la raíz del modelo de datos: todas las demás tablas (periods, document_types, documents, inscriptions, notifications) tienen FK a `users.id`. Debe crearse primero en el orden de migraciones.

## Goals / Non-Goals

**Goals:**
- Crear la tabla `users` con el esquema exacto de ARQUITECTURA.md §5
- Incluir campos `verify_token` / `verify_exp` necesarios para el flujo de verificación de correo (Propuesta 06)
- Incluir campos `recovery_token` / `recovery_exp` para recuperación de contraseña (Propuesta 08)
- Definir índices para queries frecuentes (email, username, status, verify_token)
- Implementar `down()` limpio (solo `dropTable` — no hay FK que respetar)

**Non-Goals:**
- No crear el seeder de admin (eso es Propuesta 02-J)
- No crear el modelo `UserModel` (eso es Propuesta 02-K)
- No implementar lógica de autenticación

## Decisions

**Usar CI4 Forge API en lugar de SQL raw**
→ Mantiene coherencia con el resto de las migraciones del proyecto y permite que `php spark migrate:rollback` funcione correctamente.

**Campos `verify_token` / `verify_exp` en `users` y no en tabla separada**
→ El sistema de verificación de correo es simple (un token activo por usuario), no requiere historial. Mantenerlos en `users` simplifica las queries en AuthFilter y VerifyController.

**`verify_token VARCHAR(100)`** (no VARCHAR(64))
→ El token es `bin2hex(random_bytes(32))` = 64 chars hex. Se usa 100 por margen y consistencia con `recovery_token`.

**ENUMs para `role` y `status`**
→ MySQL 8 soporta ENUMs eficientemente. Los valores son conocidos y estables; no se anticipan cambios frecuentes.

**`password_hash VARCHAR(255)`**
→ bcrypt con cost 12 genera hashes de ~60 chars. VARCHAR(255) es el estándar recomendado para compatibilidad futura con otros algoritmos.

## Risks / Trade-offs

- [Riesgo] Si se ejecuta `down()` mientras otras tablas tienen FK a `users`, MySQL lanzará error de FK.  
  → Mitigación: el rollback debe ejecutarse en orden inverso (9→1). Documentado en ROADMAP tasks.md Bloque 3.

- [Trade-off] Los ENUMs de MySQL no son fáciles de extender sin ALTER TABLE.  
  → Aceptable: los roles y estados están definidos por la arquitectura del negocio y son estables.
