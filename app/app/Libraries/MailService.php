<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * MailService — envío de correos transaccionales.
 *
 * Detecta si hay credenciales reales de Gmail configuradas;
 * si no, cae a maildev (dev local) para no bloquear pruebas.
 *
 * Spec: openspec/changes/mail-service
 */
class MailService
{
    // Devuelve true si hay credenciales Gmail reales en .env
    private function usesGmail(): bool
    {
        $pass = env('GMAIL_APP_PASSWORD', '');
        return !empty($pass) && !str_contains($pass, 'xxxx') && !str_contains($pass, 'PLACEHOLDER');
    }

    /**
     * Método central de envío.
     * SIEMPRE retorna bool — nunca lanza excepción.
     */
    private function send(string $to, string $toName, string $subject, string $viewName, array $data = []): bool
    {
        try {
            $mail = new PHPMailer(true);

            // CharSet PRIMERO — requerido por anti-alucinación §1
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->isSMTP();
            $mail->SMTPDebug  = 0;

            if ($this->usesGmail()) {
                // ── Producción / Gmail ────────────────────────────
                $mail->Host       = 'smtp.gmail.com';
                $mail->Port       = 587;
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Username   = env('GMAIL_USER');
                $mail->Password   = env('GMAIL_APP_PASSWORD');
            } else {
                // ── Desarrollo / Maildev ──────────────────────────
                $mail->Host       = 'maildev';
                $mail->Port       = 1025;
                $mail->SMTPAuth   = false;
                $mail->SMTPAutoTLS = false;  // obligatorio para maildev
                $mail->SMTPSecure = '';       // string vacío, no false
            }

            $fromEmail = env('GMAIL_USER') ?: 'noreply@localhost';
            $mail->setFrom($fromEmail, 'Registro Comisión Film');
            $mail->addAddress($to, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = view($viewName, $data);

            $mail->send();
            return true;

        } catch (MailerException $e) {
            log_message('error', '[MailService] PHPMailer error: ' . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            log_message('error', '[MailService] Error inesperado: ' . $e->getMessage());
            return false;
        }
    }

    // ── Métodos públicos ──────────────────────────────────────────────

    /**
     * Correo de verificación de email.
     *
     * @param array  $user  ['email', 'nombres', 'username', ...]
     * @param string $token Token de verificación (64 chars hex)
     */
    public function sendVerifyEmail(array $user, string $token): bool
    {
        return $this->send(
            $user['email'],
            $user['nombres'] ?? $user['username'] ?? 'Usuario',
            'Verifica tu correo electrónico — Comisión Film',
            'emails/verify_email',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Correo de bienvenida con credenciales.
     *
     * @param array  $user        ['email', 'nombres', 'username', ...]
     * @param string $rawPassword Contraseña en texto plano (solo al crear)
     */
    public function sendWelcome(array $user, string $rawPassword): bool
    {
        return $this->send(
            $user['email'],
            $user['nombres'] ?? $user['username'] ?? 'Usuario',
            'Bienvenido a Comisión Film — tus credenciales de acceso',
            'emails/welcome',
            ['user' => $user, 'rawPassword' => $rawPassword]
        );
    }

    /**
     * Correo de recuperación de contraseña.
     *
     * @param array  $user  ['email', 'nombres', 'username', ...]
     * @param string $token Token de recuperación
     */
    public function sendRecovery(array $user, string $token): bool
    {
        return $this->send(
            $user['email'],
            $user['nombres'] ?? $user['username'] ?? 'Usuario',
            'Recupera tu contraseña — Comisión Film',
            'emails/recovery',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Correo de estado de documento (aprobado/rechazado).
     *
     * @param array  $user        ['email', 'nombres', 'username', ...]
     * @param array  $doc         ['status' => 'approved'|'rejected', 'rejection_note' => '...']
     * @param string $docTypeName Nombre del tipo de documento (e.g. "INE", "Pasaporte")
     */
    public function sendDocumentStatus(array $user, array $doc, string $docTypeName): bool
    {
        $status = $doc['status'] ?? 'approved';
        $viewName = ($status === 'rejected') ? 'emails/document_rejected' : 'emails/document_approved';
        $subject = ($status === 'rejected')
            ? 'Documento rechazado — Comisión Film'
            : 'Documento aprobado — Comisión Film';

        return $this->send(
            $user['email'],
            $user['nombres'] ?? $user['username'] ?? 'Usuario',
            $subject,
            $viewName,
            ['user' => $user, 'doc' => $doc, 'docTypeName' => $docTypeName]
        );
    }

    /**
     * Correo de resultado de inscripción (aprobado/rechazado).
     *
     * @param array $user        ['email', 'nombres', 'username', ...]
     * @param array $inscription ['status' => 'approved'|'rejected', 'rejection_note' => '...']
     */
    public function sendInscriptionResult(array $user, array $inscription): bool
    {
        $status = $inscription['status'] ?? 'approved';
        $viewName = ($status === 'rejected') ? 'emails/inscription_rejected' : 'emails/inscription_approved';
        $subject = ($status === 'rejected')
            ? 'Inscripción rechazada — Comisión Film'
            : '¡Inscripción aprobada! — Comisión Film';

        return $this->send(
            $user['email'],
            $user['nombres'] ?? $user['username'] ?? 'Usuario',
            $subject,
            $viewName,
            ['user' => $user, 'inscription' => $inscription]
        );
    }

    /**
     * Correo administrativo (mensaje del admin a un usuario).
     *
     * @param array  $user    ['email', 'nombres', 'username', ...]
     * @param string $subject Asunto del mensaje
     * @param string $body    Cuerpo del mensaje (HTML permitido)
     */
    public function sendAdminMessage(array $user, string $subject, string $body): bool
    {
        return $this->send(
            $user['email'],
            $user['nombres'] ?? $user['username'] ?? 'Usuario',
            $subject,
            'emails/admin_message',
            ['user' => $user, 'subject' => $subject, 'body' => $body]
        );
    }
}
