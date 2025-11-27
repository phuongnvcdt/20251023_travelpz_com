<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;

class BaseAPI
{
  protected $client;

  public function __construct()
  {
    $this->client = Services::curlrequest([
      'timeout' => 30,
      'http_errors' => false,
      'headers'      => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 1.' . rand(1000, 9999)
      ]
    ]);
  }

  protected function handleResponse($response)
  {
    if (!isset($response)) {
      throw new HTTPException('API Error - no response');
    }

    $status = $response->getStatusCode();
    $body   = $response->getBody();

    if ($status >= 400) {
      throw new HTTPException("API Error ({$status}): {$body}", $status);
    }

    return json_decode($body, true);
  }

  protected function defaultHeaders(): array
  {
    return [
      'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 1.' . rand(1000, 9999),
      'Accept-Encoding' => 'gzip, deflate',
    ];
  }
}
