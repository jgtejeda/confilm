<?php

/**
 * Notification Helper
 * 
 * Centralized function to create system notifications.
 * Loaded via BaseController: $this->helpers[] = 'notification';
 */

if (!function_exists('create_notification')) {
    /**
     * Create a notification for a user.
     *
     * @param int    $userId    The user receiving the notification
     * @param string $type      Notification type: info, success, warning, error, document, inscription
     * @param string $title     Notification title
     * @param string $body      Notification body/message
     * @param bool   $sendEmail Whether to also send an email notification
     * @return void
     */
    function create_notification(int $userId, string $type, string $title, string $body, bool $sendEmail = false): void
    {
        $db = \Config\Database::connect();
        
        $db->table('notifications')->insert([
            'user_id'    => $userId,
            'sender_id'  => null,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'send_email' => (int) $sendEmail,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        if ($sendEmail) {
            try {
                $mailService = new \App\Libraries\MailService();
                
                $user = $db->table('users')
                    ->where('id', $userId)
                    ->get()
                    ->getRowArray();
                
                if ($user && !empty($user['email'])) {
                    $mailService->sendAdminMessage($user, $title, $body);
                    
                    $db->table('notifications')
                        ->where('id', $db->insertID())
                        ->update(['email_sent_at' => date('Y-m-d H:i:s')]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to send notification email: ' . $e->getMessage());
            }
        }
    }
}
