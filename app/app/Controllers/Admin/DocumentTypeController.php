<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentTypeModel;

class DocumentTypeController extends BaseController
{
    protected $docTypeModel;

    public function __construct()
    {
        $this->docTypeModel = new DocumentTypeModel();
    }

    public function index(): string
    {
        $docTypes = $this->docTypeModel->orderBy('sort_order', 'ASC')->paginate(20);
        $pager = $this->docTypeModel->pager;

        return view('layouts/admin', [
            'content' => view('admin/document_types/index', [
                'docTypes' => $docTypes,
                'pager' => $pager,
            ])
        ]);
    }

    public function create(): string
    {
        return view('layouts/admin', [
            'content' => view('admin/document_types/form', [
                'docType' => null,
            ])
        ]);
    }

    public function store()
    {
        $request = service('request');
        $allowedTypes = $request->getPost('allowed_types');

        $validationRules = [
            'name' => 'required|max_length[255]',
            'category' => 'required|in_list[inicial,complementario]',
            'max_size_mb' => 'permit_empty|integer|greater_than[0]',
            'max_months' => 'permit_empty|integer|greater_than[0]',
            'sort_order' => 'permit_empty|integer',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (empty($allowedTypes) || !is_array($allowedTypes) || count($allowedTypes) < 1) {
            return redirect()->back()->withInput()->with('error', 'Selecciona al menos un tipo de archivo');
        }

        $validTypes = ['pdf', 'docx', 'xlsx', 'pptx', 'jpg', 'png'];
        $allowedTypes = array_intersect($allowedTypes, $validTypes);

        if (empty($allowedTypes)) {
            return redirect()->back()->withInput()->with('error', 'Selecciona al menos un tipo de archivo válido');
        }

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description') ?? null,
            'category' => $request->getPost('category'),
            'required' => $request->getPost('required') ? 1 : 0,
            'allowed_types' => json_encode($allowedTypes),
            'max_size_mb' => $request->getPost('max_size_mb') ?: 5,
            'max_months' => $request->getPost('max_months') ?: null,
            'sort_order' => $request->getPost('sort_order') ?: 0,
            'active' => 1,
            'created_by' => session('user_id'),
        ];

        $this->docTypeModel->insert($data);

        return redirect()->to(site_url('admin/tipos-documento'))->with('success', 'Tipo de documento creado');
    }

    public function edit($id): string
    {
        $docType = $this->docTypeModel->find($id);

        if (!$docType) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $docType['allowed_types'] = json_decode($docType['allowed_types'], true) ?? [];

        return view('layouts/admin', [
            'content' => view('admin/document_types/form', [
                'docType' => $docType,
            ])
        ]);
    }

    public function update($id)
    {
        $request = service('request');
        $allowedTypes = $request->getPost('allowed_types');

        $existing = $this->docTypeModel->find($id);
        if (!$existing) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $validationRules = [
            'name' => 'required|max_length[255]',
            'category' => 'required|in_list[inicial,complementario]',
            'max_size_mb' => 'permit_empty|integer|greater_than[0]',
            'max_months' => 'permit_empty|integer|greater_than[0]',
            'sort_order' => 'permit_empty|integer',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (empty($allowedTypes) || !is_array($allowedTypes) || count($allowedTypes) < 1) {
            return redirect()->back()->withInput()->with('error', 'Selecciona al menos un tipo de archivo');
        }

        $validTypes = ['pdf', 'docx', 'xlsx', 'pptx', 'jpg', 'png'];
        $allowedTypes = array_intersect($allowedTypes, $validTypes);

        if (empty($allowedTypes)) {
            return redirect()->back()->withInput()->with('error', 'Selecciona al menos un tipo de archivo válido');
        }

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description') ?? null,
            'category' => $request->getPost('category'),
            'required' => $request->getPost('required') ? 1 : 0,
            'allowed_types' => json_encode($allowedTypes),
            'max_size_mb' => $request->getPost('max_size_mb') ?: 5,
            'max_months' => $request->getPost('max_months') ?: null,
            'sort_order' => $request->getPost('sort_order') ?: 0,
        ];

        $this->docTypeModel->update($id, $data);

        return redirect()->to(site_url('admin/tipos-documento'))->with('success', 'Tipo de documento actualizado');
    }

    public function toggle($id)
    {
        $docType = $this->docTypeModel->find($id);

        if (!$docType) {
            return $this->response->setJSON(['success' => false, 'error' => 'Tipo de documento no encontrado']);
        }

        $newVal = $docType['active'] ? 0 : 1;
        $this->docTypeModel->update($id, ['active' => $newVal]);

        return $this->response->setJSON(['success' => true, 'active' => $newVal]);
    }

    public function reorder()
    {
        $request = service('request');
        $items = json_decode($request->getBody(), true);

        if (!is_array($items)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Datos inválidos']);
        }

        foreach ($items as $item) {
            if (isset($item['id']) && isset($item['sort_order'])) {
                $this->docTypeModel->update($item['id'], ['sort_order' => $item['sort_order']]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }
}
