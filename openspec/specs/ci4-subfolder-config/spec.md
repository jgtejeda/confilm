## Purpose
Configura CodeIgniter 4 para operar bajo el subfolder `/comisionfilm/` en desarrollo y producción, incluyendo baseURL, rewrite rules, y credenciales de entorno.

## Requirements

### Requirement: CI4 responde en el subfolder /comisionfilm/
CodeIgniter 4 SHALL responder con HTTP 200 en `http://localhost/comisionfilm/` y procesar rutas sin el segmento `index.php` en la URL.

#### Scenario: Página de bienvenida CI4 accesible
- **WHEN** se accede a `GET http://localhost/comisionfilm/`
- **THEN** el servidor retorna HTTP 200
- **THEN** el cuerpo de la respuesta contiene contenido generado por CI4 (no un error 404 de Apache)

#### Scenario: Rutas sin index.php funcionan
- **WHEN** se accede a `GET http://localhost/comisionfilm/cualquier-ruta`
- **THEN** CI4 procesa la solicitud (retorna 404 de CI4, no 404 de Apache DirectoryListing)

### Requirement: baseURL incluye /comisionfilm/ y termina con /
El valor de `$baseURL` en `app/app/Config/App.php` SHALL ser `'http://localhost/comisionfilm/'` — con el subfolder y terminando en `/`.

#### Scenario: site_url() genera URLs correctas
- **WHEN** se llama a `site_url('login')` dentro de CI4
- **THEN** retorna `http://localhost/comisionfilm/login`

#### Scenario: base_url() genera URLs correctas para assets
- **WHEN** se llama a `base_url('assets/css/main.css')` dentro de CI4
- **THEN** retorna `http://localhost/comisionfilm/assets/css/main.css`

### Requirement: RewriteBase /comisionfilm/ en .htaccess
El archivo `app/public/.htaccess` SHALL contener `RewriteBase /comisionfilm/` como primera directiva después de `RewriteEngine On`.

#### Scenario: .htaccess tiene RewriteBase correcto
- **WHEN** se lee el archivo `app/public/.htaccess`
- **THEN** contiene la línea `RewriteBase /comisionfilm/`

#### Scenario: Headers de seguridad presentes
- **WHEN** se lee el archivo `app/public/.htaccess`
- **THEN** contiene `X-Frame-Options "SAMEORIGIN"`, `X-Content-Type-Options "nosniff"`, `X-XSS-Protection` y `Referrer-Policy`

### Requirement: Dependencias Composer instaladas
El directorio `app/vendor/` SHALL contener `aws/aws-sdk-php` y `phpmailer/phpmailer` instalados via Composer dentro del contenedor `rcf_app`.

#### Scenario: aws-sdk-php disponible
- **WHEN** se ejecuta `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('Aws\S3\S3Client') ? 'OK' : 'FAIL';"`
- **THEN** la salida es `OK`

#### Scenario: phpmailer disponible
- **WHEN** se ejecuta `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('PHPMailer\PHPMailer\PHPMailer') ? 'OK' : 'FAIL';"`
- **THEN** la salida es `OK`

### Requirement: Config/Database.php lee credenciales de $_ENV
`app/app/Config/Database.php` SHALL leer `hostname`, `database`, `username` y `password` desde `$_ENV`, no con valores hardcodeados.

#### Scenario: Credenciales provenientes del entorno
- **WHEN** se lee `app/app/Config/Database.php`
- **THEN** el campo `hostname` usa `$_ENV['database.default.hostname']` o `env('database.default.hostname')` — no contiene `'localhost'` ni `'127.0.0.1'` hardcodeados

#### Scenario: Conexión exitosa a la base de datos
- **WHEN** el `app/.env` tiene las credenciales correctas y `rcf_mysql` está corriendo
- **THEN** `docker exec rcf_app php spark db:connect` retorna sin errores de conexión

### Requirement: NO existe carpeta writable/uploads
La carpeta `app/writable/uploads/` NO SHALL existir en ningún momento del proyecto.

#### Scenario: writable/uploads no creada
- **WHEN** se lista el contenido de `app/writable/`
- **THEN** no existe la subcarpeta `uploads/`
- **THEN** solo existen `cache/` y `logs/` bajo `writable/`
