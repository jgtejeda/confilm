## Context

Los tres bloques anteriores instalan y configuran la infraestructura. Este bloque de verificación cierra el ciclo de PROPUESTA 01 ejecutando los criterios de aceptación definidos en el ROADMAP y en ARQUITECTURA.md. Es el "gate" antes de empezar PROPUESTA 02 (migraciones de base de datos).

Los criterios de éxito originales del ROADMAP PROPUESTA 01 son:
1. `docker compose up --build` sin errores
2. `GET http://localhost/comisionfilm/` → 200 con página CI4
3. `GET http://localhost/comisionfilm/login` → CI4 procesa la ruta correctamente
4. `GET http://localhost:1080` → Maildev UI disponible
5. PHP: `aws/aws-sdk-php` y `phpmailer` están en `vendor/`

Adicionalmente se verifican los requisitos críticos de arquitectura que no tienen criterio de éxito explícito pero son bloqueantes para PROPUESTA 02.

## Goals / Non-Goals

**Goals:**
- Confirmar los 5 criterios de éxito de PROPUESTA 01
- Verificar que los nombres de contenedores son exactamente `rcf_app`, `rcf_mysql`, `rcf_maildev`
- Verificar que la red `red_interna` existe
- Verificar extensiones PHP: `pdo_mysql`, `intl`, `mbstring`, `gd`, `zip`
- Verificar que `writable/uploads/` NO existe
- Verificar que `RewriteBase /comisionfilm/` está en `.htaccess`
- Verificar que `$baseURL` incluye `/comisionfilm/` y termina en `/`
- Verificar que `database.default.hostname = db` en `app/.env`

**Non-Goals:**
- Verificar migraciones (PROPUESTA 02)
- Verificar conexión a S3 real (PROPUESTA 03)
- Verificar envío de correos (PROPUESTA 09)
- Verificar autenticación (PROPUESTA 07)

## Decisions

### D1: Verificación manual + comandos de consola
**Decisión**: Las verificaciones se hacen con `docker exec`, `curl` y lectura de archivos — no se crea un script de verificación automatizado.  
**Razón**: Mantiene el bloque simple y sin dependencias adicionales. Los comandos son claros y auditables.

### D2: Correcciones in-situ si se detectan problemas
**Decisión**: Si una verificación falla, la tarea incluye el comando/edición correctiva, no solo el diagnóstico.  
**Razón**: El objetivo del bloque es tener la infraestructura lista, no solo documentar problemas.

## Risks / Trade-offs

- **[Risk] Los Bloques 1-3 tienen errores no detectados** → Este bloque los detecta y corrige antes de avanzar.
- **[Trade-off] Verificación manual vs automatizada** → La verificación manual es más lenta pero no requiere escribir código de test para la infraestructura. Se puede automatizar en el futuro con un script de healthcheck.

## Migration Plan

Este bloque es en sí mismo el plan de verificación. No hay rollback — si algo falla, se corrige en el bloque anterior correspondiente y se re-verifica.

## Open Questions

_(ninguna)_
