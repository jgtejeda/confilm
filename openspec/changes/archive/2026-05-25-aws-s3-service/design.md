## Context

El sistema almacena todos los archivos de usuarios en AWS S3 (no en el servidor). Las propuestas 01 y 02 sentaron la infraestructura Docker y la base de datos. Esta propuesta crea las dos libraries que encapsulan toda la lógica de almacenamiento y validación:

- **S3Service** (`app/app/Libraries/S3Service.php`): wrapper sobre el AWS SDK v3 para subir, generar presigned URLs, archivar y eliminar objetos.
- **FileValidator** (`app/app/Libraries/FileValidator.php`): valida extensión, tamaño y magic bytes de los archivos antes de subirlos.
- **Config/AWS** (`app/app/Config/AWS.php`): clase de configuración CI4 (extiende `BaseConfig`) que expone las credenciales y configuración S3 leídas del `.env`.

El SDK `aws/aws-sdk-php ^3.0` ya está instalado en `vendor/`. Las variables de entorno `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_REGION` y `AWS_S3_BUCKET` ya existen en `.env`. La tabla `documents` ya tiene las columnas `s3_key`, `s3_bucket`, `file_extension`, `period_id`, `original_name` y `stored_name`.

## Goals / Non-Goals

**Goals:**
- Encapsular toda la lógica S3 en una Library reutilizable con interfaz simple (métodos bool/string)
- Garantizar que ninguna excepción del SDK llegue al caller — siempre retornar bool/string y loggear
- Validar archivos por extensión, tamaño y magic bytes antes de subirlos a S3
- Mantener la estructura de keys `rcf/{period_id}/{user_id}/{categoria}/{uuid}.{ext}` en S3

**Non-Goals:**
- No crear endpoints ni rutas en esta propuesta (los consumen P05, P14, P17, P20)
- No modificar la tabla `documents` ni los modelos existentes
- No implementar lógica de negocio (quién puede subir qué) — eso va en los controllers
- No hacer streaming ni multipart upload (archivos ≤ 20 MB según php.ini)

## Decisions

### D1: S3Service como Library CI4 (no servicio de CI4)

**Decisión**: Usar `app/app/Libraries/S3Service.php` con namespace `App\Libraries`, instanciada con `new S3Service()` en los controllers.

**Alternativas consideradas**:
- Registrar como servicio en `Config/Services.php`: añade flexibilidad para mock en tests, pero es sobrediseño para este proyecto de tamaño medio.
- Helper function: no permite estado (el `S3Client` debe inicializarse una vez por request).

**Razón**: Library es el patrón estándar CI4 para wrappers de terceros. Permite fácil instanciación y el cliente S3 se construye una sola vez en el constructor.

### D2: Config/AWS.php en lugar de leer env() directamente en S3Service

**Decisión**: Crear `app/app/Config/AWS.php` que extiende `BaseConfig` con propiedades públicas (`region`, `bucket`, `key`, `secret`) leídas del `.env`.

**Razón**: Centraliza la configuración AWS siguiendo el patrón CI4 estándar (igual que `Config/Database.php`, `Config/Email.php`). S3Service lee `config('AWS')` en su constructor.

### D3: FileValidator separado de S3Service

**Decisión**: `FileValidator` es una Library independiente. El caller (controller) invoca primero `FileValidator::validate()`, luego mueve el archivo a temp, llama `FileValidator::checkMagicBytes()`, y finalmente `S3Service::upload()`.

**Razón**: Separación de responsabilidades. FileValidator no necesita saber de S3; S3Service no necesita saber de tipos de archivo. El controller coordina ambos.

### D4: checkMagicBytes lee solo 5 bytes

**Decisión**: `fread($handle, 5)` es suficiente para los 6 tipos soportados. DOCX/XLSX/PPTX se diferencian por extensión (todos comparten `PK\x03\x04`).

**Razón**: Verificación mínima pero efectiva. Un archivo `.exe` renombrado a `.pdf` no tendrá `%PDF-` como header. Para Office, la diferenciación real requeriría parsear el ZIP (formato OOXML), lo que es excesivo para este proyecto — se acepta la limitación documentada.

### D5: archive() = CopyObject + DeleteObject (no RenameObject)

**Decisión**: S3 no tiene operación rename nativa. `archive()` copia el objeto con nuevo key (`{base}_archived_{timestamp}.{ext}`) y luego elimina el original.

**Razón**: Es la única forma correcta de "mover" objetos en S3. Si la copia falla, el original permanece intacto.

## Risks / Trade-offs

| Riesgo | Mitigación |
|--------|-----------|
| Credenciales AWS inválidas o bucket incorrecto | S3Service retorna `false` y loggea el error; los controllers deben verificar el retorno y mostrar error al usuario |
| Presigned URL expira en 15 min (frontend la cachea) | El frontend no debe cachear la URL — debe pedirla al endpoint cada vez que el usuario abre el modal |
| DOCX/XLSX/PPTX comparten magic bytes `PK\x03\x04` | Documentado. Se acepta que checkMagicBytes() valida solo que es un ZIP válido; la diferenciación es por extensión |
| Timeout de upload para archivos grandes (≤ 20 MB) | `php.ini` ya tiene `max_execution_time = 60s`. Para 20 MB en conexión típica es suficiente |
| archive() falla a mitad (copy OK, delete falla) | El objeto queda duplicado en S3. Loggear el error con ambos keys para auditoría manual |

## Migration Plan

Esta propuesta no toca código existente. Los archivos nuevos se agregan sin romper nada:

1. Crear `app/app/Config/AWS.php`
2. Crear `app/app/Libraries/S3Service.php`
3. Crear `app/app/Libraries/FileValidator.php`
4. Verificar que `vendor/aws/aws-sdk-php` está presente
5. Test manual: subir un PDF real al bucket via `S3Service::upload()`
6. Test manual: generar presigned URL y acceder con el browser
7. Test manual: archivo `.exe` renombrado a `.pdf` → `FileValidator::checkMagicBytes()` retorna `false`

Rollback: eliminar los 3 archivos creados. No hay cambios en DB ni en archivos existentes.

## Open Questions

- ¿El bucket S3 de producción ya existe y tiene la política IAM correcta? (Ver `ARQUITECTURA.md §10`)
- ¿Se habilita S3 Versioning en el bucket? (recomendado para auditoría pero no requerido por el código)
