# email-templates Specification

## Purpose
TBD - created by archiving change mail-service. Update Purpose after archive.
## Requirements
### Requirement: 8 plantillas HTML con inline CSS
Cada plantilla SHALL ser un archivo PHP en `Views/emails/` que genera HTML completo con DOCTYPE, estilos inline (sin `<style>` externo), usando las variables pasadas por `view($template, $data)`. Los links SHALL usar `site_url()` para incluir el subfolder /comisionfilm/.

#### Scenario: verify_email.php contiene link de verificación prominente
- **WHEN** se renderiza `emails/verify_email.php` con `['user'=>$user,'token'=>$token]`
- **THEN** el HTML contiene un botón/link con `href="<?= site_url('verificar/'.$token) ?>"` destacado visualmente

#### Scenario: welcome.php muestra credenciales de acceso
- **WHEN** se renderiza `emails/welcome.php` con `['user'=>$user,'rawPassword'=>$pass]`
- **THEN** el HTML muestra el username y la contraseña, y un link al login con `site_url('login')`

#### Scenario: document_rejected.php incluye motivo de rechazo
- **WHEN** se renderiza `emails/document_rejected.php` con `['user'=>$user,'doc'=>$doc,'docTypeName'=>$name]`
- **THEN** el HTML muestra el nombre del tipo de documento y `$doc['rejection_note']`

#### Scenario: Plantillas no usan archivos CSS externos
- **WHEN** se inspecciona cualquier plantilla de email
- **THEN** no existe ningún `<link rel="stylesheet">` ni `<style>` externo — todo CSS es inline en atributos `style=""`

