<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SourceModel;
use App\Libraries\Carla;

class Home extends BaseController
{
  protected $sourceModel;

  public function __construct()
  {
    $this->sourceModel = new SourceModel();
  }

  public function index()
  {
    $page = (int) $this->request->getGet('page');
    if ($page == 1) {
      $currentUrl = current_url();
      $query = $_GET;
      unset($query['page']);
      $redirectUrl = $currentUrl . (empty($query) ? '' : '?' . http_build_query($query));

      return redirect()->to($redirectUrl, 301);
    }

    $sources = $this->sourceModel->findAll();
    $srcLocales = [];
    foreach ($sources as $src) {
      $srcLocales[(string)$src['id']] = array_map(fn($lang) => strtolower($lang['locale']), sourceLanguages($this->base_data['languages'], $src));
    }

    $perPage = 12;
    $items = $this->itemModel->orderBy('created_at', 'DESC')
      ->paginate($perPage);
    foreach ($items as $i => $item) {
      $items[$i]['locales'] = $srcLocales[(string)$item['source_id']] ?? null;
    }
    $pager = $this->itemModel->pager;

    $sidebar_data = $this->getSidebarData(null);

    $this->response->removeHeader('Cache-Control');
    $this->response->setHeader('Cache-Control', 'public, max-age=3600');

    return view('home', [
      'base_data' => $this->base_data,
      'items' => $items,
      'sidebar_data' => $sidebar_data,
      'pager' => $pager,
    ]);
  }

  public function rentCar($sourceSlug)
  {
    $source = $this->sourceModel
      ->where('slug', $sourceSlug)
      ->first();

    if (!$source) {
      return redirect()->to(locale_url());
    }

    switch ($source['name']) {
      case 'Carla':
        return redirect()->to(Carla::rentCarLink());

      default:
        return redirect()->to(locale_url());
    }
  }

  public function error404()
  {
    $this->initController(service('request'), service('response'), service('logger'));
    return $this->viewError404();
  }

  public function any()
  {
    return $this->responseError404();
  }
}
