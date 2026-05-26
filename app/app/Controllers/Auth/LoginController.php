<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PeriodModel;
use Config\Database;

class LoginController extends BaseController
{
    public function index(): string
    {
        $periodModel = new PeriodModel();
        $period = $periodModel->where('active', 1)
            ->where('start_date <=', date('Y-m-d H:i:s'))
            ->where('end_date >=', date('Y-m-d H:i:s'))
            ->first();

        $docTypes = [];
        if ($period !== null) {
            $db = Database::connect();
            $docTypes = $db->table('period_document_types pdt')
                ->select('dt.*, pdt.sort_order as pdt_sort')
                ->join('document_types dt', 'dt.id = pdt.doc_type_id')
                ->where('pdt.period_id', $period['id'])
                ->where('dt.active', 1)
                ->where('dt.category', 'inicial')
                ->orderBy('pdt.sort_order', 'ASC')
                ->get()->getResultArray();
        }

        return view('layouts/auth', [
            'card'     => 'login',
            'period'   => $period,
            'docTypes' => $docTypes,
        ]);
    }

    public function process()
    {
        $credential = $this->request->getPost('email');
        $password   = $this->request->getPost('password');

        if (empty($credential) || empty($password)) {
            return redirect()->to(site_url('login'))->with('error', 'Email y contraseña son requeridos');
        }

        $ip = $this->request->getIPAddress();

        // Rate limiting: verificar intentos fallidos en los últimos 15 minutos
        $db = Database::connect();
        $fifteenMinAgo = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        $count = $db->table('login_attempts')
            ->where('identifier', $credential)
            ->where('ip_address', $ip)
            ->where('success', 0)
            ->where('attempted_at >', $fifteenMinAgo)
            ->countAllResults();

        if ($count >= 5) {
            return redirect()->to(site_url('login'))->with('error', 'Demasiados intentos fallidos. Intenta en 15 minutos.');
        }

        $userModel = new UserModel();

        // Buscar por email o username
        $user = $userModel->groupStart()
            ->where('email', $credential)
            ->orWhere('username', $credential)
            ->groupEnd()
            ->first();

        // Si no existe o contraseña incorrecta
        if (! $user || ! password_verify($password, $user['password_hash'])) {
            $this->logAttempt($credential, $ip, false);
            return redirect()->to(site_url('login'))->with('error', 'Credenciales incorrectas');
        }

        // Si la cuenta no está activa
        if ($user['status'] !== 'active') {
            $this->logAttempt($credential, $ip, false);
            return redirect()->to(site_url('login'))->with('error', 'Cuenta no activa');
        }

        // Login exitoso
        $this->logAttempt($credential, $ip, true);

        session()->regenerate(true);
        session()->set([
            'user_id'        => $user['id'],
            'username'       => $user['username'],
            'nombres'        => $user['nombres'],
            'email'          => $user['email'],
            'role'           => $user['role'],
            'email_verified' => (int) $user['email_verified'],
        ]);

        // Actualizar last_login
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        // Redirect según role
        if (in_array($user['role'], ['admin', 'superadmin'], true)) {
            return redirect()->to(site_url('admin'));
        }
        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }

    private function logAttempt(string $identifier, string $ip, bool $success): void
    {
        Database::connect()->table('login_attempts')->insert([
            'identifier'   => $identifier,
            'ip_address'   => $ip,
            'success'      => (int) $success,
            'attempted_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
