<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
  protected $table = 'items';
  protected $primaryKey = 'id';
  protected $allowedFields = [
    'en_name',
    'source_id',
    'source_item_id',
    'category_id',
    'country_id',
    'city_id',
    'en_description',
    'image',
    'rating',
    'rating_count',
    'youtube_id',
  ];
  protected $useTimestamps = true;

  public function getPaged($limit, $offset)
  {
    return $this->select('items.*, categories.slug as category_slug, sources.slug as source_slug')
      ->join('categories', 'categories.id = items.category_id', 'left')
      ->join('sources', 'sources.id = items.source_id', 'left')
      ->orderBy('updated_at', 'DESC')
      ->findAll($limit, $offset);
  }

  public function getLink($item, $locale = '')
  {
    $localeUrl =  rtrim(base_url(), '/');
    $defaultLocale = config('App')->defaultLocale;
    if (!empty($locale) && $locale != $defaultLocale) {
      $localeUrl .= '/' . $locale;
    }

    if (!isset($item['category_slug'])) {
      $item['category_slug'] = new CategoryModel()->find($item['category_id'])['slug'] ?? '';
    }

    if (!isset($item['source_slug'])) {
      $item['source_slug'] = new SourceModel()->find($item['source_id'])['slug'] ?? '';
    }
    return "{$localeUrl}/{$item['category_slug']}/{$item['source_slug']}/{$item['source_item_id']}-{$this->getSlug($item)}";
  }

  public function getSlug($item)
  {
    $slug = strtolower($item['en_name'] ?? '');
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
  }

  public function updateBaseData(&$item)
  {
    if (empty($item['category'])) {
      $categoryModel = new CategoryModel();
      $item['category'] = $categoryModel->find($item['category_id'] ?? 0);
    }

    if (empty($item['source'])) {
      $sourceModel = new SourceModel();
      $item['source'] = $sourceModel->find($item['source_id'] ?? 0);
    }

    if (empty($item['slug'])) {
      $item['slug'] = $this->getSlug($item);
    }
  }
}
