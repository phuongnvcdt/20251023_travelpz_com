<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
	protected $userModel;

	public function __construct()
	{
		$this->userModel = new UserModel();
	}

	public function index()
	{
		$data['users'] = $this->userModel->findAll();
		return view('admin/users/index', $data);
	}

	public function create()
	{
		return view('admin/users/create');
	}

	public function store()
	{
		$this->userModel->save([
			'username' => esc($this->request->getPost('username')),
			'password' => esc($this->request->getPost('password')),
			'role'     => esc($this->request->getPost('role'))
		]);

		return redirect()->to('/admin/users');
	}

	public function edit($id)
	{
		$data['user'] = $this->userModel->find($id);
		return view('admin/users/edit', $data);
	}

	public function update($id)
	{
		$updateData = [
			'username' => esc($this->request->getPost('username')),
			'role'     => esc($this->request->getPost('role'))
		];

		$password = esc($this->request->getPost('password'));
		if (!empty($password)) {
			$updateData['password'] = $password;
		}

		$this->userModel->update($id, $updateData);

		return redirect()->to('/admin/users');
	}

	public function delete($id)
	{
		$this->userModel->delete($id);
		return redirect()->to('/admin/users');
	}
}
