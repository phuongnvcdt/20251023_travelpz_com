<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
	public function index()
	{
		return view('auth/login', [
			'base_data' => $this->base_data,
		]);
	}

	public function login()
	{
		helper(['form']);
		$session = session();
		$userModel = new UserModel();
		$username = esc($this->request->getPost('username'));
		$password = esc($this->request->getPost('password'));

		$user = $userModel->where('username', $username)->first();

		if ($user && password_verify($password, $user['password'])) {
			$session->set([
				'user_id' => $user['id'],
				'username' => $user['username'],
				'role' => $user['role'],
				'isLoggedIn' => true
			]);
			log_message('debug', 'User logged in: ' . $user['username']);

			// Điều hướng dựa trên role
			if ($user['role'] === 'admin') {
				return redirect()->to('/admin/dashboard');
			} else {
				return redirect()->to('/');
			}
		}

		$session->setFlashdata('error', 'Invalid username or password.');
		return redirect()->to('/login');
	}

	public function logout()
	{
		session()->destroy();
		return redirect()->to('/login');
	}
}
