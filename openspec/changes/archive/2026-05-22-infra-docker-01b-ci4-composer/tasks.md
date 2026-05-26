## 1. Instalar CodeIgniter 4 via Composer

- [x] 1.1 Verificar que el contenedor `rcf_app` está corriendo: `docker ps | grep rcf_app`
- [x] 1.2 Verificar que la carpeta `app/` está vacía o no existe antes de instalar
- [x] 1.3 Ejecutar dentro del contenedor: `docker exec rcf_app composer create-project codeigniter4/appstarter .`
- [x] 1.4 Confirmar que `app/app/`, `app/public/`, `app/writable/` y `app/vendor/` fueron creados

## 2. Instalar dependencias adicionales

- [x] 2.1 Instalar aws-sdk y phpmailer: `docker exec rcf_app composer require aws/aws-sdk-php phpmailer/phpmailer`
- [x] 2.2 Verificar que `app/vendor/aws/` existe después de la instalación
- [x] 2.3 Verificar que `app/vendor/phpmailer/` existe después de la instalación

## 3. Configurar .htaccess con RewriteBase /comisionfilm/

- [x] 3.1 Abrir `app/public/.htaccess` (generado por CI4 appstarter)
- [x] 3.2 Agregar `RewriteBase /comisionfilm/` después de `RewriteEngine On`
- [x] 3.3 Agregar (o verificar que existen) los headers de seguridad: `X-Frame-Options "SAMEORIGIN"`, `X-Content-Type-Options "nosniff"`, `X-XSS-Protection "1; mode=block"`, `Referrer-Policy "strict-origin-when-cross-origin"`
- [x] 3.4 Verificar que el bloque RewriteRule queda: `RewriteCond %{REQUEST_FILENAME} !-f`, `RewriteCond %{REQUEST_FILENAME} !-d`, `RewriteRule ^(.*)$ index.php/$1 [L]`

## 4. Configurar Config/App.php con baseURL

- [x] 4.1 Abrir `app/app/Config/App.php`
- [x] 4.2 Establecer `public string $baseURL = 'http://localhost/comisionfilm/';`
- [x] 4.3 Agregar la URL de producción comentada: `// public string $baseURL = 'https://intratur.guanajuato.gob.mx/comisionfilm/';`
- [x] 4.4 Verificar que `$baseURL` termina con `/` y contiene `/comisionfilm/`

## 5. Configurar Config/Database.php

- [x] 5.1 Abrir `app/app/Config/Database.php`
- [x] 5.2 Configurar `$default['hostname']` para leer de `$_ENV['database.default.hostname'] ?? 'db'`
- [x] 5.3 Configurar `$default['database']` para leer de `$_ENV['database.default.database'] ?? ''`
- [x] 5.4 Configurar `$default['username']` para leer de `$_ENV['database.default.username'] ?? ''`
- [x] 5.5 Configurar `$default['password']` para leer de `$_ENV['database.default.password'] ?? ''`
- [x] 5.6 Establecer `$default['DBDriver'] = 'MySQLi'` y `$default['port'] = 3306`

## 6. Verificación

- [x] 6.1 Acceder a `http://localhost/comisionfilm/` — debe retornar HTTP 200 con página CI4
- [x] 6.2 Acceder a `http://localhost/comisionfilm/una-ruta-falsa` — CI4 retorna su 404, no Apache DirectoryListing
- [x] 6.3 Verificar aws-sdk: `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('Aws\\S3\\S3Client') ? 'OK' : 'FAIL';"`
- [x] 6.4 Verificar phpmailer: `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'OK' : 'FAIL';"`
- [x] 6.5 Confirmar que NO existe `app/writable/uploads/`

## ⚠️ Anti-alucinación

- `$baseURL` DEBE ser `'http://localhost/comisionfilm/'` — con `/comisionfilm/` y terminando en `/`
- `RewriteBase` DEBE ser `/comisionfilm/` — exactamente este valor
- El hostname de la DB en `Database.php` es `db` (nombre del servicio Docker), no `localhost` ni `rcf_mysql`
- Composer se ejecuta **dentro del contenedor** con `docker exec rcf_app composer ...`
- **NO crear** la carpeta `writable/uploads/` — ningún archivo se guarda en el servidor
- Los comandos `docker exec` usan `rcf_app` — el nombre exacto del contenedor
