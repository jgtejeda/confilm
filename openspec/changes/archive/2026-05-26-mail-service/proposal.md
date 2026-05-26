## Why

Todos los flujos de auth y validación necesitan enviar correos (bienvenida, verificación, recuperación, resultados de documentos/inscripción). MailService centraliza el envío con PHPMailer v7.1 y abstrae dev (MailDev) vs prod (Gmail SMTP). Los stubs de log que usan P05-P08 se reemplazan con llamadas reales.

## What Changes

- **NUEVO** `app/app/Libraries/MailService.php` — 8 métodos públicos + send() privado; dev: maildev:1025, prod: smtp.gmail.com:587
- **NUEVO** `app/app/Views/emails/verify_email.php`, `welcome.php`, `recovery.php`, `document_approved.php`, `document_rejected.php`, `inscription_approved.php`, `inscription_rejected.php`, `admin_message.php` — 8 plantillas HTML con inline CSS
- **VERIFICAR** `app/app/Config/Email.php` — ya existe, confirmar configuración dev/prod

## Capabilities

### New Capabilities

- `mail-smtp-config`: MailService con PHPMailer v7.1, configuración dual dev/prod, retorna bool sin lanzar excepción al caller
- `email-templates`: 8 plantillas HTML con inline CSS para cada evento del sistema

### Modified Capabilities

(ninguna — reemplaza stubs de log sin cambiar la firma de los callers)

## Impact

- Nuevos: `Libraries/MailService.php`, 8 archivos en `Views/emails/`
- Usa PHPMailer v7.1 (ya instalado vía Composer desde P01)
- Variables de entorno: `GMAIL_USER`, `GMAIL_APP_PASSWORD` (dev: maildev, no necesita auth)
- Sin cambios en DB, modelos, S3 ni rutas
