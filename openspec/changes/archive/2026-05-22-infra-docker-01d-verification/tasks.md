## 1. Revisión de archivos críticos (antes de levantar)

- [x] 1.1 Leer `docker-compose.yml` — verificar que los `container_name` son exactamente `rcf_app`, `rcf_mysql`, `rcf_maildev` y la red es `red_interna`
- [x] 1.2 Leer `docker/apache/vhost.conf` — verificar que contiene `Alias /comisionfilm /var/www/html/public` y `AllowOverride All`
- [x] 1.3 Leer `app/public/.htaccess` — verificar que contiene `RewriteBase /comisionfilm/` (exactamente este valor)
- [x] 1.4 Leer `app/app/Config/App.php` — verificar que `$baseURL = 'http://localhost/comisionfilm/';` (con subfolder y trailing slash)
- [x] 1.5 Leer `app/.env` — verificar que `database.default.hostname = db` y `app.baseURL = 'http://localhost/comisionfilm/'`
- [x] 1.6 Verificar que NO existe la carpeta `app/writable/uploads/`

## 2. Criterio 1 — docker compose up --build sin errores

- [x] 2.1 Ejecutar `docker compose up --build` (o `docker compose up --build -d` para modo detached)
- [x] 2.2 Confirmar que el build del Dockerfile termina sin errores
- [x] 2.3 Ejecutar `docker ps` — verificar que `rcf_app`, `rcf_mysql` y `rcf_maildev` aparecen con estado `Up`
- [x] 2.4 Ejecutar `docker network ls` — verificar que existe la red con `red_interna` en el nombre

## 3. Criterio 2 — GET /comisionfilm/ retorna 200 CI4

- [x] 3.1 Abrir `http://localhost/comisionfilm/` en el navegador o ejecutar `curl -s -o /dev/null -w "%{http_code}" http://localhost/comisionfilm/`
- [x] 3.2 Verificar que la respuesta es HTTP 200
- [x] 3.3 Verificar que NO es DirectoryListing de Apache ni error 500 de PHP
- [x] 3.4 Si retorna 403 o 404: revisar `vhost.conf` — el `Alias /comisionfilm` puede estar ausente o mal configurado

## 4. Criterio 3 — GET /comisionfilm/login procesado por CI4

- [x] 4.1 Acceder a `http://localhost/comisionfilm/login`
- [x] 4.2 Verificar que CI4 procesa la ruta (respuesta es de CI4, no DirectoryListing ni 403 de Apache)
- [x] 4.3 Si retorna 404 de Apache: revisar que `AllowOverride All` está en el `<Directory>` del `vhost.conf`
- [x] 4.4 Si retorna error PHP sobre `RewriteBase`: verificar que `app/public/.htaccess` tiene `RewriteBase /comisionfilm/`

## 5. Criterio 4 — GET :1080 Maildev UI

- [x] 5.1 Abrir `http://localhost:1080` en el navegador
- [x] 5.2 Verificar que la UI de Maildev es visible (interfaz web de bandeja de entrada de desarrollo)
- [x] 5.3 Si no carga: verificar que `rcf_maildev` está corriendo con `docker ps | grep rcf_maildev`

## 6. Criterio 5 — aws-sdk y phpmailer en vendor

- [x] 6.1 Ejecutar: `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('Aws\\S3\\S3Client') ? 'aws OK' : 'aws FAIL';"`
- [x] 6.2 Verificar que la salida es `aws OK`
- [x] 6.3 Ejecutar: `docker exec rcf_app php -r "require 'vendor/autoload.php'; echo class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'phpmailer OK' : 'phpmailer FAIL';"`
- [x] 6.4 Verificar que la salida es `phpmailer OK`

## 7. Verificaciones adicionales de arquitectura

- [x] 7.1 Verificar extensiones PHP: `docker exec rcf_app php -m | grep -E "pdo_mysql|intl|mbstring|gd|zip"` — deben aparecer las 5
- [x] 7.2 Verificar versión PHP: `docker exec rcf_app php -v` — debe mostrar `PHP 8.2.x`
- [x] 7.3 Confirmar que NO existe `app/writable/uploads/`: `ls app/writable/` — solo `cache/` y `logs/`
- [x] 7.4 Verificar `app.secretKey` generado: `grep "app.secretKey" app/.env` — no debe ser el placeholder ni estar vacío
- [x] 7.5 Ejecutar `docker exec rcf_app php spark env` — debe mostrar `CI_ENVIRONMENT = development`

## 8. Resumen de resultado

- [x] 8.1 Documentar en un comentario o nota el resultado: todos los ✅ confirmados → **PROPUESTA 01 completa, listo para PROPUESTA 02**
- [x] 8.2 Si hay ❌: corregir en el bloque correspondiente (01-A, 01-B o 01-C) y re-ejecutar las verificaciones fallidas

## ⚠️ Anti-alucinación

- Los comandos `docker exec` usan `rcf_app` — nombre exacto del contenedor
- Si el criterio 3 falla con 404 de CI4 (no de Apache), eso es **correcto** — CI4 aún no tiene ruta `/login`; lo que se verifica es que **CI4** responde, no Apache
- `http://localhost/comisionfilm/` — la URL de verificación SIEMPRE incluye `/comisionfilm/`
- La red Docker puede aparecer como `comision-film_red_interna` (Docker prefija el nombre del proyecto) — eso es válido
- **NO crear** `writable/uploads/` como "fix" de ningún error — si algo falla por permisos de `writable/`, es un issue de permisos de carpeta, no de uploads
