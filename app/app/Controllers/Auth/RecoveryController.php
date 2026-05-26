<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class RecoveryController extends BaseController
{
    public function index(): string
    {
        return view('auth/recovery');
    }

    public function sendLink()
    {
        $email = $this->request->getPost('email');

        if (empty($email)) {
            return redirect()->back()->with('info', 'Si ese correo está registrado, recibirás un link en breve.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $userModel->update($user['id'], [
                'recovery_token' => $token,
                'recovery_exp' => $exp,
            ]);

            $resetLink = site_url('reset/' . $token);

            log_message('info', 'Password reset link for ' . $email . ': ' . $resetLink);
        }

        return redirect()->back()->with('info', 'Si ese correo está registrado, recibirás un link en breve.');
    }

    public function resetForm($hash)
    {
        if (empty($hash)) {
            return redirect()->to(site_url('recuperar'))->with('error', 'Token inválido.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('recovery_token', $hash)->first();

        if (!$user) {
            return redirect()->to(site_url('recuperar'))->with('error', 'Token inválido o no encontrado.');
        }

        if (strtotime($user['recovery_exp']) <= time()) {
            return redirect()->to(site_url('recuperar'))->with('error', 'El link ha expirado. Solicita uno nuevo.');
        }

        return view('auth/reset_form', ['token' => $hash]);
    }

    public function resetProcess()
    {
        $rules = [
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('new_password');

        if (empty($token)) {
            return redirect()->to(site_url('recuperar'))->with('error', 'Token inválido.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('recovery_token', $token)->first();

        if (!$user) {
            return redirect()->to(site_url('recuperar'))->with('error', 'Token inválido o no encontrado.');
        }

        if (strtotime($user['recovery_exp']) <= time()) {
            return redirect()->to(site_url('recuperar'))->with('error', 'El link ha expirado. Solicita uno nuevo.');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $userModel->update($user['id'], [
            'password_hash' => $hashedPassword,
            'recovery_token' => null,
            'recovery_exp' => null,
        ]);

        return redirect()->to(site_url('login'))->with('success', 'Tu contraseña ha sido actualizada. Inicia sesión.');
    }
}
