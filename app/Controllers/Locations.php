<?php

namespace App\Controllers;

use App\Libraries\Agoda;
use App\Models\LocationSourceModel;

class Locations extends BaseController
{
  protected $locationSourceModel;

  public function __construct()
  {
    $this->locationSourceModel = new LocationSourceModel();
  }

  public function list()
  {
    $body = $this->request->getJSON(true);
    $parent_slug = $body['parent'] ?? null;
    $parent_loc = $this->locationModel->where('slug', $parent_slug)
      ->first();
    $list = $this->locationModel->select('locations.slug, locations.en_name, location_translations.name as trans_name')
      ->join('location_translations', "location_translations.location_id = locations.id AND location_translations.language_id = {$this->language_id}", 'left')
      ->where('parent_id', $parent_loc['id'] ?? 0)
      ->findAll();
    return $this->response->setJSON($list);
  }

  public function items($slug)
  {
    $page = (int) $this->request->getGet('page');
    if ($page == 1) {
      $currentUrl = current_url();
      $query = $_GET;
      unset($query['page']);
      $redirectUrl = $currentUrl . (empty($query) ? '' : '?' . http_build_query($query));

      return redirect()->to($redirectUrl, 301);
    }

    $loc = $this->locationModel
      ->where('slug', $slug)
      ->first();
    if (!$loc) {
      return $this->responseError404();
      // throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    $loc['trans_name'] = $this->locationTranslationModel->getTransName($loc, $this->language);

    $locSources = $this->locationSourceModel->select('location_sources.*, sources.name as source_name')
      ->join('sources', 'sources.id = location_sources.source_id', 'left')
      ->where('location_id', $loc['id'])
      ->findAll();

    $detail = null;
    if (!empty($locSources)) {
      $a_loc = array_find($locSources, fn($ls) => $ls['source_name'] == 'Agoda');
      if (!empty($a_loc)) {
        $langId = $this->language['a_id'] ?? 1;
        $agoda = new Agoda();
        $detail = $agoda->getLocationDetail($a_loc['location_source_id'], empty($loc['parent_id']) ? 4 : 5, $langId)['data'] ?? null;
      }
    }

    $locs = [];
    if ($loc['parent_id'] != null) {
      array_push($locs, $this->locationModel->find($loc['parent_id']));

      $builder = $this->itemModel
        ->where('city_id', $loc['id']);
    } else {
      $builder = $this->itemModel
        ->where('country_id', $loc['id']);
    }

    $perPage = 12;
    $items = $builder->orderBy('created_at', 'DESC')
      ->paginate($perPage);
    $pager = $this->itemModel->pager;

    $breadcrumbs_data = $this->getLocBreadcrumbs($locs, $loc['trans_name'] ?? $loc['en_name']);
    $sidebar_data = $this->getSidebarData($locs[0] ?? $loc);
    $tags = [];
    foreach ($this->base_data['categories'] as $cat) {
      if (!empty($this->itemModel->getCount($loc, $cat))) {
        array_push($tags, [
          'link' => relate_items_link($cat, $loc),
          'name' => relate_items_title($cat, $loc),
        ]);
      }
    }

    $this->updateLocationMetaData($loc, $detail);

    $this->response->removeHeader('Cache-Control');
    $this->response->setHeader('Cache-Control', 'public, max-age=3600');

    return view('items/index', [
      'base_data' => $this->base_data,
      'detail' => $detail,
      'items' => $items,
      'breadcrumb_data' => $breadcrumbs_data,
      'sidebar_data' => $sidebar_data,
      'tags' => $tags,
      'pager' => $pager,
    ]);
  }

  private function updateLocationMetaData($loc, $detail = null)
  {
    $this->base_data['meta_title'] = meta_title($loc['trans_name'] ?? $loc['en_name']);
    $this->base_data['meta_description'] = !empty($detail['description']) ? mb_strimwidth(strip_tags($detail['description']), 0, 160, '...') : meta_description(null, $loc);

    $this->base_data['meta_keywords'] = array_merge([
      $loc['trans_name'] ?? $loc['en_name'],
    ], $this->base_data['meta_keywords']);
  }
}
