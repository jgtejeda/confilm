## ADDED Requirements

### Requirement: FileValidator valida extensión y tamaño antes del upload
El sistema SHALL verificar que la extensión del archivo (obtenida con `$file->getClientExtension()`) esté en el array `$allowedTypes` y que el tamaño en bytes sea menor o igual a `$maxSizeMb * 1024 * 1024`. El método `validate()` DEBE retornar un array de strings con los mensajes de error (array vacío = válido). No debe lanzar excepciones.

#### Scenario: Archivo válido dentro de límites
- **WHEN** se llama `$validator->validate($file, ['pdf', 'jpg'], 5)` con un JPG de 2 MB
- **THEN** retorna array vacío `[]`

#### Scenario: Extensión no permitida
- **WHEN** se llama `$validator->validate($file, ['pdf'], 5)` con un archivo `.docx`
- **THEN** retorna un array con un mensaje indicando que el tipo no está permitido
- **THEN** el mensaje incluye los tipos aceptados

#### Scenario: Archivo excede el tamaño máximo
- **WHEN** se llama `$validator->validate($file, ['pdf'], 5)` con un PDF de 6 MB
- **THEN** retorna un array con un mensaje indicando que el límite es 5 MB

#### Scenario: Múltiples errores
- **WHEN** el archivo tiene extensión no permitida y además excede el tamaño
- **THEN** retorna un array con dos mensajes de error (uno por cada falla)

---

### Requirement: FileValidator verifica magic bytes para detectar archivos maliciosos
El sistema SHALL leer los primeros 5 bytes del archivo temporal y compararlos con los magic bytes conocidos del tipo declarado. El método `checkMagicBytes(string $tempPath, string $ext): bool` DEBE retornar `false` si los bytes no coinciden, rechazando así archivos renombrados maliciosamente.

Los magic bytes por extensión son:
- `pdf`: `%PDF-` (5 bytes)
- `png`: `\x89PNG` (4 bytes, se leen 5 para consistencia)
- `jpg`: `\xFF\xD8\xFF` (3 bytes, se leen 5 para consistencia)
- `docx`, `xlsx`, `pptx`: `PK\x03\x04` (firma ZIP — todos los formatos OOXML)

#### Scenario: PDF real pasa la verificación
- **WHEN** se llama `$validator->checkMagicBytes('/tmp/real.pdf', 'pdf')`
- **THEN** retorna `true`

#### Scenario: EXE renombrado como PDF falla la verificación
- **WHEN** se llama `$validator->checkMagicBytes('/tmp/malware.pdf', 'pdf')` y el archivo es un ejecutable
- **THEN** los primeros 5 bytes no son `%PDF-`
- **THEN** retorna `false`

#### Scenario: JPG real pasa la verificación
- **WHEN** se llama `$validator->checkMagicBytes('/tmp/photo.jpg', 'jpg')`
- **THEN** los primeros bytes son `\xFF\xD8\xFF`
- **THEN** retorna `true`

#### Scenario: DOCX real pasa la verificación
- **WHEN** se llama `$validator->checkMagicBytes('/tmp/doc.docx', 'docx')`
- **THEN** los primeros bytes son `PK\x03\x04` (firma ZIP de formato OOXML)
- **THEN** retorna `true`

#### Scenario: XLSX y PPTX también pasan con firma ZIP
- **WHEN** se llama con ext `xlsx` o `pptx` y el archivo es un OOXML válido
- **THEN** los primeros bytes son `PK\x03\x04`
- **THEN** retorna `true`

#### Scenario: PNG renombrado como DOCX falla
- **WHEN** se llama `$validator->checkMagicBytes('/tmp/image.docx', 'docx')` y el archivo es PNG
- **THEN** los primeros bytes son `\x89PNG`, no `PK\x03\x04`
- **THEN** retorna `false`

---

### Requirement: FileValidator soporta exactamente 6 tipos de archivo
El sistema SHALL reconocer únicamente los tipos: `pdf`, `docx`, `xlsx`, `pptx`, `jpg`, `png`. Cualquier otra extensión en `$allowedTypes` será rechazada si no tiene magic bytes definidos.

#### Scenario: Tipo no reconocido en checkMagicBytes
- **WHEN** se llama `$validator->checkMagicBytes('/tmp/file.zip', 'zip')`
- **THEN** retorna `false` (extensión sin magic bytes definidos en el sistema)

#### Scenario: Los 6 tipos reconocidos tienen magic bytes configurados
- **WHEN** se accede al array interno `$magicBytes`
- **THEN** contiene entradas para: `pdf`, `png`, `jpg`, `docx`, `xlsx`, `pptx`
