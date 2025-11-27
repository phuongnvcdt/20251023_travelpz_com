<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LocaleFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    $segments = service('request')->getUri()->getSegments();
    $firstSegment = strtolower($segments[0] ?? '');
    if (!preg_match('#^[a-z]{2}-[a-z]{2}$#i', $firstSegment)) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Không cần xử lý sau
  }
}
