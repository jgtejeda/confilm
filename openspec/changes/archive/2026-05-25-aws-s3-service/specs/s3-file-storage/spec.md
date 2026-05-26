## ADDED Requirements

### Requirement: S3Service inicializa el cliente con credenciales del entorno
El sistema SHALL crear el `S3Client` en el constructor de `S3Service` usando exclusivamente las variables de entorno `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_REGION` y `AWS_S3_BUCKET`. Las credenciales NO deben estar hardcodeadas en ningún archivo PHP.

#### Scenario: Constructor lee configuración correctamente
- **WHEN** se instancia `new S3Service()`
- **THEN** el cliente S3 se construye con `version=latest`, `region` de `env('AWS_REGION')`, y `credentials.key`/`credentials.secret` de `env('AWS_ACCESS_KEY_ID')`/`env('AWS_SECRET_ACCESS_KEY')`
- **THEN** `$this->bucket` se asigna con `env('AWS_S3_BUCKET')`

---

### Requirement: S3Service::upload sube archivos con cifrado del lado servidor
El sistema SHALL subir archivos a S3 usando `PutObject`, con `ContentType` igual al `$mimeType` recibido y `ServerSideEncryption = 'AES256'`. El método DEBE retornar `true` en éxito y `false` en cualquier error, sin lanzar excepciones al caller.

#### Scenario: Subida exitosa de un archivo PDF
- **WHEN** se llama `$s3->upload('/tmp/abc.pdf', 'rcf/1/5/inicial/uuid.pdf', 'application/pdf')`
- **THEN** el objeto existe en S3 bajo la key `rcf/1/5/inicial/uuid.pdf`
- **THEN** el método retorna `true`

#### Scenario: Error de conexión o credenciales inválidas
- **WHEN** el SDK lanza una excepción (credenciales inválidas, bucket inexistente, timeout)
- **THEN** el método registra el error con `log_message('error', ...)`
- **THEN** el método retorna `false`
- **THEN** ninguna excepción se propaga al caller

---

### Requirement: S3Service::presignedUrl genera URL temporal de acceso
El sistema SHALL generar una URL pre-firmada de tipo `GetObject` válida por `$minutes` minutos (default 15). La URL DEBE permitir descargar el objeto sin necesidad de credenciales AWS.

#### Scenario: URL válida para un objeto existente
- **WHEN** se llama `$s3->presignedUrl('rcf/1/5/inicial/uuid.pdf', 15)`
- **THEN** retorna un string con URL HTTPS firmada
- **THEN** un `GET` a esa URL devuelve el contenido del archivo durante los primeros 15 minutos

#### Scenario: URL expirada no permite acceso
- **WHEN** han transcurrido más de 15 minutos desde la generación
- **THEN** un `GET` a la URL retorna HTTP 403 (Access Denied)

---

### Requirement: S3Service::archive copia y elimina el objeto original
El sistema SHALL copiar el objeto a un nuevo key con sufijo `_archived_{timestamp}` (preservando la extensión) y luego eliminar el original. Si la copia falla, el original NO se elimina.

#### Scenario: Archivado exitoso
- **WHEN** se llama `$s3->archive('rcf/1/5/inicial/uuid.pdf')`
- **THEN** existe un nuevo objeto en S3 con key `rcf/1/5/inicial/uuid_archived_{timestamp}.pdf`
- **THEN** el objeto original `rcf/1/5/inicial/uuid.pdf` ya no existe
- **THEN** el método retorna `true`

#### Scenario: Fallo durante la copia
- **WHEN** `CopyObject` lanza excepción
- **THEN** el original permanece intacto en S3
- **THEN** el método registra el error con `log_message('error', ...)` e retorna `false`

---

### Requirement: S3Service::delete elimina un objeto de S3
El sistema SHALL eliminar el objeto identificado por `$s3Key`. El método DEBE retornar `true` en éxito y `false` en error sin lanzar excepciones.

#### Scenario: Eliminación exitosa
- **WHEN** se llama `$s3->delete('rcf/1/5/inicial/uuid.pdf')`
- **THEN** el objeto ya no existe en S3
- **THEN** el método retorna `true`

#### Scenario: Error al eliminar
- **WHEN** el SDK lanza excepción durante `DeleteObject`
- **THEN** el método registra el error con `log_message('error', ...)` y retorna `false`

---

### Requirement: Estructura de keys S3 sigue el patrón definido
El sistema SHALL usar exclusivamente el formato `rcf/{period_id}/{user_id}/{categoria}/{uuid}.{ext}` para los keys de objetos en S3. `{categoria}` DEBE ser `inicial` o `complementario`.

#### Scenario: Key para documento inicial
- **WHEN** un usuario con `id=5` sube un documento inicial en el periodo `id=1`
- **THEN** el key en S3 es `rcf/1/5/inicial/{uuid}.pdf`

#### Scenario: Key para documento complementario
- **WHEN** un usuario con `id=5` sube un documento complementario en el periodo `id=1`
- **THEN** el key en S3 es `rcf/1/5/complementario/{uuid}.docx`
