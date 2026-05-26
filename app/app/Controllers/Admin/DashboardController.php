<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_users'          => $db->table('users')->where('role', 'user')->countAllResults(),
            'pending_review'       => $db->table('users')->where('status', 'pending')->countAllResults(),
            'docs_pending'         => $db->table('documents')->where('status', 'pending')->countAllResults(),
            'inscriptions_approved'=> $db->table('inscriptions')->where('status', 'approved')->countAllResults(),
        ];

        return view('layouts/admin', [
            'content' => view('admin/dashboard', ['stats' => $stats])
        ]);
    }
}
