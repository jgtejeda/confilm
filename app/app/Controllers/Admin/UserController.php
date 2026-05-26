<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\MailService;
use App\Libraries\PasswordGenerator;
use App\Models\UserModel;

class UserController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * 2.1 index(): lista paginada con filtros
     */
    public function index(): string
    {
        $query = $this->userModel->where('role', 'user');

        // Filtro por status
        $statusFilter = $this->request->getGet('status');
        if ($statusFilter && in_array($statusFilter, ['pending', 'active', 'rejected', 'suspended'], true)) {
            $query->where('status', $statusFilter);
        }

        // Búsqueda por nombres/email
        $search = $this->request->getGet('search');
        if ($search) {
            $query->groupStart()
                ->like('nombres', $search)
                ->orLike('apellido_pat', $search)
                ->orLike('apellido_mat', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $users = $query->orderBy('created_at', 'DESC')->paginate(20);
        $pager = $this->userModel->pager;

        return view('layouts/admin', [
            'content' => view('admin/users/index', [
                'users' => $users,
                'pager' => $pager,
                'statusFilter' => $statusFilter,
                'search' => $search,
            ])
        ]);
    }

    /**
     * 2.2 detail($id): usuario + documentos + inscripción actual
     */
    public function detail(int $id): string
    {
        $db = \Config\Database::connect();

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/usuarios'))->with('error', 'Usuario no encontrado');
        }

        // Documentos con JOIN a doc_types — solo el más reciente por tipo de documento
        // Subquery para obtener el id más alto (más reciente) por cada doc_type_id/period_id del usuario
        $subSql = "SELECT MAX(id) FROM documents WHERE user_id = {$id} GROUP BY doc_type_id, period_id";
        $documents = $db->table('documents d')
            ->select('d.*, dt.name as doc_type_name, dt.category as doc_category')
            ->join('document_types dt', 'dt.id = d.doc_type_id')
            ->where("d.id IN ({$subSql})")
            ->orderBy('dt.category', 'ASC')
            ->orderBy('d.uploaded_at', 'DESC')
            ->get()
            ->getResultArray();

        // Inscripción actual (la más reciente)
        $inscription = $db->table('inscriptions')
            ->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();

        return view('layouts/admin', [
            'content' => view('admin/users/detail', [
                'user' => $user,
                'documents' => $documents,
                'inscription' => $inscription ? (array)$inscription : null,
            ])
        ]);
    }

    /**
     * 2.3 edit($id): retornar form con datos del usuario
     */
    public function edit(int $id): string
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/usuarios'))->with('error', 'Usuario no encontrado');
        }

        return view('layouts/admin', [
            'content' => view('admin/users/edit', [
                'user' => $user,
            ])
        ]);
    }

    /**
     * 2.4 update($id): validar y actualizar usuario
     */
    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/usuarios'))->with('error', 'Usuario no encontrado');
        }

        $rules = [
            'nombres' => 'required|max_length[100]',
            'apellido_pat' => 'required|max_length[100]',
            'apellido_mat' => 'max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[user,admin,superadmin]',
            'status' => 'required|in_list[pending,active,rejected,suspended]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $data = [
            'nombres' => $this->request->getPost('nombres'),
            'apellido_pat' => $this->request->getPost('apellido_pat'),
            'apellido_mat' => $this->request->getPost('apellido_mat'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        // Si el email cambió: re-verificar
        $newEmail = $this->request->getPost('email');
        if ($newEmail !== $user['email']) {
            $data['email'] = $newEmail;
            $data['email_verified'] = 0;
            $data['verify_token'] = bin2hex(random_bytes(16));
            $data['verify_exp'] = date('Y-m-d H:i:s', strtotime('+24 hours'));
        }

        $this->userModel->update($id, $data);

        // Enviar correo de verificación si cambió email
        if ($newEmail !== $user['email']) {
            $updatedUser = $this->userModel->find($id);
            $mailService = new MailService();
            $mailService->sendVerifyEmail($updatedUser, $updatedUser['verify_token']);
        }

        return redirect()->to(site_url('admin/usuarios/' . $id))->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * 2.5 changeStatus($id): cambiar status del usuario
     */
    public function changeStatus(int $id): \CodeIgniter\HTTP\Response
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'error' => 'Usuario no encontrado'])->setStatusCode(404);
        }

        $body = $this->request->getJSON(true);
        $newStatus = $body['status'] ?? '';

        $validStatuses = ['pending', 'active', 'rejected', 'suspended'];
        if (!in_array($newStatus, $validStatuses, true)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Status inválido'])->setStatusCode(400);
        }

        $this->userModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }

    /**
     * 2.6 resetPassword($id): generar nueva contraseña y enviar por correo
     */
    public function resetPassword(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/usuarios'))->with('error', 'Usuario no encontrado');
        }

        $newPassword = PasswordGenerator::generate();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $this->userModel->update($id, ['password_hash' => $hash]);

        $mailService = new MailService();
        $mailService->sendWelcome($user, $newPassword);

        return redirect()->to(site_url('admin/usuarios/' . $id))->with('success', 'Contraseña reseteada y enviada por correo');
    }

    /**
     * 2.7 validateInscription($id): aprobar o rechazar inscripción
     */
    public function validateInscription(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $db = \Config\Database::connect();

        // 2.1 Obtener la inscripción del usuario (la más reciente)
        $inscription = $db->table('inscriptions')
            ->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();

        if (!$inscription) {
            return redirect()->to(site_url('admin/usuarios/' . $id))->with('error', 'El usuario no tiene inscripción');
        }

        $action = $this->request->getPost('action');
        $user = $this->userModel->find($id);
        $adminId = session('user_id');

        if ($action === 'approve') {
            // 2.2 COUNT documentos con status != 'approved' para ese user_id + period_id
            $pendingDocs = $db->table('documents')
                ->where('user_id', $id)
                ->where('period_id', $inscription['period_id'])
                ->where('status !=', 'approved')
                ->countAllResults();

            // 2.3 Si COUNT > 0: retornar error
            if ($pendingDocs > 0) {
                return redirect()->to(site_url('admin/usuarios/' . $id))->with('error', 'Hay ' . $pendingDocs . ' documento(s) sin aprobar. Apruébalos primero.');
            }

            // 2.4 COUNT = 0: UPDATE inscriptions + UPDATE users
            $db->table('inscriptions')
                ->where('id', $inscription['id'])
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => $adminId,
                    'reviewed_at' => date('Y-m-d H:i:s'),
                ]);

            $this->userModel->update($id, ['status' => 'active']);

            // 2.6 Notificación + email
            $this->tryCreateInscriptionNotification($id, 'approved', $user);
            $this->trySendInscriptionEmail($id, $user, ['status' => 'approved']);

            return redirect()->to(site_url('admin/usuarios/' . $id))->with('success', 'Inscripción aprobada correctamente');

        } elseif ($action === 'reject') {
            // 2.5 Validar rejection_note >= 30 chars
            $rejectionNote = $this->request->getPost('rejection_note');
            if (!$rejectionNote || strlen(trim($rejectionNote)) < 30) {
                return redirect()->to(site_url('admin/usuarios/' . $id))->with('error', 'El motivo de rechazo debe tener al menos 30 caracteres');
            }

            // UPDATE inscriptions (NO modificar users.status)
            $db->table('inscriptions')
                ->where('id', $inscription['id'])
                ->update([
                    'status' => 'rejected',
                    'rejection_note' => trim($rejectionNote),
                    'reviewed_by' => $adminId,
                    'reviewed_at' => date('Y-m-d H:i:s'),
                ]);

            // 2.6 Notificación + email
            $this->tryCreateInscriptionNotification($id, 'rejected', $user, trim($rejectionNote));
            $this->trySendInscriptionEmail($id, $user, ['status' => 'rejected', 'rejection_note' => trim($rejectionNote)]);

            return redirect()->to(site_url('admin/usuarios/' . $id))->with('success', 'Inscripción rechazada');
        }

        return redirect()->to(site_url('admin/usuarios/' . $id))->with('error', 'Acción inválida');
    }

    /**
     * delete($id): eliminar usuario, sus documentos e inscripciones
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/usuarios'))->with('error', 'Usuario no encontrado');
        }

        // No permitir eliminar admins
        if (in_array($user['role'], ['admin', 'superadmin'], true)) {
            return redirect()->to(site_url('admin/usuarios/' . $id))->with('error', 'No se puede eliminar un administrador');
        }

        $db = \Config\Database::connect();

        // Eliminar archivos de S3
        $documents = $db->table('documents')->where('user_id', $id)->get()->getResultArray();
        if (!empty($documents)) {
            $s3Service = new \App\Libraries\S3Service();
            foreach ($documents as $doc) {
                $s3Service->delete($doc['s3_key']);
            }
        }

        // Eliminar registros relacionados
        $db->table('documents')->where('user_id', $id)->delete();
        $db->table('inscriptions')->where('user_id', $id)->delete();
        $db->table('notifications')->where('user_id', $id)->delete();
        $db->table('login_attempts')->where('identifier', $user['email'])->delete();

        // Eliminar usuario
        $this->userModel->delete($id);

        return redirect()->to(site_url('admin/usuarios'))->with('success', 'Usuario eliminado correctamente');
    }

    private function tryCreateInscriptionNotification(int $userId, string $result, ?array $user, ?string $rejectionNote = null): void
    {
        if (function_exists('create_notification')) {
            $title = $result === 'approved' ? 'Inscripción aprobada' : 'Inscripción rechazada';
            $body = $result === 'approved'
                ? '¡Felicidades! Tu inscripción ha sido aprobada y tu cuenta ya está activa.'
                : 'Tu inscripción ha sido rechazada. Motivo: ' . ($rejectionNote ?? 'No especificado');

            create_notification($userId, 'inscription', $title, $body, false); // el email lo envía trySendInscriptionEmail()
        } else {
            log_message('info', 'Notif pendiente inscription user:' . $userId);
        }
    }

    private function trySendInscriptionEmail(int $userId, array $user, array $inscription): void
    {
        if (!$user) {
            log_message('error', 'Usuario no encontrado para email de inscripción');
            return;
        }

        $mailService = new MailService();
        $sent = $mailService->sendInscriptionResult($user, $inscription);
        if (!$sent) {
            log_message('error', 'Falló envío de email de inscripción, user_id: ' . $userId);
        }
    }
}
