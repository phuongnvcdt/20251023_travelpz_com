<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ApiAuth implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		$fixedToken = env('API_TOKEN');

		$token = $request->getHeaderLine('Authorization');
		$token = str_replace('Bearer ', '', $token);

		if ($token !== $fixedToken) {
			return service('response')->setStatusCode(401)
				->setJSON(['error' => 'Invalid or missing token']);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		
	}
}
