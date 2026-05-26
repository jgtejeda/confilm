<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class VerifyController extends BaseController
{
    public function pending()
    {
        $userId = session()->get('user_id') ?? session()->get('pending_user_id');

        if (! $userId) {
            return redirect()->to(site_url('login'));
        }

        $email = session()->get('email');

        if (! $email) {
            $userModel = new UserModel();
            $user = $userModel->find($userId);
            $email = $user['email'] ?? '';
        }

        return view('auth/verify_pending', [
            'email' => $email,
        ]);
    }

    public function confirm(string $token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('verify_token', $token)->first();

        if (! $user) {
            return view('auth/verify_error', [
                'title' => 'Link inválido',
                'message' => 'El link de verificación no es válido. Solicita un nuevo correo.',
                'show_resend' => false,
            ]);
        }

        if (strtotime($user['verify_exp']) <= time()) {
            return view('auth/verify_error', [
                'title' => 'Link expirado',
                'message' => 'El link de verificación ha expirado. Solicita un nuevo correo.',
                'show_resend' => true,
            ]);
        }

        // Generar contraseña definitiva y activar cuenta
        $rawPassword = \App\Libraries\PasswordGenerator::generate();

        $userModel->update($user['id'], [
            'email_verified' => 1,
            'status'         => 'active',
            'password_hash'  => password_hash($rawPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            'verify_token'   => null,
            'verify_exp'     => null,
        ]);

        // Enviar correo de bienvenida con credenciales (email + contraseña)
        $mailService = new \App\Libraries\MailService();
        $mailService->sendWelcome([
            'email'   => $user['email'],
            'nombres' => $user['nombres'],
        ], $rawPassword);

        return redirect()->to(site_url('login'))->with('success', '¡Correo verificado! Te enviamos tus credenciales de acceso.');
    }

    public function resend()
    {
        $userId = session()->get('user_id') ?? session()->get('pending_user_id');

        if (! $userId) {
            return redirect()->to(site_url('login'));
        }

        $resendCount = (int) session()->get('resend_count');
        $resendHour = session()->get('resend_hour');
        $currentHour = date('Y-m-d H');

        if ($resendHour !== $currentHour) {
            session()->set('resend_count', 1);
            session()->set('resend_hour', $currentHour);
        } else {
            if ($resendCount >= 3) {
                return redirect()->back()->with('error', 'Límite de reenvíos alcanzado. Intenta en una hora.');
            }
            session()->set('resend_count', $resendCount + 1);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        $token = bin2hex(random_bytes(32));
        $exp = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $userModel->update($userId, [
            'verify_token' => $token,
            'verify_exp' => $exp,
        ]);

        $mailService = new \App\Libraries\MailService();
        $mailService->sendVerifyEmail([
            'email'    => $user['email'],
            'nombres'  => $user['nombres'] ?? $user['username'],
            'username' => $user['username'],
        ], $token);

        return redirect()->back()->with('success', 'Correo de verificación reenviado.');
    }
}
