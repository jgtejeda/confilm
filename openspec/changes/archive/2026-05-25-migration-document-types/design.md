## Context

`document_types` es el catálogo maestro de tipos de documento configurable por el admin. No tiene seed inicial — el admin los crea desde el panel. El campo más crítico es `allowed_types`: almacena un JSON array de extensiones permitidas (ej: `["pdf","jpg","png"]`) como VARCHAR(500). Esta decisión evita una tabla relacional adicional para un dato simple y estable.

## Goals / Non-Goals

**Goals:**
- Crear tabla `document_types` con el esquema exacto de ARQUITECTURA.md §5
- `allowed_types VARCHAR(500) NOT NULL` — JSON array, no tabla separada
- `category ENUM('inicial','complementario') NOT NULL`
- FK `created_by → users.id ON DELETE SET NULL`
- Sin seed — el admin popula esta tabla

**Non-Goals:**
- No crear tabla separada para los tipos de archivo permitidos
- No seed de document_types (el admin los define)
- No crear DocumentTypeModel (Propuesta 02-K)
- No crear el CRUD admin (Propuesta 11)

## Decisions

**`allowed_types` como VARCHAR(500) JSON, no tabla relacional**
→ Los valores válidos son fijos: `["pdf","docx","xlsx","pptx","jpg","png"]`. Son máximo 6 elementos de 4 chars cada uno. VARCHAR(500) es suficiente holgado. Una tabla relacional añadiría complejidad innecesaria para un dato que el admin selecciona via checkboxes.

**`category ENUM('inicial','complementario')`**
→ El sistema distingue documentos que se suben en el registro (inicial) de los que se suben después (complementario). Son exactamente dos categorías, bien definidas por el negocio.

**`required TINYINT(1) DEFAULT 1`**
→ Por defecto todos los tipos de documento son requeridos. El admin puede marcarlos como opcionales.

**`max_months INT NULL`**
→ Restricción de vigencia del documento (ej: comprobante de domicilio no mayor a 3 meses). NULL significa sin restricción de vigencia.

## Risks / Trade-offs

- [Riesgo] Si `allowed_types` se corrompe (JSON inválido), FileValidator fallará al hacer json_decode.
  → Mitigación: el admin controller valida que al menos 1 tipo esté seleccionado y hace json_encode antes de INSERT.

- [Trade-off] VARCHAR(500) limita el número de tipos configurables.
  → Aceptable: solo hay 6 tipos de archivo válidos en el sistema; VARCHAR(500) es más que suficiente.
