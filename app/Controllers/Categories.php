<?php

namespace App\Controllers;

class Categories extends BaseController
{
  public function list()
  {
    $body = $this->request->getJSON(true);
    $parent_slug = $body['parent'] ?? null;
    $parent_cat = $this->categoryModel->where('slug', $parent_slug)
      ->first();
    $list = $this->categoryModel->select('categories.slug, categories.en_name, category_translations.name as trans_name')
      ->join('category_translations', "category_translations.category_id = categories.id AND category_translations.language_id = {$this->language_id}", 'left')
      ->where('parent_id', $parent_cat['id'] ?? 0)
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

    $cat = $this->categoryModel
      ->where('slug', $slug)
      ->first();
    if (!$cat) {
      return $this->responseError404();
      // throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    $cat['trans_name'] = $this->categoryTranslationModel->getTransName($cat, $this->language);

    $locSlug = esc($this->request->getGet('loc'));
    $loc = $this->locationModel
      ->where('slug', $locSlug)
      ->first();
    if ($loc) {
      $loc['trans_name'] = $this->locationTranslationModel->getTransName($loc, $this->language);
    }

    $builder = $this->itemModel->select('items.*');
    if ($loc) {
      if ($loc['parent_id'] == null) {
        $country = $loc;
        $builder->where('country_id', $loc['id']);
      } else {
        $country = $this->locationModel->find($loc['parent_id']);
        $builder->where('city_id', $loc['id']);
      }
    } else if (!empty($locSlug)) {
      $currentUrl = current_url();
      $query = $_GET;
      unset($query['loc']);
      $redirectUrl = $currentUrl . (empty($query) ? '' : '?' . http_build_query($query));

      return redirect()->to($redirectUrl, 301);
    }

    if ($cat['parent_id'] == null) {
      $builder->where('category_id', $cat['id']);
    } else {
      $builder->join('item_sub_categories isc', 'isc.item_id = items.id', 'inner')
        ->where('isc.sub_category_id', $cat['id']);
    }

    $sub_categories = $this->categoryModel->where('parent_id', $cat['parent_id'] ?? $cat['id'])
      ->select('categories.*, category_translations.name as trans_name')
      ->join('category_translations', 'category_translations.category_id = categories.id AND category_translations.language_id = ' . (int)$this->language_id, 'left')
      ->findAll();

    $tags = array_map(fn($sc) => [
      'link' => category_link($sc),
      'name' => $sc['trans_name'] ?? $sc['en_name'],
    ], $sub_categories);

    $perPage = 12;
    $items = $builder->orderBy('created_at', 'DESC')
      ->paginate($perPage);
    $pager = $this->itemModel->pager;

    $this->base_data['category'] = $cat;

    $breadcrumbs_data = $this->getCatBreadcrumbs($cat, $loc, $loc ? relate_items_title($cat, $loc) : $cat['trans_name'] ?? $cat['en_name']);
    $sidebar_data = $this->getSidebarData($country ?? null);
    $this->updateCategoryMetaData($cat, $loc);

    $this->response->removeHeader('Cache-Control');
    $this->response->setHeader('Cache-Control', 'public, max-age=3600');

    return view('items/index', [
      'base_data' => $this->base_data,
      'items' => $items,
      'tags' => $tags,
      'breadcrumb_data' => $breadcrumbs_data,
      'sidebar_data' => $sidebar_data,
      'pager' => $pager,
    ]);
  }

  private function updateCategoryMetaData($cat, $loc)
  {
    $this->base_data['meta_title'] = meta_title(relate_items_title($cat, $loc));
    $this->base_data['meta_description'] = meta_description($cat, $loc);

    $this->base_data['meta_keywords'] = array_merge([
      $cat['trans_name'] ?? $cat['en_name'] ?? '',
      $loc['trans_name'] ?? $loc['en_name'] ?? '',
    ], $this->base_data['meta_keywords']);

    if (!empty($sub_cats_names)) {
      $this->base_data['meta_keywords'] = array_merge($sub_cats_names, $this->base_data['meta_keywords']);
    }
  }
}
