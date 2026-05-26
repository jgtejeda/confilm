<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar sesión activa (user_id en sesión)
        if (! session()->get('user_id')) {
            return redirect()->to(site_url('login'))->with('error', 'Debes iniciar sesión primero');
        }

        // Verificar correo verificado — ARQUITECTURA.md §6
        // Si email_verified=0: redirigir a pantalla de verificación pendiente (P06)
        if (! session()->get('email_verified')) {
            return redirect()->to(site_url('verificar-pendiente'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}