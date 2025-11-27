<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LanguageModel;

class Languages extends BaseController
{
    protected $languageModel;

    public function __construct()
    {
        $this->languageModel = new LanguageModel();
    }

    public function index()
    {
        $data['languages'] = $this->languageModel->findAll();
        return view('admin/languages/index', $data);
    }

    public function create()
    {
        return view('admin/languages/create');
    }

    public function store()
    {
        $this->languageModel->save([
            'name' => esc($this->request->getPost('name')),
            'code' => esc($this->request->getPost('code')),
            'locale' => esc($this->request->getPost('locale')),
            'a_id' => esc($this->request->getPost('a_id')),
            'k_code' => esc($this->request->getPost('k_code')),
            'kd_code' => esc($this->request->getPost('kd_code')),
            'active' => esc($this->request->getPost('active')) == 'on' ? 1 : 0,
        ]);

        cache()->delete('supported_locales');
        return redirect()->to('/admin/languages');
    }

    public function edit($id)
    {
        $data['language'] = $this->languageModel->find($id);
        return view('admin/languages/edit', $data);
    }

    public function update($id)
    {
        $this->languageModel->update($id, [
            'name' => esc($this->request->getPost('name')),
            'code' => esc($this->request->getPost('code')),
            'locale' => esc($this->request->getPost('locale')),
            'a_id' => esc($this->request->getPost('a_id')),
            'k_code' => esc($this->request->getPost('k_code')),
            'kd_code' => esc($this->request->getPost('kd_code')),
            'active' => esc($this->request->getPost('active')) == 'on' ? 1 : 0,
        ]);

        cache()->delete('supported_locales');
        return redirect()->to('/admin/languages');
    }

    public function delete($id)
    {
        $this->languageModel->delete($id);
        return redirect()->to('/admin/languages');
    }
}
