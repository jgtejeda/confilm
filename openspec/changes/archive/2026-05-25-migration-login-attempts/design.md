## Context

El rate limiting cuenta intentos fallidos (`success=0`) por combinación `identifier+ip_address` en los últimos 15 minutos. `identifier` puede ser email o username — el usuario puede intentar login con cualquiera de los dos.

## Goals / Non-Goals

**Goals:**
- Tabla con `identifier VARCHAR(150)`, `ip_address VARCHAR(45)`, `success TINYINT(1) DEFAULT 0`, `attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP`
- Índice en `(identifier, ip_address)` para la query de rate limiting
- Índice en `attempted_at` para limpiar registros antiguos (opcional)
- Sin FK — tabla de auditoría independiente

**Non-Goals:**
- No la lógica de rate limiting (va en LoginController)
- No limpieza automática de registros (puede hacerse con un cron job externo)

## Decisions

**Sin FK a users**
→ Los intentos incluyen usernames/emails que pueden no existir (alguien probando credenciales inexistentes). Una FK rompería el registro de estos intentos.

**`identifier VARCHAR(150)`**
→ Puede ser email (máx 150) o username (máx 60). VARCHAR(150) cubre ambos.

**`ip_address VARCHAR(45)`**
→ Soporta tanto IPv4 (15 chars) como IPv6 (39 chars) con margen.

## Risks / Trade-offs

- [Trade-off] La tabla puede crecer indefinidamente si no se limpia.
  → Mitigación: el LoginController solo consulta los últimos 15 minutos. Los registros viejos no afectan la lógica. Se puede purgar periódicamente.
