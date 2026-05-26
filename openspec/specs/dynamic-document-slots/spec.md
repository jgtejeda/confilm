# dynamic-document-slots Specification

## Purpose
TBD - created by archiving change auth-register. Update Purpose after archive.
## Requirements
### Requirement: Slots de documentos generados dinámicamente desde DB
La vista `views/auth/register.php` SHALL generar un slot de upload por cada `document_type` del periodo activo con `category='inicial'`. Los slots se obtienen de `period_document_types JOIN document_types WHERE period_id=$period['id'] AND dt.active=1 ORDER BY pdt.sort_order`. Cada slot SHALL mostrar: `name`, `description` (instrucción al usuario), tipos aceptados legibles, tamaño máximo, y un `<input type="file">` con `name="doc_{$docType['id']}"` y `accept=` generado desde `allowed_types`.

#### Scenario: Slots de documentos coinciden con los del periodo
- **WHEN** el periodo activo tiene 3 tipos de documento iniciales configurados
- **THEN** el formulario de registro muestra exactamente 3 slots de archivo, con los nombres e instrucciones del admin

#### Scenario: Input file tiene accept= correcto por tipo
- **WHEN** un document_type tiene `allowed_types = ["pdf","jpg","png"]`
- **THEN** el input file generado tiene `accept=".pdf,.jpg,.jpeg,.png"` (mapeando jpg→.jpg,.jpeg)

#### Scenario: Instrucción del admin visible en el slot
- **WHEN** un document_type tiene `description = "Sube tu RFC actualizado (vigencia máxima 3 meses)"`
- **THEN** la vista muestra ese texto como instrucción debajo del nombre del documento

---

### Requirement: Validación backend de cada archivo subido
El sistema SHALL validar cada archivo con `FileValidator::validate($file, json_decode($docType['allowed_types'],true), $docType['max_size_mb'])`. Después de mover a temporal, SHALL verificar magic bytes con `FileValidator::checkMagicBytes($tempPath, $ext)`. Si cualquier validación falla SHALL retornar al formulario con el error específico indicando qué documento falló.

#### Scenario: Archivo con extensión no permitida es rechazado
- **WHEN** se sube un archivo `.exe` renombrado a `.pdf` para un slot que acepta solo PDF
- **THEN** FileValidator::checkMagicBytes() falla y el sistema retorna error "Tipo de archivo no permitido"

#### Scenario: Archivo mayor al límite es rechazado
- **WHEN** se sube un archivo de 6MB para un slot con `max_size_mb=5`
- **THEN** FileValidator::validate() retorna error de tamaño y el formulario muestra el mensaje específico

#### Scenario: PDF válido pasa la validación
- **WHEN** se sube un PDF real (magic bytes `%PDF-`) para un slot que acepta PDF
- **THEN** tanto validate() como checkMagicBytes() retornan sin errores

