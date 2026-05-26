## Context

CodeIgniter 4 necesita dos configuraciones críticas para funcionar en un subfolder:

1. **`$baseURL`** en `Config/App.php` — le dice a CI4 la URL base completa incluyendo el subfolder. Si no incluye `/comisionfilm/`, los helpers `site_url()` y `base_url()` generan URLs incorrectas.
2. **`RewriteBase /comisionfilm/`** en `public/.htaccess` — le dice a Apache mod_rewrite desde qué subfolder operar. Sin esto, las rutas `GET /comisionfilm/login` hacen que mod_rewrite busque un archivo `login` relativo a `/` en lugar de relativo a `/comisionfilm/`.

Ambos valores deben terminar con `/` y deben incluir `/comisionfilm/`.

El Composer se ejecuta **dentro del contenedor** `rcf_app` para usar el PHP 8.2 del contenedor, no el PHP del host (que puede ser diferente o no existir).

## Goals / Non-Goals

**Goals:**
- `GET http://localhost/comisionfilm/` retorna HTTP 200 con la página de bienvenida de CI4
- `GET http://localhost/comisionfilm/login` CI4 procesa la ruta (no hay `.php` en la URL)
- `aws/aws-sdk-php` y `phpmailer/phpmailer` están en `app/vendor/`
- `Config/Database.php` lee credenciales de `$_ENV` — no hardcodeadas

**Non-Goals:**
- Crear migraciones o modelos (Propuesta 02)
- Configurar filters de autenticación (Propuesta 07)
- Crear vistas o rutas de la aplicación (Propuestas 04+)
- Configurar el `.env` de CI4 (Bloque 3 / propuesta 01-C)

## Decisions

### D1: Composer dentro del contenedor, no en el host
**Decisión**: Ejecutar `docker exec -it rcf_app composer create-project ...` en lugar de correr Composer en el host.  
**Razón**: Garantiza que se usa PHP 8.2 y las extensiones correctas. El host puede tener PHP diferente o no tenerlo. El Dockerfile ya copia Composer desde `composer:latest`.  
**Alternativa descartada**: Instalar Composer en el host y ejecutarlo localmente — crea riesgo de incompatibilidad de versión PHP.

### D2: .htaccess con RewriteBase /comisionfilm/ (no en el vhost.conf)
**Decisión**: El `RewriteBase` va en `app/public/.htaccess`, no en `docker/apache/vhost.conf`.  
**Razón**: El `.htaccess` es el mecanismo que también funciona en producción (servidor Apache del gobierno). El `vhost.conf` de dev solo necesita el `Alias` y `AllowOverride All`. Separar las responsabilidades hace que el código sea idéntico en dev y prod.

### D3: Config/Database.php lee de $_ENV
**Decisión**: Las credenciales de base de datos en `Config/Database.php` leen `$_ENV['DB_HOSTNAME']`, etc., NO `getenv()` ni valores hardcodeados.  
**Razón**: CI4 carga el `.env` propio (`app/.env`) via DotEnv y lo pone en `$_ENV`. Usar `$_ENV` es el patrón estándar de CI4.  
**Nota**: El hostname de la DB es `db` (nombre del servicio Docker), no `localhost` ni `rcf_mysql`.

### D4: baseURL en App.php para dev, comentada para prod
**Decisión**: `$baseURL = 'http://localhost/comisionfilm/';` activa; la línea de producción `https://intratur.guanajuato.gob.mx/comisionfilm/` comentada justo abajo.  
**Razón**: Hace explícito el valor de producción para cuando se haga el deploy, sin necesidad de buscar la URL en documentación.

## Risks / Trade-offs

- **[Risk] `composer create-project` tarda varios minutos** dentro del contenedor si la imagen no tiene caché de Composer. Mitigation: esperar; es un proceso de una sola vez.
- **[Risk] `app/` ya existe con archivos** → `composer create-project` falla si el directorio destino tiene contenido. Mitigation: la tarea incluye verificar que `app/` esté vacío antes de ejecutar.
- **[Trade-off] Hostname `db` vs `rcf_mysql`** → CI4 usa `db` (nombre del servicio Docker) como hostname, no `rcf_mysql` (nombre del contenedor). Docker resuelve nombres de servicio en redes internas. Usar `rcf_mysql` también funciona porque es el `container_name`, pero `db` es más estándar en Compose.

## Migration Plan

1. Verificar que `rcf_app` está corriendo: `docker ps`
2. Crear proyecto CI4: `docker exec rcf_app composer create-project codeigniter4/appstarter .`
3. Instalar dependencias: `docker exec rcf_app composer require aws/aws-sdk-php phpmailer/phpmailer`
4. Editar `app/public/.htaccess` — agregar `RewriteBase /comisionfilm/`
5. Editar `app/app/Config/App.php` — `$baseURL = 'http://localhost/comisionfilm/'`
6. Editar `app/app/Config/Database.php` — leer credenciales de `$_ENV`
7. Verificar: `GET http://localhost/comisionfilm/` → 200

**Rollback**: Eliminar la carpeta `app/` y recrearla vacía. Los archivos Docker del Bloque 1 no se tocan.

## Open Questions

- ¿Se usa `$_ENV` o `env()` helper de CI4 para leer las variables en `Database.php`? → Se usa `$_ENV` directamente para compatibilidad con el cargado de `.env` de CI4.
- ¿Se hace `composer install` o `composer create-project`? → `create-project` para nuevo proyecto; `install` si `composer.json` ya existe.
