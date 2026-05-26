## ADDED Requirements

### Requirement: Criterio de éxito 1 — docker compose up --build sin errores
`docker compose up --build` SHALL completarse sin errores de build ni de arranque de contenedores.

#### Scenario: Build exitoso
- **WHEN** se ejecuta `docker compose up --build` en la raíz del proyecto
- **THEN** no aparecen mensajes de error en el output del build
- **THEN** los tres contenedores quedan en estado `Up` según `docker ps`

---

### Requirement: Criterio de éxito 2 — GET /comisionfilm/ retorna 200 CI4
`GET http://localhost/comisionfilm/` SHALL retornar HTTP 200 con contenido generado por CodeIgniter 4.

#### Scenario: Página de bienvenida CI4
- **WHEN** se accede a `http://localhost/comisionfilm/`
- **THEN** el servidor retorna HTTP 200
- **THEN** el cuerpo contiene indicios de CI4 (no un error PHP ni DirectoryListing de Apache)

---

### Requirement: Criterio de éxito 3 — GET /comisionfilm/login procesado por CI4
`GET http://localhost/comisionfilm/login` SHALL ser procesado por el router de CI4 (404 de CI4, no 404 de Apache).

#### Scenario: Router CI4 activo para subrutas
- **WHEN** se accede a `http://localhost/comisionfilm/login`
- **THEN** la respuesta NO es un DirectoryListing de Apache ni un 403
- **THEN** CI4 retorna su respuesta (ya sea la vista de login o un 404 de CI4 si la ruta aún no existe)

---

### Requirement: Criterio de éxito 4 — GET :1080 retorna Maildev UI
`GET http://localhost:1080` SHALL retornar HTTP 200 con la interfaz web de Maildev.

#### Scenario: Maildev UI accesible
- **WHEN** se accede a `http://localhost:1080`
- **THEN** el servidor retorna HTTP 200
- **THEN** la respuesta contiene la UI de Maildev

---

### Requirement: Criterio de éxito 5 — aws-sdk y phpmailer en vendor
`aws/aws-sdk-php` y `phpmailer/phpmailer` SHALL estar instalados y cargables vía autoload en el contenedor `rcf_app`.

#### Scenario: aws-sdk-php cargable
- **WHEN** se ejecuta `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('Aws\\S3\\S3Client') ? 'OK' : 'FAIL';"`
- **THEN** la salida es `OK`

#### Scenario: phpmailer cargable
- **WHEN** se ejecuta `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'OK' : 'FAIL';"`
- **THEN** la salida es `OK`

---

### Requirement: Verificación de arquitectura — nombres de contenedores exactos
Los contenedores SHALL tener exactamente los nombres `rcf_app`, `rcf_mysql` y `rcf_maildev`.

#### Scenario: Nombres de contenedores verificados
- **WHEN** se ejecuta `docker ps --format "{{.Names}}"`
- **THEN** la salida incluye exactamente: `rcf_app`, `rcf_mysql`, `rcf_maildev`

---

### Requirement: Verificación de arquitectura — writable/uploads NO existe
La carpeta `app/writable/uploads/` NO SHALL existir en ningún punto del proyecto.

#### Scenario: writable/uploads ausente
- **WHEN** se lista `app/writable/`
- **THEN** no existe ninguna carpeta `uploads` bajo `writable/`

---

### Requirement: Verificación de arquitectura — configuración subfolder correcta
Tanto `app/public/.htaccess` como `app/app/Config/App.php` SHALL contener la configuración correcta del subfolder `/comisionfilm/`.

#### Scenario: RewriteBase correcto
- **WHEN** se lee `app/public/.htaccess`
- **THEN** contiene `RewriteBase /comisionfilm/`

#### Scenario: baseURL correcta en App.php
- **WHEN** se lee `app/app/Config/App.php`
- **THEN** `$baseURL` es `'http://localhost/comisionfilm/'` (incluye subfolder, termina en `/`)
