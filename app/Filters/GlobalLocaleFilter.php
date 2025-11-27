<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\LanguageModel;

class GlobalLocaleFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    $defaultLocale = strtolower(config('App')->defaultLocale);
    $segments = service('request')->getUri()->getSegments();

    if (empty($segments[0]) || $segments[0] === 'admin') {
      return;
    }

    $firstSegment = strtolower($segments[0]);

    // Nếu là default locale, redirect về path không có locale
    if ($firstSegment === $defaultLocale) {
      array_shift($segments);
      return redirect()->to(base_url(implode('/', $segments) ?: '/'));
    }

    // Lấy danh sách locale hợp lệ từ cache/DB
    $cache = cache();
    $locales = $cache->get('supported_locales');
    if (!$locales) {
      $languageModel = new LanguageModel();
      $languages = $languageModel->getActiveList();
      $locales = array_map(fn($lang) => strtolower($lang['locale']), $languages);
      $cache->save('supported_locales', $locales, 3600);
    }

    // Nếu locale hợp lệ, set locale
    if (in_array($firstSegment, array_map('strtolower', $locales))) {
      service('request')->setLocale($firstSegment);
      config('App')->currentLocale = $firstSegment;
      return;
    }

    // Nếu có dạng xx-xx nhưng không hợp lệ, bỏ qua segment đầu
    if (preg_match('#^[a-z]{2}-[a-z]{2}$#i', $segments[0])) {
      array_shift($segments);
      return redirect()->to(base_url(implode('/', $segments) ?: '/'));
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
