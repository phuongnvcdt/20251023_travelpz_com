<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel
            ->select('categories.*, parent.en_name as parent_name')
            ->join('categories parent', 'parent.id = categories.parent_id', 'left')
            ->findAll();

        return view('admin/categories/index', ['categories' => $categories]);
    }

    public function create()
    {
        $data['parents'] = $this->categoryModel->findAll();
        return view('admin/categories/create', $data);
    }

    public function store()
    {
        $this->categoryModel->save([
            'en_name'   => esc($this->request->getPost('en_name')),
            'parent_id' => esc($this->request->getPost('parent_id')) ?: null
        ]);

        return redirect()->to('/admin/categories');
    }

    public function edit($id)
    {
        $data['category'] = $this->categoryModel->find($id);
        $data['parents'] = $this->categoryModel->where('id !=', $id)->findAll();
        return view('admin/categories/edit', $data);
    }

    public function update($id)
    {
        $this->categoryModel->update($id, [
            'en_name'   => esc($this->request->getPost('en_name')),
            'parent_id' => esc($this->request->getPost('parent_id')) ?: null
        ]);

        return redirect()->to('/admin/categories');
    }

    public function delete($id)
    {
        $this->categoryModel->delete($id);
        return redirect()->to('/admin/categories');
    }
}
