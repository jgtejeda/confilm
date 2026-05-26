<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\MailService;
use App\Libraries\S3Service;
use App\Models\DocumentModel;
use App\Models\UserModel;

class DocumentController extends BaseController
{
    private DocumentModel $documentModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->userModel = new UserModel();
    }

    /**
     * approveOrReject($userId, $docId): aprobar o rechazar documento
     */
    public function approveOrReject(int $userId, int $docId): \CodeIgniter\HTTP\RedirectResponse
    {
        // Verificar ownership
        $doc = $this->documentModel->where('id', $docId)->where('user_id', $userId)->first();
        if (!$doc) {
            return redirect()->back()->with('error', 'Documento no encontrado o no pertenece a este usuario');
        }

        $action = $this->request->getPost('action');
        $note = trim($this->request->getPost('rejection_note') ?? '');

        if (!in_array($action, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Acción inválida');
        }

        if ($action === 'reject' && strlen($note) < 20) {
            return redirect()->back()->with('error', 'La nota de rechazo debe tener al menos 20 caracteres');
        }

        $newStatus = $action === 'approve' ? 'approved' : 'rejected';
        $adminId = session('user_id');

        $updateData = [
            'status' => $newStatus,
            'reviewed_by' => $adminId,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ];

        if ($action === 'reject') {
            $updateData['rejection_note'] = $note;
        }

        $this->documentModel->update($docId, $updateData);

        // Notificaciones
        $this->tryCreateNotification($doc['user_id'], $action, $doc, $note);
        $this->trySendEmail($doc['user_id'], $doc, $action);

        $message = $action === 'approve' ? 'Documento aprobado correctamente' : 'Documento rechazado correctamente';
        return redirect()->to(site_url('admin/usuarios/' . $userId))->with('success', $message);
    }

    /**
     * view($docId): retornar JSON con presigned URL
     */
    public function view(int $docId): \CodeIgniter\HTTP\Response
    {
        $doc = $this->documentModel->find($docId);
        if (!$doc) {
            return $this->response->setJSON(['error' => 'Documento no encontrado'])->setStatusCode(404);
        }

        $s3Service = new S3Service();
        $url = $s3Service->presignedUrl($doc['s3_key'], 15);

        if (empty($url)) {
            return $this->response->setJSON(['error' => 'No se pudo generar la URL del documento'])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'url' => $url,
            'mime_type' => $doc['mime_type'],
            'file_extension' => $doc['file_extension'],
            'original_name' => $doc['original_name'],
            'file_size' => $doc['file_size'],
        ]);
    }

    private function tryCreateNotification(int $userId, string $action, array $doc, string $note): void
    {
        if (function_exists('create_notification')) {
            $title = $action === 'approve' ? 'Documento aprobado' : 'Documento rechazado';
            $body = $action === 'approve'
                ? 'Tu documento ' . ($doc['doc_type_name'] ?? 'subido') . ' ha sido aprobado.'
                : 'Tu documento ' . ($doc['doc_type_name'] ?? 'subido') . ' ha sido rechazado. Motivo: ' . $note;

            create_notification($userId, 'document', $title, $body, false); // el email lo envía trySendEmail()
        } else {
            log_message('info', 'Notif pendiente doc:' . $doc['id']);
        }
    }

    private function trySendEmail(int $userId, array $doc, string $action): void
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            log_message('error', 'Usuario no encontrado para notificación de email, doc_id: ' . $doc['id']);
            return;
        }

        $mailService = new MailService();
        $docTypeName = $doc['doc_type_name'] ?? 'Documento';
        $doc['status'] = $action === 'approve' ? 'approved' : 'rejected';

        $sent = $mailService->sendDocumentStatus($user, $doc, $docTypeName);
        if (!$sent) {
            log_message('error', 'No se pudo enviar email de estado de documento, doc_id: ' . $doc['id']);
        }
    }
}
