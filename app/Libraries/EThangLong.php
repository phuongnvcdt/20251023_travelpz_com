<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Exceptions\HTTPException;

class EThangLong
{
  protected string $baseUrl = 'https://e-thanglong.vn';

  /**
   * LOGIN 2 BƯỚC
   */
  public function login(string $username, string $password): array
  {
    $step1 = $this->fetchLoginPage();

    /**
     * STEP 2: POST /login
     */
    $ch = curl_init();

    curl_setopt_array($ch, [
      CURLOPT_URL => $this->baseUrl . '/login',
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,            // ⭐ bắt header
      CURLOPT_FOLLOWLOCATION => false,   // ⭐ KHÔNG FOLLOW REDIRECT
      CURLOPT_POSTFIELDS => http_build_query([
        'Username' => $username,
        'Password' => $password,
        '__RequestVerificationToken' => $step1['token'],
      ]),
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: ' . $this->userAgent(),
        'Cookie: ' . implode('; ', $step1['cookies']),
      ],
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
      throw new HTTPException('cURL error: ' . curl_error($ch));
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headersRaw = substr($response, 0, $headerSize);

    curl_close($ch);

    $cookies = $this->extractCookiesFromRawHeader($headersRaw);

    if (empty($cookies)) {
      return ['success' => false];
    }

    return [
      'success' => true,
      'status'  => $statusCode,
      'cookies' => $this->mergeCookies($step1['cookies'], $cookies),
    ];
  }

  /**
   * STEP 1: GET /login
   */
  protected function fetchLoginPage(): array
  {
    $ch = curl_init();

    curl_setopt_array($ch, [
      CURLOPT_URL => $this->baseUrl . '/login',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,
      CURLOPT_FOLLOWLOCATION => false,
      CURLOPT_HTTPHEADER => [
        'User-Agent: ' . $this->userAgent(),
      ],
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
      throw new HTTPException('cURL error: ' . curl_error($ch));
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headersRaw = substr($response, 0, $headerSize);
    $html = substr($response, $headerSize);

    curl_close($ch);

    // 🔍 extract token
    preg_match(
      '/name="__RequestVerificationToken".*?value="([^"]+)"/',
      $html,
      $matches
    );

    $token = $matches[1] ?? null;

    if (!$token) {
      throw new HTTPException('RequestVerificationToken not found');
    }

    // 🔍 extract cookies
    $cookies = $this->extractCookiesFromRawHeader($headersRaw);

    return [
      'token'   => $token,
      'cookies' => $cookies,
    ];
  }

  /**
   * Lấy cookie từ RAW HEADER (cURL)
   */
  protected function extractCookiesFromRawHeader(string $rawHeader): array
  {
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $rawHeader, $matches);
    return $matches[1] ?? [];
  }

  /**
   * Merge cookie theo key (cookie mới ghi đè cookie cũ)
   */
  protected function mergeCookies(array $cookies1, array $cookies2): array
  {
    $result = [];

    foreach (array_merge($cookies1, $cookies2) as $cookie) {
      [$name, $value] = explode('=', $cookie, 2);
      $result[$name] = $value;
    }

    return array_map(
      fn($k, $v) => "{$k}={$v}",
      array_keys($result),
      $result
    );
  }

  protected function userAgent(): string
  {
    return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/141.0.0.0 Safari/537.36 ' . rand(1000, 9999);
  }
}
