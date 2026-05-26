<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    public function index()
    {
        $notifModel = new NotificationModel();
        
        $notifications = $notifModel
            ->where('user_id', session('user_id'))
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        
        $pager = $notifModel->pager;
        
        return view('layouts/user', [
            'content' => view('user/notifications', [
                'notifications' => $notifications,
                'pager' => $pager,
            ])
        ]);
    }
    
    public function markRead($id)
    {
        $notifModel = new NotificationModel();
        
        $notif = $notifModel
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->first();
        
        if (!$notif) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Forbidden']);
        }
        
        $notifModel->update($id, ['read_at' => date('Y-m-d H:i:s')]);
        
        return $this->response->setJSON(['success' => true]);
    }
    
    public function unreadCount()
    {
        $notifModel = new NotificationModel();
        
        $count = $notifModel
            ->where('user_id', session('user_id'))
            ->where('read_at', null)
            ->countAllResults();
        
        return $this->response->setJSON(['count' => $count]);
    }
}
