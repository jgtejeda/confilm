<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PeriodModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PeriodController extends BaseController
{
    protected $periodModel;

    public function __construct()
    {
        $this->periodModel = new PeriodModel();
    }

    public function index(): string
    {
        $periods = $this->periodModel->orderBy('created_at', 'DESC')->findAll();

        foreach ($periods as &$period) {
            $period['status_badge'] = $this->getPeriodStatus($period);
        }

        return view('layouts/admin', [
            'content' => view('admin/periods/index', [
                'periods' => $periods,
            ])
        ]);
    }

    public function create(): string
    {
        $db = \Config\Database::connect();
        $docTypes = $db->table('document_types')
            ->where('active', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        return view('layouts/admin', [
            'content' => view('admin/periods/form', [
                'period' => null,
                'docTypes' => $docTypes,
                'assignedDocIds' => [],
            ])
        ]);
    }

    public function store()
    {
        $request = service('request');
        $docTypeIds = $request->getPost('doc_types');

        $validationRules = [
            'name' => 'required|max_length[200]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');

        if (strtotime($startDate) >= strtotime($endDate)) {
            return redirect()->back()->withInput()->with('error', 'La fecha de inicio debe ser menor a la fecha de fin');
        }

        $db = \Config\Database::connect();

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description') ?: null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active' => $request->getPost('active') ? 1 : 0,
            'created_by' => session('user_id'),
        ];

        $db->transStart();
        $this->periodModel->insert($data);
        $periodId = $this->periodModel->getInsertID();

        if (!empty($docTypeIds) && is_array($docTypeIds)) {
            $idx = 0;
            foreach ($docTypeIds as $dtId) {
                $db->table('period_document_types')->insert([
                    'period_id' => $periodId,
                    'doc_type_id' => (int)$dtId,
                    'sort_order' => $idx,
                ]);
                $idx++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Error al crear el periodo');
        }

        return redirect()->to(site_url('admin/periodos'))->with('success', 'Periodo creado exitosamente');
    }

    public function edit($id): string
    {
        $period = $this->periodModel->find($id);

        if (!$period) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();
        $docTypes = $db->table('document_types')
            ->where('active', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $assignedDocs = $db->table('period_document_types')
            ->where('period_id', $id)
            ->get()
            ->getResultArray();

        $assignedDocIds = array_map(fn($row) => $row['doc_type_id'], $assignedDocs);

        return view('layouts/admin', [
            'content' => view('admin/periods/form', [
                'period' => $period,
                'docTypes' => $docTypes,
                'assignedDocIds' => $assignedDocIds,
            ])
        ]);
    }

    public function update($id)
    {
        $request = service('request');
        $docTypeIds = $request->getPost('doc_types');

        $existing = $this->periodModel->find($id);
        if (!$existing) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $validationRules = [
            'name' => 'required|max_length[200]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');

        if (strtotime($startDate) >= strtotime($endDate)) {
            return redirect()->back()->withInput()->with('error', 'La fecha de inicio debe ser menor a la fecha de fin');
        }

        $db = \Config\Database::connect();

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description') ?: null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active' => $request->getPost('active') ? 1 : 0,
        ];

        $db->transStart();
        $this->periodModel->update($id, $data);

        $db->table('period_document_types')->where('period_id', $id)->delete();

        if (!empty($docTypeIds) && is_array($docTypeIds)) {
            $idx = 0;
            foreach ($docTypeIds as $dtId) {
                $db->table('period_document_types')->insert([
                    'period_id' => $id,
                    'doc_type_id' => (int)$dtId,
                    'sort_order' => $idx,
                ]);
                $idx++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el periodo');
        }

        return redirect()->to(site_url('admin/periodos'))->with('success', 'Periodo actualizado exitosamente');
    }

    public function toggle($id)
    {
        $period = $this->periodModel->find($id);

        if (!$period) {
            return $this->response->setJSON(['success' => false, 'error' => 'Periodo no encontrado']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('periods')->where('id !=', $id)->update(['active' => 0]);
        $db->table('periods')->where('id', $id)->update(['active' => 1]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al activar el periodo']);
        }

        return $this->response->setJSON(['success' => true, 'active' => 1]);
    }

    protected function getPeriodStatus(array $period): array
    {
        $now = time();
        $start = strtotime($period['start_date']);
        $end = strtotime($period['end_date']);
        $active = (bool)$period['active'];

        if (!$active) {
            return ['label' => 'Inactivo', 'class' => 'badge-inactive'];
        }

        if ($end < $now) {
            return ['label' => 'Expirado', 'class' => 'badge-expired'];
        }

        if ($start > $now) {
            return ['label' => 'Futuro', 'class' => 'badge-future'];
        }

        return ['label' => 'Activo', 'class' => 'badge-active'];
    }
}
