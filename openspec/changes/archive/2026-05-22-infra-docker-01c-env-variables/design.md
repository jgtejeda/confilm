## Context

El proyecto usa **dos archivos `.env` separados**:

1. **`.env` raíz** — leído por Docker Compose vía interpolación `${VAR}`. Contiene las credenciales de MySQL para que Docker las inyecte en el contenedor. CI4 **no** lee este archivo directamente.

2. **`app/.env`** — leído por CI4 via su cargador DotEnv (`system/bootstrap.php`). Contiene toda la configuración de la aplicación: baseURL, database connection, sesiones, Gmail, AWS. CI4 pone estas variables en `$_ENV`, que es lo que lee `Config/Database.php`.

Esta separación es intencional: Docker no necesita saber de AWS ni Gmail; CI4 no necesita saber del password root de MySQL.

## Goals / Non-Goals

**Goals:**
- `docker compose up` funciona sin errores (`.env` raíz con `DB_*` variables)
- CI4 conecta a MySQL y carga la configuración correcta (`app/.env` con `database.*` y `app.baseURL`)
- Los `.env.example` documentan cada variable con comentarios y valores placeholder
- Ningún secreto real termina en el repositorio

**Non-Goals:**
- Configurar AWS S3 real (credenciales placeholder en `app/.env`)
- Configurar Gmail real (credenciales placeholder en `app/.env`)
- Configurar sesiones en base de datos (se activa en Propuesta 02 al crear la tabla `ci_sessions`)

## Decisions

### D1: Dos archivos .env separados (raíz y app/)
**Decisión**: `.env` raíz para Docker Compose; `app/.env` para CI4.  
**Razón**: Responsabilidades distintas. Docker Compose interpola `${DB_ROOT_PASSWORD}` en el YAML. CI4 lee su propio `app/.env` con `database.default.hostname=db`. Mezclarlos en uno solo requeriría que CI4 tenga acceso al password root de MySQL, lo cual viola el principio de mínimo privilegio.

### D2: .env.example versionado, .env NO versionado
**Decisión**: Los archivos `.env.example` se versionar (están fuera del `.gitignore`). Los `.env` reales no se versionan.  
**Razón**: `.env.example` es documentación de configuración — cualquier desarrollador clona el repo, copia el `.env.example` a `.env` y ajusta los valores. Sin él, no se sabe qué variables configurar.

### D3: Variables de sesión en app/.env apuntan a DatabaseHandler pero sin tabla aún
**Decisión**: `session.driver=DatabaseHandler` y `session.savePath=ci_sessions` se incluyen en `app/.env` desde este bloque.  
**Razón**: La variable debe estar presente para que CI4 no use el driver de archivos por default. La tabla `ci_sessions` se crea en Propuesta 02 (migraciones). Hasta entonces, las sesiones fallarán si se intenta usar CI4 — pero en este bloque solo verificamos la página de bienvenida, que no usa sesiones.

### D4: app.secretKey generado con php -r
**Decisión**: El `.env` de dev incluye una `app.secretKey` generada localmente con `php -r "echo bin2hex(random_bytes(32));"`.  
**Razón**: CI4 requiere esta clave para cifrar sesiones y cookies. Si está vacía o usa el default, CI4 lanza una advertencia de seguridad.

## Risks / Trade-offs

- **[Risk] Desarrollador comete el `.env` real por accidente** → Mitigation: `.gitignore` ya incluye `.env` desde Bloque 1. Agregar un pre-commit hook en el futuro como capa adicional.
- **[Risk] Credenciales AWS placeholder en `.env` de dev** → S3Service fallará si se llama antes de configurar credenciales reales. Mitigation: S3Service no se usa hasta Propuesta 03; para Bloque 4 de verificación solo se verifica Docker y CI4.
- **[Trade-off] session.driver=DatabaseHandler sin tabla** → CI4 mostrará error si se intenta iniciar sesión antes de crear la migración `ci_sessions`. Aceptable: este bloque no requiere sesiones funcionales.

## Migration Plan

1. Copiar `.env.example` a `.env` en la raíz y ajustar `DB_*` con valores de desarrollo
2. Copiar `app/.env.example` a `app/.env` y ajustar `database.*`, `app.baseURL`, generar `app.secretKey`
3. Reiniciar el contenedor `rcf_app` para que tome el nuevo `app/.env`: `docker compose restart app`
4. Verificar que CI4 lee las variables: `GET http://localhost/comisionfilm/` → sin errores de configuración

**Rollback**: Eliminar los `.env` creados. Los `.env.example` son seguros de mantener.

## Open Questions

- ¿Las credenciales reales de MySQL de desarrollo se comparten en el equipo? → Por ahora cada desarrollador usa las del `.env.example` como referencia y crea las suyas.
