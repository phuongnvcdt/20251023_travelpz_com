<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CategoryTranslations extends BaseController
{
    protected $categoryTranslationModel;
    protected $categoryModel;
    protected $languageModel;

    public function __construct()
    {
        $this->categoryTranslationModel = new \App\Models\CategoryTranslationModel();
        $this->categoryModel = new \App\Models\CategoryModel();
        $this->languageModel = new \App\Models\LanguageModel();
    }

    public function index($categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $category_trans = $this->categoryTranslationModel
            ->select('category_translations.*, languages.name as language_name')
            ->join('languages', 'languages.id = category_translations.language_id')
            ->where('category_translations.category_id', $categoryId)
            ->findAll();

        return view('admin/category_translations/index', [
            'category' => $category,
            'category_trans' => $category_trans
        ]);
    }

    public function create($categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        $languages = $this->languageModel
            ->whereNotIn('id', function($builder) use ($categoryId) {
                $builder->select('language_id')
                        ->from('category_translations')
                        ->where('category_id', $categoryId);
            })
            ->findAll();

        return view('admin/category_translations/create', [
            'category' => $category,
            'languages' => $languages
        ]);
    }

    public function store($categoryId)
    {
        $this->categoryTranslationModel->save([
            'category_id'   => $categoryId,
            'language_id'   => esc($this->request->getPost('language_id')) ?: null,
            'name'          => esc($this->request->getPost('name')) ?: null
        ]);

        return redirect()->to("/admin/categories/$categoryId/trans")->with('success', 'Translation added');
    }

    public function edit($categoryId, $id)
    {
        $record = $this->categoryTranslationModel->find($id);
        $languages = $this->languageModel->findAll();
        $category = $this->categoryModel->find($categoryId);

        return view('admin/category_translations/edit', [
            'category' => $category,
            'record' => $record,
            'languages' => $languages
        ]);
    }

    public function update($categoryId, $id)
    {
        $this->categoryTranslationModel->update($id, [
            'name' => esc($this->request->getPost('name')) ?: null
        ]);

        return redirect()->to("/admin/categories/$categoryId/trans")->with('success', 'Translation updated');
    }

    public function delete($categoryId, $id)
    {
        $this->categoryTranslationModel->delete($id);
        return redirect()->to("/admin/categories/$categoryId/trans")->with('success', 'Translation deleted');
    }
}
