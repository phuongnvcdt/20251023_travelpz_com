<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SourceModel;

class Sources extends BaseController
{
	protected $sourceModel;

	public function __construct()
	{
		$this->sourceModel = new SourceModel();
	}

	public function index()
	{
		$data['sources'] = $this->sourceModel->findAll();
		return view('admin/sources/index', $data);
	}

	public function create()
	{
		return view('admin/sources/create');
	}

	public function store()
	{
		$this->sourceModel->save([
			'name' => esc($this->request->getPost('name')),
			'slug' => esc($this->request->getPost('slug')),
		]);

		return redirect()->to('/admin/sources');
	}

	public function edit($id)
	{
		$data['source'] = $this->sourceModel->find($id);
		return view('admin/sources/edit', $data);
	}

	public function update($id)
	{
		$this->sourceModel->update($id, [
			'name' => esc($this->request->getPost('name')),
			'slug' => esc($this->request->getPost('slug')),
		]);

		return redirect()->to('/admin/sources');
	}

	public function delete($id)
	{
		$this->sourceModel->delete($id);
		return redirect()->to('/admin/sources');
	}
}
