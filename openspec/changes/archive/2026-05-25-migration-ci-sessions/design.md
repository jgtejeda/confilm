## Context

CI4's `DatabaseHandler` para sesiones requiere una tabla con estructura fija. El archivo `.env` configura: `session.driver='CodeIgniter\Session\Handlers\DatabaseHandler'` y `session.savePath=ci_sessions`. El `id` es VARCHAR(128) porque CI4 genera session IDs de longitud variable.

## Goals / Non-Goals

**Goals:**
- Estructura EXACTA: id VARCHAR(128) PRIMARY KEY, ip_address VARCHAR(45) NOT NULL, timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP, data BLOB NOT NULL
- Sin FK — CI4 gestiona esta tabla completamente
- `down()` limpio: solo dropTable

**Non-Goals:**
- No modificar la configuración de sesiones (va en .env y Config/Session.php)
- No lógica de sesiones — CI4 lo maneja automáticamente

## Decisions

**`id VARCHAR(128)` como PRIMARY KEY (no INT AUTO_INCREMENT)**
→ CI4 genera session IDs como strings. La PRIMARY KEY debe ser el id de sesión, no un entero autogenerado.

**`timestamp TIMESTAMP` (no DATETIME)**
→ La documentación oficial de CI4 DatabaseHandler especifica TIMESTAMP para este campo. Usar DATETIME podría causar problemas con el handler.

**`data BLOB`**
→ Los datos de sesión son datos binarios serializados por CI4. BLOB es el tipo correcto.

**Crear con SQL raw en lugar de Forge**
→ El Forge de CI4 puede tener comportamientos inesperados con PRIMARY KEY de tipo VARCHAR. Usar `$this->db->query()` con SQL directo garantiza la estructura exacta.

## Risks / Trade-offs

- [Riesgo] Si la estructura no coincide exactamente con lo que espera DatabaseHandler, las sesiones fallarán silenciosamente o con errores crípticos.
  → Mitigación: verificar con `SHOW CREATE TABLE ci_sessions` y comparar con la documentación de CI4.
