## 1. Verificación previa

- [x] 1.1 Verificar `vendor/phpmailer/phpmailer/` existe en `app/vendor/` (P01)
- [x] 1.2 Verificar `Config/Email.php` actual — qué tiene configurado para no duplicar
- [x] 1.3 Verificar variables de entorno disponibles: `GMAIL_USER`, `GMAIL_APP_PASSWORD` en `.env`

## 2. MailService Library

- [x] 2.1 Crear `app/app/Libraries/MailService.php` namespace `App\Libraries`
- [x] 2.2 Constructor: detectar `ENVIRONMENT === 'development'` para configuración dual
- [x] 2.3 Método privado `send(string $to, string $toName, string $subject, string $viewName, array $data=[]): bool` con try/catch completo
- [x] 2.4 En send(): `$mail->CharSet = PHPMailer::CHARSET_UTF8` — SIEMPRE, antes de cualquier otra config
- [x] 2.5 Dev: `$mail->Host=maildev; $mail->Port=1025; $mail->SMTPAuth=false; $mail->SMTPAutoTLS=false; $mail->SMTPSecure=''`
- [x] 2.6 Prod: `$mail->Host=smtp.gmail.com; $mail->Port=587; $mail->SMTPAuth=true; $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS; $mail->Username=env('GMAIL_USER'); $mail->Password=env('GMAIL_APP_PASSWORD')`
- [x] 2.7 `$mail->setFrom(env('GMAIL_USER') ?: 'noreply@localhost', 'Registro Comisión Film')` — fromName EXACTO
- [x] 2.8 `$mail->Body = view($viewName, $data); $mail->isHTML(true);`
- [x] 2.9 Implementar `sendVerifyEmail(array $user, string $token): bool`
- [x] 2.10 Implementar `sendWelcome(array $user, string $rawPassword): bool`
- [x] 2.11 Implementar `sendRecovery(array $user, string $token): bool`
- [x] 2.12 Implementar `sendDocumentStatus(array $user, array $doc, string $docTypeName): bool` — detectar `$doc['status']` para elegir plantilla approved/rejected
- [x] 2.13 Implementar `sendInscriptionResult(array $user, array $inscription): bool` — detectar status para plantilla
- [x] 2.14 Implementar `sendAdminMessage(array $user, string $subject, string $body): bool`

## 3. Plantillas de email (inline CSS obligatorio)

- [x] 3.1 `emails/verify_email.php` — link prominente `site_url('verificar/'.$token)`, expira 24h
- [x] 3.2 `emails/welcome.php` — username, contraseña (rawPassword), link login `site_url('login')`
- [x] 3.3 `emails/recovery.php` — link reset `site_url('reset/'.$token)`, expira 1h
- [x] 3.4 `emails/document_approved.php` — nombre del tipo de documento
- [x] 3.5 `emails/document_rejected.php` — nombre del tipo + `$doc['rejection_note']`
- [x] 3.6 `emails/inscription_approved.php` — felicitación con nombre del usuario
- [x] 3.7 `emails/inscription_rejected.php` — motivo `$inscription['rejection_note']`
- [x] 3.8 `emails/admin_message.php` — subject y body dinámicos del admin

## 4. Verificación final

- [x] 4.1 Llamar sendVerifyEmail en dev → correo aparece en MailDev UI (localhost:1080)
- [x] 4.2 Correo de bienvenida muestra username y contraseña correctamente
- [x] 4.3 Links en correos incluyen `/comisionfilm/` (site_url correcto)
- [x] 4.4 Inspeccionar HTML de plantillas → cero `<link>` externos, todo CSS inline
- [x] 4.5 Forzar error de conexión → MailService retorna false, log en `writable/logs/`

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `$mail->CharSet = PHPMailer::CHARSET_UTF8` — PRIMERO, antes de cualquier otra configuración
2. Dev: `$mail->SMTPAutoTLS = false` — OBLIGATORIO para maildev (no soporta STARTTLS)
3. Dev: `$mail->SMTPSecure = ''` — string vacío, NO false ni null
4. Prod: `$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` — usar constante, no string 'tls'
5. fromName = `'Registro Comisión Film'` — EXACTAMENTE este string (ARQUITECTURA.md §14)
6. `view($viewName, $data)` dentro del try — si la vista falla, el catch lo captura
7. `send()` SIEMPRE retorna bool — NUNCA lanza excepción ni usa throw
8. PHPMailer está en namespace `PHPMailer\PHPMailer\PHPMailer` — `use PHPMailer\PHPMailer\PHPMailer`
9. Templates usan `site_url()` para links — con subfolder /comisionfilm/
10. NO usar `$mail->AltBody` solo si se quiere texto plano — en este proyecto HTML puro es suficiente
