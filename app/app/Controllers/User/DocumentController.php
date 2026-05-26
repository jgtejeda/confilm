<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\DocumentModel;

class DocumentController extends BaseController
{
    private DocumentModel $documentModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
    }

    /**
     * view($id): verificar ownership y retornar JSON con presigned URL
     */
    public function view(int $id): \CodeIgniter\HTTP\Response
    {
        $userId = session('user_id');
        
        // Ownership check: documents.user_id = session('user_id')
        $doc = $this->documentModel
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$doc) {
            return $this->response->setJSON(['error' => 'Forbidden'])->setStatusCode(403);
        }

        $s3Service = new \App\Libraries\S3Service();
        $url = $s3Service->presignedUrl($doc['s3_key'], 15);

        if (empty($url)) {
            return $this->response->setJSON(['error' => 'No se pudo generar la URL del documento'])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'url'            => $url,
            'mime_type'      => $doc['mime_type'],
            'file_extension' => $doc['file_extension'],
            'original_name'  => $doc['original_name'],
            'file_size'      => $doc['file_size'],
            'status'         => $doc['status'],
            'rejection_note' => $doc['rejection_note'] ?? null,
        ]);
    }

    /**
     * index(): cargar periodo del usuario, query doc_types complementarios del periodo + docs existentes del usuario (LEFT JOIN)
     */
    public function index(): string
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');

        // Obtener inscripción y periodo del usuario
        $inscriptionModel = new \App\Models\InscriptionModel();
        $inscription = $inscriptionModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();

        $docTypes = [];
        $inscriptionStatus = null;

        if ($inscription) {
            $inscriptionStatus = $inscription['status'];

            // Query doc_types complementarios del periodo + docs existentes del usuario (LEFT JOIN)
            $docTypes = $db->table('period_document_types pdt')
                ->select('dt.id, dt.name, dt.description, dt.allowed_types, d.id as doc_id, d.status as doc_status, d.rejection_note, d.s3_key, d.original_name, d.file_extension')
                ->join('document_types dt', 'dt.id=pdt.doc_type_id')
                ->join('documents d', 'd.doc_type_id=dt.id AND d.user_id=' . $userId . ' AND d.period_id=' . $inscription['period_id'], 'left')
                ->where('pdt.period_id', $inscription['period_id'])
                ->where('dt.category', 'complementario')
                ->orderBy('pdt.sort_order')
                ->get()
                ->getResultArray();
        }

        return view('layouts/user', [
            'content' => view('user/documents', [
                'docTypes' => $docTypes,
                'inscriptionStatus' => $inscriptionStatus,
                'hasInscription' => (bool) $inscription,
            ])
        ]);
    }

    /**
     * upload(): validar archivo con FileValidator, si existe doc previo → S3Service::archive() + INSERT nuevo
     * S3 key: rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}
     * Retorna JSON {success, doc_id, status} para AJAX
     */
    public function upload()
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');

        // Obtener periodo del usuario
        $inscriptionModel = new \App\Models\InscriptionModel();
        $inscription = $inscriptionModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$inscription) {
            return $this->response->setJSON(['error' => 'No tienes una inscripción activa'])->setStatusCode(400);
        }

        $periodId = $inscription['period_id'];
        $docTypeId = $this->request->getPost('doc_type_id');

        if (!$docTypeId) {
            return $this->response->setJSON(['error' => 'Tipo de documento requerido'])->setStatusCode(400);
        }

        // Verificar que el doc_type es complementario y pertenece al periodo
        $docType = $db->table('document_types')
            ->where('id', $docTypeId)
            ->where('category', 'complementario')
            ->get()
            ->getRowArray();

        if (!$docType) {
            return $this->response->setJSON(['error' => 'Tipo de documento no válido'])->setStatusCode(400);
        }

        $isInPeriod = $db->table('period_document_types')
            ->where('period_id', $periodId)
            ->where('doc_type_id', $docTypeId)
            ->countAllResults() > 0;

        if (!$isInPeriod) {
            return $this->response->setJSON(['error' => 'Este documento no pertenece al periodo activo'])->setStatusCode(400);
        }

        // Obtener archivo subido
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'Archivo no válido'])->setStatusCode(400);
        }

        // Validar con FileValidator (json_decode allowed_types)
        $allowedTypes = json_decode($docType['allowed_types'], true) ?? [];
        $maxSizeMb = 10; // Default 10MB

        $validator = new \App\Libraries\FileValidator();
        $errors = $validator->validate($file, $allowedTypes, $maxSizeMb);

        if (!empty($errors)) {
            return $this->response->setJSON(['error' => implode(', ', $errors)])->setStatusCode(400);
        }

        // Capturar metadatos ANTES del move (después el path original ya no existe)
        $originalName = $file->getClientName();
        $fileSize     = $file->getSize();
        $mimeType     = $file->getMimeType();

        // Mover a directorio temporal (ruta RELATIVA a WRITEPATH)
        $tmpDir = WRITEPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $ext     = strtolower($file->getClientExtension());
        $tmpName = $file->getRandomName();
        $file->move($tmpDir, $tmpName);
        $tmpPath = $tmpDir . $tmpName;

        if (!$validator->checkMagicBytes($tmpPath, $ext)) {
            @unlink($tmpPath);
            return $this->response->setJSON(['error' => 'El archivo no parece ser del tipo declarado'])->setStatusCode(400);
        }

        // Generar S3 key: comisionfilm/rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}
        $uuid      = bin2hex(random_bytes(16));
        $awsPrefix = config('AWS')->prefix;  // "comisionfilm"
        $s3Key     = "{$awsPrefix}/rcf/{$periodId}/{$userId}/complementario/{$uuid}.{$ext}";

        // Subir a S3
        $s3Service = new \App\Libraries\S3Service();
        $uploaded  = $s3Service->upload($tmpPath, $s3Key, $mimeType);

        // Limpiar temp
        @unlink($tmpPath);

        if (!$uploaded) {
            return $this->response->setJSON(['error' => 'Error al subir el archivo'])->setStatusCode(500);
        }

        // Si existen docs previos del mismo tipo → archivar en S3 y borrar de BD
        $existingDocs = $this->documentModel
            ->where('user_id', $userId)
            ->where('doc_type_id', $docTypeId)
            ->where('period_id', $periodId)
            ->findAll();

        foreach ($existingDocs as $old) {
            $s3Service->archive($old['s3_key']);
            $this->documentModel->delete($old['id']);
        }

        // INSERT nuevo registro
        $this->documentModel->insert([
            'user_id' => $userId,
            'doc_type_id' => $docTypeId,
            'period_id' => $periodId,
            'original_name' => $originalName,
            'stored_name' => $uuid . '.' . $ext,
            's3_key' => $s3Key,
            's3_bucket' => config('AWS')->bucket,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'file_extension' => $ext,
            'status' => 'pending',
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);

        $docId = $this->documentModel->getInsertID();

        return $this->response->setJSON([
            'success' => true,
            'doc_id' => $docId,
            'status' => 'pending',
        ]);
    }

    /**
     * uploadInitial(): re-subida de documentos INICIALES (rechazados o no cargados)
     * Permite al usuario reemplazar un documento inicial desde el dashboard.
     * Sólo permite re-subir si el documento actual está en estado 'rejected' o no existe.
     * S3 key: rcf/{period_id}/{user_id}/inicial/{uuid}.{ext}
     * Retorna JSON {success, doc_id, status}
     */
    public function uploadInitial()
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');

        // Obtener inscripción y periodo del usuario
        $inscriptionModel = new \App\Models\InscriptionModel();
        $inscription = $inscriptionModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$inscription) {
            return $this->response->setJSON(['error' => 'No tienes una inscripción activa'])->setStatusCode(400);
        }

        $periodId  = $inscription['period_id'];
        $docTypeId = $this->request->getPost('doc_type_id');

        if (!$docTypeId) {
            return $this->response->setJSON(['error' => 'Tipo de documento requerido'])->setStatusCode(400);
        }

        // Verificar que el doc_type es INICIAL y pertenece al periodo
        $docType = $db->table('document_types')
            ->where('id', $docTypeId)
            ->where('category', 'inicial')
            ->get()
            ->getRowArray();

        if (!$docType) {
            return $this->response->setJSON(['error' => 'Tipo de documento no válido'])->setStatusCode(400);
        }

        $isInPeriod = $db->table('period_document_types')
            ->where('period_id', $periodId)
            ->where('doc_type_id', $docTypeId)
            ->countAllResults() > 0;

        if (!$isInPeriod) {
            return $this->response->setJSON(['error' => 'Este documento no pertenece al periodo activo'])->setStatusCode(400);
        }

        // Obtener todos los docs existentes para este tipo/usuario/periodo
        $existingDocs = $this->documentModel
            ->where('user_id', $userId)
            ->where('doc_type_id', $docTypeId)
            ->where('period_id', $periodId)
            ->orderBy('id', 'DESC')
            ->findAll();

        // Solo bloquear si el doc más reciente está aprobado
        // Permitir re-subida en estado rejected, pending o sin documento
        if (!empty($existingDocs) && $existingDocs[0]['status'] === 'approved') {
            return $this->response->setJSON(['error' => 'Este documento ya fue aprobado y no puede reemplazarse'])->setStatusCode(403);
        }

        // Validar archivo
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'Archivo no válido'])->setStatusCode(400);
        }

        $allowedTypes = json_decode($docType['allowed_types'], true) ?? [];
        $maxSizeMb    = $docType['max_size_mb'] ?? 10;

        $validator = new \App\Libraries\FileValidator();
        $errors    = $validator->validate($file, $allowedTypes, $maxSizeMb);

        if (!empty($errors)) {
            return $this->response->setJSON(['error' => implode(', ', $errors)])->setStatusCode(400);
        }

        // Capturar metadatos ANTES del move (después el path original ya no existe)
        $originalName = $file->getClientName();
        $fileSize     = $file->getSize();
        $mimeType     = $file->getMimeType();

        // Mover a directorio temporal
        $tmpDir = WRITEPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $ext     = strtolower($file->getClientExtension());
        $tmpName = $file->getRandomName();
        $file->move($tmpDir, $tmpName);
        $tmpPath = $tmpDir . $tmpName;

        if (!$validator->checkMagicBytes($tmpPath, $ext)) {
            @unlink($tmpPath);
            return $this->response->setJSON(['error' => 'El archivo no parece ser del tipo declarado'])->setStatusCode(400);
        }

        // Generar S3 key: comisionfilm/rcf/{period_id}/{user_id}/inicial/{uuid}.{ext}
        $uuid      = bin2hex(random_bytes(16));
        $awsPrefix = config('AWS')->prefix;  // "comisionfilm"
        $s3Key     = "{$awsPrefix}/rcf/{$periodId}/{$userId}/inicial/{$uuid}.{$ext}";

        // Subir a S3
        $s3Service = new \App\Libraries\S3Service();
        $uploaded  = $s3Service->upload($tmpPath, $s3Key, $mimeType);
        @unlink($tmpPath);

        if (!$uploaded) {
            return $this->response->setJSON(['error' => 'Error al subir el archivo'])->setStatusCode(500);
        }

        // Archivar en S3 y BORRAR todos los registros previos de BD
        foreach ($existingDocs as $old) {
            $s3Service->archive($old['s3_key']);
            $this->documentModel->delete($old['id']);
        }

        // INSERT nuevo registro con status 'pending'
        $this->documentModel->insert([
            'user_id'        => $userId,
            'doc_type_id'    => $docTypeId,
            'period_id'      => $periodId,
            'original_name'  => $originalName,
            'stored_name'    => $uuid . '.' . $ext,
            's3_key'         => $s3Key,
            's3_bucket'      => config('AWS')->bucket,
            'file_size'      => $fileSize,
            'mime_type'      => $mimeType,
            'file_extension' => $ext,
            'status'         => 'pending',
            'uploaded_at'    => date('Y-m-d H:i:s'),
        ]);

        $docId = $this->documentModel->getInsertID();

        return $this->response->setJSON([
            'success' => true,
            'doc_id'  => $docId,
            'status'  => 'pending',
        ]);
    }

    /**
     * submit(): contar doc_types complementarios asignados al periodo vs docs del usuario con period_id
     * Si igual: UPDATE inscriptions (status='under_review', submitted_at=NOW()), crear notificación al admin
     */
    public function submit()
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');

        // Obtener inscripción y periodo
        $inscriptionModel = new \App\Models\InscriptionModel();
        $inscription = $inscriptionModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$inscription) {
            return $this->response->setJSON(['error' => 'No tienes una inscripción activa'])->setStatusCode(400);
        }

        $periodId = $inscription['period_id'];

        // Contar doc_types complementarios asignados al periodo
        $assigned = $db->table('period_document_types pdt')
            ->join('document_types dt', 'dt.id=pdt.doc_type_id')
            ->where('pdt.period_id', $periodId)
            ->where('dt.category', 'complementario')
            ->countAllResults();

        // Contar docs del usuario con period_id (complementarios)
        $complementarioTypeIds = $db->table('period_document_types pdt')
            ->join('document_types dt', 'dt.id=pdt.doc_type_id')
            ->where('pdt.period_id', $periodId)
            ->where('dt.category', 'complementario')
            ->select('dt.id')
            ->get()
            ->getResultArray();

        $typeIds = array_column($complementarioTypeIds, 'id');

        $uploaded = 0;
        if (!empty($typeIds)) {
            $uploaded = $db->table('documents')
                ->where('user_id', $userId)
                ->where('period_id', $periodId)
                ->whereIn('doc_type_id', $typeIds)
                ->countAllResults();
        }

        // Verificar que todos los slots complementarios tienen al menos un doc
        if ($uploaded < $assigned) {
            $missing = $assigned - $uploaded;
            return $this->response->setJSON([
                'error' => "Faltan {$missing} documento(s) por cargar"
            ])->setStatusCode(400);
        }

        // UPDATE inscriptions SET status='under_review', submitted_at=NOW()
        $inscriptionModel->update($inscription['id'], [
            'status' => 'under_review',
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);

        // Crear notificación al admin
        if (function_exists('create_notification')) {
            $admins = $db->table('users')->where('role', 'admin')->get()->getResultArray();
            foreach ($admins as $admin) {
                create_notification(
                    $admin['id'],
                    'inscription',
                    'Documentos complementarios enviados',
                    "El usuario ha enviado todos sus documentos complementarios para revisión.",
                    true
                );
            }
        }

        // Verificar si es AJAX o request normal
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Documentos enviados para revisión',
            ]);
        }

        return redirect()->to(site_url('dashboard'))->with('success', 'Documentos enviados para revisión');
    }
}
