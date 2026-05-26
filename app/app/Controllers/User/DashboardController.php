<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session('user_id');
        
        // Cargar usuario de DB
        $user = $db->table('users')
            ->where('id', $userId)
            ->get()
            ->getRowArray();
        
        if (!$user) {
            return redirect()->to(site_url('logout'))->with('error', 'Usuario no encontrado');
        }
        
        // Inscripción más reciente
        $inscriptionModel = new \App\Models\InscriptionModel();
        $inscription = $inscriptionModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();
        
        $data = [
            'user' => $user,
            'noInscription' => false,
            'initialDocTypes' => [],
            'timelineStep' => 1,
        ];
        
        if (!$inscription) {
            // No hay inscripción
            $data['noInscription'] = true;
            
            // Verificar si hay periodo activo
            $periodModel = new \App\Models\PeriodModel();
            $activePeriod = $periodModel
                ->where('active', 1)
                ->first();
            $data['hasActivePeriod'] = (bool) $activePeriod;
        } else {
            // Hay inscripción - obtener periodo
            $periodModel = new \App\Models\PeriodModel();
            $period = $periodModel->find($inscription['period_id']);
            $data['inscription'] = $inscription;
            $data['period'] = $period;
            
            // Determinar paso del timeline según status
            $data['timelineStep'] = $this->getTimelineStep($inscription, $user);
            
            // Si hay periodo: query JOIN para doc_types iniciales
            if ($period) {
                $initialDocTypes = $db->table('period_document_types pdt')
                    ->select('dt.id, dt.name, dt.description, dt.allowed_types, d.status, d.id as doc_id, d.original_name, d.file_extension, d.rejection_note')
                    ->join('document_types dt', 'dt.id=pdt.doc_type_id')
                    ->join('documents d', 'd.doc_type_id=dt.id AND d.user_id=' . $userId . ' AND d.period_id=' . $period['id'], 'left')
                    ->where('pdt.period_id', $period['id'])
                    ->where('dt.category', 'inicial')
                    ->orderBy('pdt.sort_order')
                    ->get()
                    ->getResultArray();
                
                $data['initialDocTypes'] = $initialDocTypes;
            }
        }
        
        return view('layouts/user', [
            'content' => view('user/dashboard', $data)
        ]);
    }
    
    /**
     * Determinar el paso activo del timeline según estado de inscripción
     */
    private function getTimelineStep(array $inscription, array $user): int
    {
        // Paso 1: Registro completado (siempre si hay inscripción)
        // Paso 2: Correo verificado
        // Paso 3: Documentos enviados
        // Paso 4: En revisión
        // Paso 5: Aprobado
        
        if ($inscription['status'] === 'approved') {
            return 5;
        }
        
        if ($inscription['status'] === 'under_review') {
            return 4;
        }
        
        if ($inscription['status'] === 'active' || $inscription['status'] === 'submitted') {
            return 3;
        }
        
        if ($user['email_verified']) {
            return 2;
        }
        
        return 1;
    }
    
    public function notifCount()
    {
        $db = \Config\Database::connect();
        $count = $db->table('notifications')
            ->where('user_id', session('user_id'))
            ->where('read_at', null)
            ->countAllResults();
        
        return $this->response->setJSON(['count' => $count]);
    }
}
