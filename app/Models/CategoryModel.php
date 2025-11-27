<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
  protected $table = 'categories';
  protected $primaryKey = 'id';
  protected $allowedFields = ['en_name', 'slug', 'parent_id'];
  protected $useTimestamps = true;

  // Tự động sinh slug trước khi insert hoặc update
  protected $beforeInsert = ['generateSlug'];
  protected $beforeUpdate = ['generateSlug'];

  public function getPaged($limit, $offset)
  {
    return $this->orderBy('IF(parent_id IS NULL, 0, 1) ASC, updated_at DESC')
      ->findAll($limit, $offset);
  }

  public function getLink($cat, $locale = '')
  {
    $localeUrl =  rtrim(base_url(), '/');
    $defaultLocale = config('App')->defaultLocale;
    if (!empty($locale) && $locale != $defaultLocale) {
      $localeUrl .= '/' . $locale;
    }

    return "{$localeUrl}/cat/{$cat['slug']}";
  }

  public function getSubCategories($itemId)
  {
    return $this->db->table('item_sub_categories isc')
      ->select('c.*, ct.name as trans_name')
      ->join('categories c', 'isc.sub_category_id = c.id')
      ->join('category_translations ct', 'ct.category_id = c.id AND ct.language_id = ' . (int)$this->language_id, 'left')
      ->where('isc.item_id', $itemId)
      ->get()
      ->getResultArray();
  }

  protected function generateSlug(array $data)
  {
    if (isset($data['data']['en_name'])) {
      $name = $data['data']['en_name'];
      //$name = $this->toPlural($name);
      $slug = strtolower($name);
      $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
      $slug = trim($slug, '-');

      $data['data']['slug'] = $slug;
    }
    return $data;
  }

  protected function toPlural($word)
  {
    $lastLetter = strtolower(substr($word, -1));
    $lastTwo = strtolower(substr($word, -2));

    // Một số quy tắc cơ bản
    if ($lastLetter === 'y' && !in_array(strtolower(substr($word, -2, 1)), ['a', 'e', 'i', 'o', 'u'])) {
      return substr($word, 0, -1) . 'ies'; // Activity → Activities
    } elseif (in_array($lastLetter, ['s', 'x', 'z']) || in_array($lastTwo, ['sh', 'ch'])) {
      return $word . 'es'; // Bus → Buses, Match → Matches
    } else {
      return $word . 's'; // Hotel → Hotels
    }
  }

  public function insertSubCategories($category, $subCategories)
  {
    $subIds = [];
    foreach ($subCategories as $sub) {
      if (!empty($sub)) {
        $exists = $this->where('parent_id', $category['id'])
          ->where('en_name', $sub)
          ->first();

        if ($exists) {
          array_push($subIds, $exists);
        } else {
          $id = $this->insert([
            'parent_id' => $category['id'],
            'en_name' => $sub
          ]);
          array_push($subIds, $this->find($id));
        }
      }
    }

    return $subIds;
  }
}
