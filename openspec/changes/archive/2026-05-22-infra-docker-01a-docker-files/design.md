## Context

El proyecto vive en producción bajo `https://intratur.guanajuato.gob.mx/comisionfilm` — un subfolder del servidor del gobierno de Guanajuato. El entorno de desarrollo en Docker **debe replicar exactamente ese subfolder** desde el primer día para que las rutas CI4, el `.htaccess` y el `baseURL` sean idénticos en dev y prod.

El servidor web es **Apache 2.4** (el servidor de producción ya usa Apache). No hay razón para introducir Nginx; hacerlo crearía divergencia con producción.

Estado actual: repositorio vacío, sin Docker ni CI4 instalado.

## Goals / Non-Goals

**Goals:**
- Levantar `docker compose up --build` sin errores con los 3 contenedores (`rcf_app`, `rcf_mysql`, `rcf_maildev`)
- Servir `http://localhost/comisionfilm/` vía Apache con `mod_rewrite` activo
- Tener PHP 8.2 con todas las extensiones requeridas por CI4 y aws-sdk-php
- MySQL 8 inicializado con la base de datos `registro_comision_film`
- Maildev disponible en `:1080` para interceptar correos de desarrollo

**Non-Goals:**
- Instalación de CodeIgniter 4 (eso es Bloque 2 / propuesta `infra-docker-01b`)
- Configuración de SSL / HTTPS (producción lo maneja el servidor padre)
- `docker-compose.prod.yml` (fuera del alcance de este bloque)

## Decisions

### D1: Apache 2.4 — no Nginx
**Decisión**: Usar `php:8.2-apache` como imagen base, no `php:8.2-fpm` + Nginx.  
**Razón**: El servidor de producción (`intratur.guanajuato.gob.mx`) corre Apache. Mantener Apache en dev elimina diferencias de configuración de rewrite rules entre entornos.  
**Alternativa descartada**: Nginx + FPM — requeriría `try_files` en lugar de `mod_rewrite`, configuración divergente con producción.

### D2: Subfolder via `Alias` en vhost.conf (dev) + `RewriteBase` en .htaccess (prod)
**Decisión**: En desarrollo, el `vhost.conf` usa `Alias /comisionfilm /var/www/html/public`. En producción, el DocumentRoot del servidor padre ya apunta a otro lugar y el subfolder se gestiona con `Alias` en el vhost del servidor de producción (o el `.htaccess` del public/).  
**Razón**: CI4 necesita que `http://localhost/comisionfilm/` resuelva al `public/index.php`. El `Alias` en el vhost hace exactamente eso sin modificar la estructura de carpetas.  
**Implicación**: El `RewriteBase /comisionfilm/` en `app/public/.htaccess` es obligatorio (se configura en Bloque 2).

**Nota importante sobre vhost.conf**: Se mantiene el `DocumentRoot /var/www/html/public` AND el `Alias /comisionfilm`. Esto significa que tanto `http://localhost/` como `http://localhost/comisionfilm/` apuntan al mismo `public/`. En desarrollo siempre se usa la URL con el subfolder.

### D3: Credenciales MySQL via variables de entorno Docker
**Decisión**: `docker-compose.yml` lee `${DB_ROOT_PASSWORD}`, `${DB_DATABASE}`, `${DB_USERNAME}`, `${DB_PASSWORD}` del archivo `.env` raíz (no del `.env` de CI4).  
**Razón**: Separar credenciales de infraestructura (Docker) de configuración de aplicación (CI4 `.env`). El `.env` raíz es para Docker Compose, el `app/.env` es para CI4.

### D4: Red `red_interna` con driver bridge
**Decisión**: Todos los servicios usan la red `red_interna` (nombre exacto requerido por el proyecto).  
**Razón**: Aísla los contenedores del resto de redes Docker del sistema. Los servicios se comunican por nombre de contenedor (`rcf_mysql`, `rcf_maildev`) — no por IP.

## Risks / Trade-offs

- **[Risk] Puerto 80 ocupado** → Si el host ya tiene algo en el puerto 80, `docker compose up` falla. Mitigation: el usuario debe asegurarse de que el puerto 80 esté libre (OrbStack ya instalado lo gestiona bien).
- **[Risk] Puerto 3306 ocupado** → Si hay MySQL local corriendo. Mitigation: cambiar el mapeo de puertos en el `docker-compose.yml` local; el contenedor siempre escucha en 3306 internamente.
- **[Trade-off] `display_errors = On` en php.ini** → Expone errores PHP en el navegador. Es intencional para dev; en producción se usa un `php.ini` separado (o variable de entorno) con `display_errors = Off`.
- **[Risk] Imagen php:8.2-apache puede cambiar** → Fijar la versión exacta en el Dockerfile si se requiere reproducibilidad total. Por ahora `php:8.2-apache` es suficiente.

## Migration Plan

1. Crear todos los archivos de este bloque
2. Ejecutar `docker compose up --build`
3. Verificar contenedores: `docker ps` → deben aparecer `rcf_app`, `rcf_mysql`, `rcf_maildev`
4. Verificar red: `docker network ls` → debe aparecer `red_interna` (o `comision-film_red_interna`)
5. Si algo falla: `docker compose down -v` para limpiar y reintentar

**Rollback**: `docker compose down -v` elimina contenedores y volúmenes. No hay cambios en código de aplicación que revertir en este bloque.

## Open Questions

- ¿Se requiere fijar la versión exacta de la imagen `php:8.2-apache` (ej: `php:8.2.28-apache`)? Por ahora se usa `php:8.2-apache`.
- El `docker-compose.prod.yml` queda fuera de este bloque — se define en una propuesta futura de deploy.
