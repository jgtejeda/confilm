## 1. Config/AWS.php

- [x] 1.1 Verificar que `vendor/aws/aws-sdk-php` existe en `app/vendor/` (NO ejecutar composer require)
- [x] 1.2 Crear `app/app/Config/AWS.php` que extienda `\CodeIgniter\Config\BaseConfig` con namespace `Config`
- [x] 1.3 Declarar propiedades públicas: `$region`, `$bucket`, `$key`, `$secret` — todas leídas con `env()` en el constructor o como valores default usando `$_ENV`
- [x] 1.4 Verificar que los nombres de las variables coinciden exactamente con `.env`: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_REGION`, `AWS_S3_BUCKET`

## 2. S3Service Library

- [x] 2.1 Crear `app/app/Libraries/S3Service.php` con namespace `App\Libraries`
- [x] 2.2 Agregar `use Aws\S3\S3Client;` y `use Aws\S3\S3UriParser;` (solo los imports necesarios del SDK)
- [x] 2.3 Implementar constructor: leer `config('AWS')`, inicializar `$this->client = new S3Client([...])` y `$this->bucket = $awsConfig->bucket`
- [x] 2.4 Implementar `upload(string $tempPath, string $s3Key, string $mimeType): bool` con `PutObject`, `ContentType` y `ServerSideEncryption = 'AES256'`; envolver en try/catch que loggee con `log_message('error', ...)` y retorne `false`
- [x] 2.5 Implementar `presignedUrl(string $s3Key, int $minutes = 15): string` creando un `GetObject` command y llamando `$this->client->createPresignedRequest()`; retornar `(string) $presignedRequest->getUri()`
- [x] 2.6 Implementar `archive(string $s3Key): bool`: extraer extensión del key, generar nuevo key con `_archived_{timestamp}`, llamar `CopyObject`, si OK llamar `DeleteObject`; try/catch en cada paso
- [x] 2.7 Implementar `delete(string $s3Key): bool` con `DeleteObject`; try/catch → `log_message` → retornar `false` en error
- [x] 2.8 Verificar que NINGÚN método lanza excepción al caller — todos tienen try/catch completo

## 3. FileValidator Library

- [x] 3.1 Crear `app/app/Libraries/FileValidator.php` con namespace `App\Libraries`
- [x] 3.2 Declarar propiedad privada `$magicBytes` con los 6 tipos exactos:
       `'pdf' => '%PDF-'`, `'png' => "\x89PNG"`, `'jpg' => "\xFF\xD8\xFF"`,
       `'docx' => "PK\x03\x04"`, `'xlsx' => "PK\x03\x04"`, `'pptx' => "PK\x03\x04"`
- [x] 3.3 Declarar propiedad privada `$mimeTypes` con los 6 MIME types correspondientes (ver `ARQUITECTURA.md §9`)
- [x] 3.4 Implementar `validate(UploadedFile $file, array $allowedTypes, int $maxSizeMb): array`:
       — Obtener extensión con `strtolower($file->getClientExtension())`
       — Verificar extensión en `$allowedTypes`; si no: agregar mensaje de error
       — Verificar tamaño `$file->getSize() > $maxSizeMb * 1024 * 1024`; si excede: agregar mensaje
       — Retornar array de errores (vacío = válido)
- [x] 3.5 Implementar `checkMagicBytes(string $tempPath, string $ext): bool`:
       — `$handle = fopen($tempPath, 'rb')`, `$header = fread($handle, 5)`, `fclose($handle)`
       — `return isset($this->magicBytes[$ext]) && str_starts_with($header, $this->magicBytes[$ext])`
- [x] 3.6 Verificar que `validate()` NO llama `checkMagicBytes()` internamente — son pasos separados que el controller coordina

## 4. Verificación manual en Docker

- [x] 4.1 Ejecutar `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo 'AWS SDK OK';"` para confirmar que el SDK está disponible
- [x] 4.2 Test `upload()`: crear un script de prueba temporal o usar tinker/spark para subir un PDF de prueba; verificar que aparece en la consola S3 bajo `rcf/`
- [x] 4.3 Test `presignedUrl()`: generar URL con el key del archivo subido; hacer GET con browser/curl → debe descargar el archivo
- [x] 4.4 Test `FileValidator`: crear un archivo `.exe` renombrado a `.pdf` (o simplemente un TXT renombrado); pasar por `checkMagicBytes()` → debe retornar `false`
- [x] 4.5 Test `FileValidator`: subir un JPG real con extensión `.jpg`; pasar por `validate()` y `checkMagicBytes()` → ambos deben retornar éxito
- [x] 4.6 Test `FileValidator`: archivo de 6 MB con límite de 5 MB → `validate()` debe retornar array con error de tamaño
- [x] 4.7 Revisar `writable/logs/log-{fecha}.log` para confirmar que no hay errores PHP (namespace, import, sintaxis)

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN — LEER ANTES DE IMPLEMENTAR

- **AWS SDK YA INSTALADO**: `vendor/aws/aws-sdk-php` existe — NO modificar `composer.json` ni ejecutar `composer require`
- **Bucket name**: `env('AWS_S3_BUCKET')` — NO hardcodear string
- **Credenciales**: solo de `env('AWS_ACCESS_KEY_ID')` y `env('AWS_SECRET_ACCESS_KEY')`
- **S3Client config exacta**: `['version' => 'latest', 'region' => env('AWS_REGION'), 'credentials' => ['key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY')]]`
- **S3Service NUNCA lanza excepción**: todos los métodos tienen try/catch → log_message → retornar bool
- **FileValidator::validate()** usa `$file->getClientExtension()` y `$file->getSize()` (métodos de CI4 `UploadedFile`)
- **checkMagicBytes()** lee exactamente 5 bytes: `fread($handle, 5)`
- **DOCX/XLSX/PPTX** comparten magic bytes `PK\x03\x04` — se diferencian por extensión, no por bytes
- **Config/AWS.php**: namespace `Config`, extiende `\CodeIgniter\Config\BaseConfig`
- **S3Service y FileValidator**: namespace `App\Libraries`
- **NO existe** `writable/uploads/` — los archivos nunca se guardan en el servidor
- **La tabla `documents`** ya tiene las columnas correctas: `s3_key`, `s3_bucket`, `file_extension`, `period_id`, `original_name`, `stored_name` — NO inventar columnas
- **Relaciones DB**: `documents.period_id` es FK a `periods.id` NOT NULL, `documents.user_id` FK a `users.id`
- **Verificar** que no hay errores de sintaxis PHP 8.2: cerrar correctamente todos los try/catch, verificar tipos de retorno declarados, verificar que todos los `use` statements apuntan a clases que existen en el SDK
