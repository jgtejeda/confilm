## ADDED Requirements

### Requirement: MailService con configuración dual dev/prod
`MailService` SHALL detectar el entorno (`ENVIRONMENT`) y configurar PHPMailer apropiadamente: en dev usar maildev:1025 sin auth ni TLS; en prod usar smtp.gmail.com:587 con STARTTLS y App Password. El método privado `send()` SHALL retornar `bool` y nunca lanzar excepción al caller (catch interno con log_message).

#### Scenario: En dev los correos llegan a MailDev
- **WHEN** se llama cualquier método público de MailService en entorno development
- **THEN** el correo aparece en http://localhost:1080 (MailDev UI), sin errores en logs

#### Scenario: MailService retorna false y loggea en caso de error
- **WHEN** MailDev no está disponible o hay error de conexión
- **THEN** MailService::send() captura la excepción, llama log_message('error',...), retorna false sin propagar excepción

---

### Requirement: 8 métodos públicos de MailService
MailService SHALL exponer: `sendVerifyEmail(array $user, string $token): bool`, `sendWelcome(array $user, string $rawPassword): bool`, `sendRecovery(array $user, string $token): bool`, `sendDocumentStatus(array $user, array $doc, string $docTypeName): bool`, `sendInscriptionResult(array $user, array $inscription): bool`, `sendAdminMessage(array $user, string $subject, string $body): bool`. Todos usan el método privado `send()`.

#### Scenario: sendVerifyEmail genera link correcto
- **WHEN** se llama `sendVerifyEmail($user, $token)`
- **THEN** el correo enviado contiene el link `site_url('verificar/'.$token)` que resuelve a `http://localhost/comisionfilm/verificar/{token}`

#### Scenario: sendWelcome incluye username y contraseña
- **WHEN** se llama `sendWelcome($user, $rawPassword)`
- **THEN** el correo contiene el `username` generado y la contraseña en texto (solo en este correo de bienvenida)
