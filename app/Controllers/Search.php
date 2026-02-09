<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Search extends BaseController
{
  public function search()
  {
    $page = (int) $this->request->getGet('page');
    $keyword = trim(esc($this->request->getGet('q')));
    $catSlug = esc($this->request->getGet('cat'));
    $locSlug = esc($this->request->getGet('loc'));
    $srcSlug = esc($this->request->getGet('s'));

    $currentUrl = current_url();
    $orgQuery = $_GET;
    $query = $_GET;
    if ($page == 1) {
      unset($query['page']);
    }

    if (empty($keyword)) {
      unset($query['q']);
    }

    if (empty($catSlug)) {
      unset($query['cat']);
    }

    if (empty($locSlug)) {
      unset($query['loc']);
    }

    if ($orgQuery != $query) {
      $redirectUrl = $currentUrl . (empty($query) ? '' : '?' . http_build_query($query));

      return redirect()->to($redirectUrl, 301);
    }

    $builder = $this->itemModel;
    if (!empty($srcSlug)) {
      $sourceModel = new \App\Models\SourceModel();
      $source = $sourceModel->where('slug', $srcSlug)
        ->first();

      if ($source) {
        $builder->where('items.source_id', $source['id']);
      }
    }

    if (!empty($locSlug)) {
      $loc = $this->locationModel
        ->where('slug', $locSlug)
        ->first();

      if ($loc) {
        if ($loc['parent_id'] == null) {
          $builder->where('items.country_id', $loc['id']);
          $currentCountry = $loc['slug'];
        } else {
          $builder->where('items.city_id', $loc['id']);
          $currentCountry = $this->locationModel->find($loc['parent_id'])['slug'] ?? null;
          $currentCity = $loc['slug'];
        }
      }
    }

    if (!empty($catSlug)) {
      $cat = $this->categoryModel
        ->where('slug', $catSlug)
        ->first();

      if ($cat) {
        if ($cat['parent_id'] == null) {
          $builder->groupStart()
            ->where('items.category_id', $cat['id'])
            ->groupEnd();
          $currentCat = $cat['slug'];
        } else {
          $builder->groupStart()
            ->join('item_sub_categories', 'item_sub_categories.item_id = items.id', 'left')
            ->where('item_sub_categories.sub_category_id', $cat['id'])
            ->groupEnd();
          $currentCat = $this->categoryModel->find($cat['parent_id'])['slug'] ?? null;
          $currentSubCat = $cat['slug'];
        }
      }
    }

    if (!empty($keyword)) {
      $builder->groupStart()
        ->like('items.en_name', $keyword)
        ->orLike('items.en_description', $keyword)
        ->groupEnd();
    }

    $builder->select('items.*');
    $builder->groupBy('items.id');
    $perPage = 12;
    $items = $builder->orderBy('created_at', 'DESC')
      ->paginate($perPage);
    $pager = $this->itemModel->pager;

    $categories = $this->categoryModel->select('categories.*, category_translations.name as trans_name')
      ->join('category_translations', "category_translations.category_id = categories.id AND category_translations.language_id = {$this->language_id}", 'left')
      ->where('parent_id', null)
      ->orderBy('en_name', 'ASC')
      ->findAll();

    if (!empty($cat)) {
      $sub_categories = $this->categoryModel->select('categories.*, category_translations.name as trans_name')
        ->join('category_translations', "category_translations.category_id = categories.id AND category_translations.language_id = {$this->language_id}", 'left')
        ->where('parent_id', $cat['parent_id'] ?? $cat['id'])
        ->orderBy('en_name', 'ASC')
        ->findAll();
    }

    $countries = $this->locationModel->select('locations.*, location_translations.name as trans_name')
      ->join('location_translations', "location_translations.location_id = locations.id AND location_translations.language_id = {$this->language_id}", 'left')
      ->where('parent_id', null)
      ->orderBy('en_name', 'ASC')
      ->findAll();

    if (!empty($loc)) {
      $cities = $this->locationModel->select('locations.*, location_translations.name as trans_name')
        ->join('location_translations', "location_translations.location_id = locations.id AND location_translations.language_id = {$this->language_id}", 'left')
        ->where('parent_id', $loc['parent_id'] ?? $loc['id'])
        ->orderBy('en_name', 'ASC')
        ->findAll();
    }

    $this->response->removeHeader('Cache-Control');
    $this->response->setHeader('Cache-Control', 'public, max-age=3600');

    return view('items/search', [
      'base_data' => $this->base_data,
      'items' => $items,
      'categories' => $categories,
      'sub_categories' => $sub_categories ?? [],
      'countries' => $countries,
      'cities' => $cities ?? [],
      'keyword' => $keyword,
      'currentCat' => $currentCat ?? null,
      'currentSubCat' => $currentSubCat ?? null,
      'currentCountry' => $currentCountry ?? null,
      'currentCity' => $currentCity ?? null,
      'pager' => $pager,
      'js_sources' => [
        base_url('assets/js/item.search.js'),
      ]
    ]);
  }
}
