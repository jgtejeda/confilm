## Context

El AdminUserSeeder es el único seed del proyecto. Crea un usuario admin inicial para que el sistema sea funcional desde el primer deploy. El seed de ARQUITECTURA.md §5 muestra la estructura: `password_hash` con `$2y$12$...` (bcrypt cost 12).

## Goals / Non-Goals

**Goals:**
- Seeder que hace `password_hash('contraseña_inicial', PASSWORD_BCRYPT, ['cost' => 12])`
- `email_verified = 1` — el admin accede sin verificar correo
- `status = 'active'` — el admin puede hacer login inmediatamente
- `role = 'admin'`
- Idempotente: verificar si ya existe antes de insertar (usar INSERT IGNORE o verificar email)

**Non-Goals:**
- No seed de usuarios normales
- No seed de document_types ni periods (el admin los crea desde el panel)
- No envío de correo al crear el admin por seeder

## Decisions

**Contraseña hardcodeada con constante en el seeder**
→ Es un seed de desarrollo/setup inicial. La contraseña debe cambiarse en producción. Documentar esto claramente en el seeder. Alternativa: leer de una variable de entorno — más seguro pero más complejo para un seed.

**Usar INSERT IGNORE o verificar existencia**
→ Si el seeder se ejecuta dos veces, no debe fallar. Verificar `WHERE email = 'admin@...'` antes de insertar.

**`password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12])`**
→ Cost 12 es el estándar recomendado para bcrypt en 2024-2025. Balanceo entre seguridad y performance (~300ms en hardware moderno).

## Risks / Trade-offs

- [Riesgo] La contraseña inicial del admin puede quedar en código si no se cambia en producción.
  → Mitigación: documentar prominentemente en el seeder y en el README que la contraseña DEBE cambiarse antes del deploy en producción.
