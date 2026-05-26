## Why

CI4 y Docker Compose necesitan variables de entorno para funcionar: credenciales de base de datos, configuración de sesiones, claves AWS y credenciales de Gmail. Sin el `.env` correcto, `docker compose up` falla al no encontrar `${DB_ROOT_PASSWORD}` y CI4 no puede conectarse a MySQL. El `.env.example` documenta todas las variables requeridas para que cualquier desarrollador nuevo sepa qué configurar.

## What Changes

- `.env` — archivo raíz para Docker Compose (credenciales MySQL, no versionado)
- `app/.env` — archivo CI4 con todas las variables de la aplicación (no versionado)
- `.env.example` — plantilla raíz con todas las claves de Docker Compose y valores placeholder (sí versionado)
- `app/.env.example` — plantilla CI4 con todas las claves de la aplicación y valores placeholder (sí versionado)

## Capabilities

### New Capabilities

- `env-configuration`: Archivos `.env` configurados con todas las variables del proyecto según ARQUITECTURA.md §18, con archivos `.env.example` versionados como documentación de configuración

### Modified Capabilities

_(ninguna)_

## Impact

- **Archivos NO versionados** (en `.gitignore`): `.env`, `app/.env`
- **Archivos versionados** (documentación): `.env.example`, `app/.env.example`
- **Variables cubre**: CI_ENVIRONMENT, app.baseURL, database.*, DB_*, GMAIL_*, AWS_*, session.*
- **Dependencia**: Requiere Bloques 1 y 2 completados (Docker + CI4 instalado)
