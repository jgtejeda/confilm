<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DocumentModel;
use App\Models\InscriptionModel;
use App\Models\PeriodModel;
use App\Libraries\PasswordGenerator;
use App\Libraries\S3Service;
use App\Libraries\FileValidator;
use Config\Database;

class RegisterController extends BaseController
{
    public function index(): string
    {
        $periodModel = new PeriodModel();

        $period = $periodModel->where('active', 1)
            ->where('start_date <=', date('Y-m-d H:i:s'))
            ->where('end_date >=', date('Y-m-d H:i:s'))
            ->first();

        if ($period === null) {
            return view('auth/no_period');
        }

        $db = Database::connect();
        $docTypes = $db->table('period_document_types pdt')
            ->select('dt.*, pdt.sort_order as pdt_sort')
            ->join('document_types dt', 'dt.id = pdt.doc_type_id')
            ->where('pdt.period_id', $period['id'])
            ->where('dt.active', 1)
            ->where('dt.category', 'inicial')
            ->orderBy('pdt.sort_order', 'ASC')
            ->get()->getResultArray();

        return view('layouts/auth', [
            'card' => 'register',
            'period' => $period,
            'docTypes' => $docTypes
        ]);
    }

    public function process()
    {
        $periodModel = new PeriodModel();

        $period = $periodModel->where('active', 1)
            ->where('start_date <=', date('Y-m-d H:i:s'))
            ->where('end_date >=', date('Y-m-d H:i:s'))
            ->first();

        if ($period === null) {
            return redirect()->to('registro')->with('error', 'No hay convocatoria activa');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombres' => 'required|min_length[2]|max_length[100]',
            'apellido_pat' => 'required|min_length[2]|max_length[80]',
            'apellido_mat' => 'permit_empty|max_length[80]',
            'phone' => 'required|regex_match[/^\d{10}$/]',
            'email' => 'required|valid_email|is_unique[users.email]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->to(site_url('registro'))->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $db = Database::connect();
        $docTypes = $db->table('period_document_types pdt')
            ->select('dt.*, pdt.sort_order as pdt_sort')
            ->join('document_types dt', 'dt.id = pdt.doc_type_id')
            ->where('pdt.period_id', $period['id'])
            ->where('dt.active', 1)
            ->where('dt.category', 'inicial')
            ->orderBy('pdt.sort_order', 'ASC')
            ->get()->getResultArray();

        $fileValidator = new FileValidator();
        $errors = [];

        foreach ($docTypes as $dt) {
            $file = $this->request->getFile('doc_' . $dt['id']);

            if ($file === null || !$file->isValid()) {
                $errors['doc_' . $dt['id']] = 'El documento ' . $dt['name'] . ' es requerido';
                continue;
            }

            // allowed_types puede ser JSON array o string simple (application/pdf)
            $decoded = json_decode($dt['allowed_types'], true);
            $allowedTypes = is_array($decoded) ? $decoded : array_map('trim', explode(',', $dt['allowed_types']));
            $fileErrors = $fileValidator->validate($file, $allowedTypes, $dt['max_size_mb']);

            if (!empty($fileErrors)) {
                $errors['doc_' . $dt['id']] = implode(', ', $fileErrors);
                continue;
            }

            $tempPath = $file->getTempName();
            $ext = strtolower($file->getClientExtension());

            if (!$fileValidator->checkMagicBytes($tempPath, $ext)) {
                $errors['doc_' . $dt['id']] = 'Tipo de archivo no permitido para ' . $dt['name'];
                continue;
            }
        }

        if (!empty($errors)) {
            return redirect()->to(site_url('registro'))->withInput()->with('errors', $errors);
        }

        $db->transStart();

        $userModel = new UserModel();
        $verifyToken = bin2hex(random_bytes(32));
        $verifyExp   = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $emailValue  = $this->request->getPost('email');

        $userId = $userModel->insert([
            'username'      => $emailValue,          // email como identificador
            'email'         => $emailValue,
            'phone'         => $this->request->getPost('phone'),
            'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT, ['cost' => 12]), // temporal
            'nombres'       => $this->request->getPost('nombres'),
            'apellido_pat'  => $this->request->getPost('apellido_pat'),
            'apellido_mat'  => $this->request->getPost('apellido_mat') ?: null,
            'status'        => 'pending',
            'email_verified'=> 0,
            'verify_token'  => $verifyToken,
            'verify_exp'    => $verifyExp,
        ], true);

        if ($userId === false) {
            $db->transRollback();
            return redirect()->to(site_url('registro'))->withInput()->with('error', 'Error al crear el usuario');
        }

        $s3Service = new S3Service();
        $documentModel = new DocumentModel();
        $uploadedKeys = [];
        $s3UploadFailed = false;

        // Carpeta personalizada: comisionfilm/NombreApellido_userId/
        $nombres     = $this->request->getPost('nombres');
        $apellidoPat = $this->request->getPost('apellido_pat');
        $folderName  = $this->slugifyName($nombres, $apellidoPat, $userId);
        $awsPrefix   = config('AWS')->prefix;  // "comisionfilm"

        foreach ($docTypes as $dt) {
            $file = $this->request->getFile('doc_' . $dt['id']);
            $ext = strtolower($file->getClientExtension());
            $uuid = bin2hex(random_bytes(16));
            // Ruta: comisionfilm/JuanPerez_42/uuid.pdf
            $s3Key = $awsPrefix . '/' . $folderName . '/' . $uuid . '.' . $ext;
            $tempPath = $file->getTempName();
            $mimeType = $file->getMimeType();

            $uploaded = $s3Service->upload($tempPath, $s3Key, $mimeType);

            if (!$uploaded) {
                $s3UploadFailed = true;
                break;
            }

            $uploadedKeys[] = $s3Key;

            $documentModel->insert([
                'user_id' => $userId,
                'doc_type_id' => $dt['id'],
                'period_id' => $period['id'],
                'original_name' => $file->getClientName(),
                'stored_name' => $uuid . '.' . $ext,
                's3_key' => $s3Key,
                's3_bucket' => env('AWS_S3_BUCKET'),
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
                'file_extension' => $ext,
                'status' => 'pending',
            ]);
        }

        if ($s3UploadFailed) {
            foreach ($uploadedKeys as $key) {
                $s3Service->delete($key);
            }
            $db->transRollback();
            return redirect()->to(site_url('registro'))->withInput()->with('error', 'Error al subir archivos a S3');
        }

        $inscriptionModel = new InscriptionModel();
        $inscriptionModel->insert([
            'user_id' => $userId,
            'period_id' => $period['id'],
            'status' => 'incomplete',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            foreach ($uploadedKeys as $key) {
                $s3Service->delete($key);
            }
            return redirect()->to(site_url('registro'))->withInput()->with('error', 'Error en la transacción de base de datos');
        }

        // Solo enviar correo de verificación — las credenciales llegan al verificar
        $userForMail = [
            'email'   => $email,
            'nombres' => $this->request->getPost('nombres'),
        ];

        $mailService = new \App\Libraries\MailService();
        $mailService->sendVerifyEmail($userForMail, $verifyToken);

        session()->set('pending_user_id', $userId);

        return redirect()->to(site_url('verificar-pendiente'));
    }

    /**
     * Genera el nombre de la carpeta S3 a partir del nombre y apellido.
     * Ejemplo: "Juan Carlos", "Pérez Ruiz", 42  →  "JuanCarlos_PerezRuiz_42"
     * Sin acentos, sin espacios, sin caracteres especiales.
     */
    private function slugifyName(string $nombres, string $apellidoPat, int $userId): string
    {
        $map = [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',
            'ñ'=>'n','Ñ'=>'N','ü'=>'u','Ü'=>'U',
        ];

        $clean = static function (string $str) use ($map): string {
            $str = strtr($str, $map);
            // Capitalizar cada palabra y unirlas sin espacio
            $words = preg_split('/\s+/', trim($str));
            $result = '';
            foreach ($words as $w) {
                $result .= ucfirst(strtolower($w));
            }
            // Eliminar cualquier carácter que no sea letra o número
            return preg_replace('/[^a-zA-Z0-9]/', '', $result);
        };

        return $clean($nombres) . '_' . $clean($apellidoPat) . '_' . $userId;
    }
}