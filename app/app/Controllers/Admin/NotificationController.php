<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Libraries\MailService;

class NotificationController extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        $notificationModel = new NotificationModel();

        $sentNotifications = $notificationModel
            ->where('sender_id', session('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('layouts/admin', [
            'content' => view('admin/notifications', [
                'notifications' => $sentNotifications,
            ])
        ]);
    }

    public function send()
    {
        $request = service('request');

        $targetType = $request->getPost('target_type');
        $title = $request->getPost('title');
        $body = $request->getPost('body');
        $sendEmail = (bool) $request->getPost('send_email');

        if (!in_array($targetType, ['user', 'group'])) {
            return redirect()->back()->with('error', 'Tipo de destino inválido.');
        }

        if (empty($title) || empty($body)) {
            return redirect()->back()->with('error', 'Título y cuerpo son obligatorios.');
        }

        $notificationModel = new NotificationModel();
        $db = \Config\Database::connect();
        $mailService = new MailService();
        $senderId = session('user_id');

        $recipients = [];

        if ($targetType === 'user') {
            $targetId = $request->getPost('target_id');
            if (empty($targetId)) {
                return redirect()->back()->with('error', 'Debe seleccionar un usuario.');
            }

            $user = $db->table('users')
                ->select('id, email, nombres')
                ->where('id', $targetId)
                ->get()
                ->getRowArray();

            if (!$user) {
                return redirect()->back()->with('error', 'Usuario no encontrado.');
            }

            $recipients[] = $user;
        } else {
            $targetStatus = $request->getPost('target_status');
            if (empty($targetStatus)) {
                return redirect()->back()->with('error', 'Debe seleccionar un estado.');
            }

            $recipients = $db->table('users')
                ->select('id, email, nombres')
                ->where('status', $targetStatus)
                ->get()
                ->getResultArray();
        }

        $sentCount = 0;
        $emailFailures = 0;

        foreach ($recipients as $u) {
            $notificationModel->insert([
                'user_id'    => $u['id'],
                'sender_id'  => $senderId,
                'type'       => 'info',
                'title'      => $title,
                'body'       => $body,
                'send_email' => $sendEmail ? 1 : 0,
            ]);

            if ($sendEmail) {
                $mailSent = $mailService->sendAdminMessage($u, $title, $body);
                if (!$mailSent) {
                    $emailFailures++;
                    log_message('warning', "[NotificationController] Falló envío de email a usuario ID {$u['id']} ({$u['email']})");
                } else {
                    $db->table('notifications')
                        ->where('user_id', $u['id'])
                        ->where('sender_id', $senderId)
                        ->where('title', $title)
                        ->orderBy('created_at', 'DESC')
                        ->limit(1)
                        ->update(['email_sent_at' => date('Y-m-d H:i:s')]);
                }
            }

            $sentCount++;
        }

        $message = "Se enviaron {$sentCount} notificación(es).";
        if ($emailFailures > 0) {
            $message .= " {$emailFailures} fallo(s) de email (ver log).";
        }

        return redirect()->back()->with('success', $message);
    }
}
