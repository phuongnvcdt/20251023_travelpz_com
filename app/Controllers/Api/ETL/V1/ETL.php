<?php

namespace App\Controllers\Api\ETL\V1;

use CodeIgniter\Controller;
use App\Libraries\EThangLong;

class ETL extends Controller
{
  public function login()
  {
    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    if (!$username || !$password) {
      return $this->response->setStatusCode(400)
        ->setJSON(['error' => 'Missing credentials']);
    }

    $etl = new EThangLong();
    return $this->response->setJSON($etl->login($username, $password));
  }
}
