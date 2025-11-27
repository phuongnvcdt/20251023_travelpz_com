<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LocationModel;
use App\Models\SourceModel;

class Locations extends BaseController
{
    protected $locationModel;
    protected $sourceModel;

    public function __construct()
    {
        $this->locationModel = new LocationModel();
        $this->sourceModel = new SourceModel();
    }

    public function index()
    {
        $locations = $this->locationModel
            ->select('locations.*, parent.en_name as parent_name')
            ->join('locations parent', 'parent.id = locations.parent_id', 'left')
            ->findAll();

        return view('admin/locations/index', ['locations' => $locations]);
    }

    public function create()
    {
        $data['sources'] = $this->sourceModel->findAll();
        $data['parents'] = $this->locationModel->findAll(); // cho chọn parent
        return view('admin/locations/create', $data);
    }

    public function store()
    {
        $this->locationModel->save([
            'en_name'            => esc($this->request->getPost('en_name')),
            'parent_id'          => esc($this->request->getPost('parent_id')) ?: null
        ]);

        return redirect()->to('/admin/locations');
    }

    public function edit($id)
    {
        $data['location'] = $this->locationModel->find($id);
        $data['parents'] = $this->locationModel->where('id !=', $id)->findAll(); // loại chính nó ra
        return view('admin/locations/edit', $data);
    }

    public function update($id)
    {
        $this->locationModel->update($id, [
            'en_name'            => esc($this->request->getPost('en_name')),
            'parent_id'          => esc($this->request->getPost('parent_id')) ?: null
        ]);

        return redirect()->to('/admin/locations');
    }

    public function delete($id)
    {
        $this->locationModel->delete($id);
        return redirect()->to('/admin/locations');
    }
}
