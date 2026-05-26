## Context

PHPMailer v7.1 ya instalado (Composer, P01). Config/Email.php ya existe. MailDev disponible en `rcf_maildev:1025`. ARQUITECTURA.md §14 tiene la tabla completa de configuración dev vs prod y notas críticas de PHPMailer v7.

## Goals / Non-Goals

**Goals:** MailService con 8 métodos públicos, plantillas HTML con inline CSS, configuración dual dev/prod.
**Non-Goals:** Colas de correo, reintentos automáticos, attachments, bounce handling.

## Decisions

### D1 — PHPMailer v7.1 configuración crítica (ARQUITECTURA.md §14)
```php
$mail = new PHPMailer(true);
$mail->CharSet = PHPMailer::CHARSET_UTF8; // CRÍTICO — default es iso-8859-1 en v7
// DEV (maildev):
$mail->SMTPSecure  = '';    // sin cifrado
$mail->SMTPAutoTLS = false; // CRÍTICO — maildev no soporta STARTTLS
// PROD (Gmail):
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;
```

### D2 — Detección dev/prod
```php
$isDev = (ENVIRONMENT === 'development');
```
Si dev: Host=maildev, Port=1025, SMTPAuth=false, SMTPAutoTLS=false
Si prod: Host=smtp.gmail.com, Port=587, SMTPAuth=true, Username=env('GMAIL_USER'), Password=env('GMAIL_APP_PASSWORD')

### D3 — send() privado retorna bool
```php
private function send(string $to, string $subject, string $viewName, array $data = []): bool {
    try {
        // configurar PHPMailer, cargar vista como body HTML
        $mail->Body = view($viewName, $data);
        return $mail->send();
    } catch (\Exception $e) {
        log_message('error', 'MailService: '.$e->getMessage());
        return false;
    }
}
```

### D4 — fromName exacto (ARQUITECTURA.md §14)
`$mail->setFrom(env('GMAIL_USER') ?: 'noreply@localhost', 'Registro Comisión Film')` — el string fromName es EXACTAMENTE "Registro Comisión Film"

### D5 — Plantillas con inline CSS
Los clientes de correo no leen `<style>` externo. Todo CSS debe ser inline en los atributos `style=""`. Las vistas son PHP simples que generan HTML completo con `<!DOCTYPE html>`.

## Risks / Trade-offs
- **[Riesgo] Gmail App Password no configurada en dev** → Dev usa MailDev sin auth, no afecta. En prod es obligatoria.
- **[Trade-off] MailService retorna bool** → El caller nunca sabe exactamente qué falló. Se loggea el error completo con log_message('error', ...).
