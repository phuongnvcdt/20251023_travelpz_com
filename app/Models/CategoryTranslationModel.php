<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryTranslationModel extends Model
{
  protected $table = 'category_translations';
  protected $primaryKey = 'id';
  protected $allowedFields = ['category_id', 'language_id', 'name'];
  protected $useTimestamps = true;

  public function getTransName($category, $lang)
  {
    if ($lang) {
      return $this->select('name')
        ->where('category_id', $category['id'])
        ->where('language_id', $lang['id'])
        ->get()
        ->getRow('name');
    }
  }

  public function upsertTransNames($subCategories, $lang, $transNames)
  {
    foreach ($subCategories as $index => $sub) {
      if (!isset($trans_names[$index]) || empty($transNames[$index])) {
        continue;
      }

      $name = $transNames[$index];

      $exists = $this->where('category_id', $sub['id'])
        ->where('language_id', $lang['id'])
        ->first();

      if ($exists) {
        if ($exists['name'] != $name) {
          $this->update($exists['id'], ['name' => $name]);
        }
      } else {
        $this->insert([
          'category_id' => $sub['id'],
          'language_id' => $lang['id'],
          'name' => $name
        ]);
      }
    }
  }
}
