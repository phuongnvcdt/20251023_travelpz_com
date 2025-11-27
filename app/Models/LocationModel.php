<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\LocationSourceModel;

class LocationModel extends Model
{
  protected $table = 'locations';
  protected $primaryKey = 'id';
  protected $allowedFields = ['en_name', 'slug', 'parent_id'];
  protected $useTimestamps = true;

  protected $beforeInsert = ['generateSlug'];
  protected $beforeUpdate = ['generateSlug'];

  public function getPaged($limit, $offset)
  {
    return $this->orderBy('IF(parent_id IS NULL, 0, 1) ASC, updated_at DESC')
      ->findAll($limit, $offset);
  }

  public function getLink($loc, $locale = '')
  {
    $localeUrl =  rtrim(base_url(), '/');
    $defaultLocale = config('App')->defaultLocale;
    if (!empty($locale) && $locale != $defaultLocale) {
      $localeUrl .= '/' . $locale;
    }

    return "{$localeUrl}/loc/{$loc['slug']}";
  }

  protected function generateSlug(array $data)
  {
    if (isset($data['data']['en_name'])) {
      // Tạo slug cơ bản
      $slug = strtolower($data['data']['en_name']);
      $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
      $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
      $slug = trim($slug, '-');

      // Lưu bản gốc để nhân bản
      $baseSlug = $slug;
      $index = 1;

      // Nếu slug đã tồn tại → thêm -1, -2, -3...
      while ($this->slugExists($slug)) {
        $slug = $baseSlug . '-' . $index;
        $index++;
      }

      $data['data']['slug'] = $slug;
    }
    return $data;
  }

  public function findBySource($source, $locationSourceId, $parentId = null)
  {
    $loc = $this->select("locations.*, location_sources.source_id as source_id, location_sources.location_source_id as location_source_id")
      ->join('location_sources', 'locations.id = location_sources.location_id', 'left')
      ->where('source_id', $source['id'])
      ->where('location_source_id', $locationSourceId)
      ->where('parent_id', $parentId)
      ->first();

    if (!$loc) {
      return null;
    }

    return $loc;
  }

  public function findByEnName($en_name, $parent_id = null)
  {
    return $this->where('en_name', $en_name)
      ->where('parent_id', $parent_id)
      ->first();
  }

  public function insertBySource($source, $location_source_id, $en_name, $parent_id = null)
  {
    $location = $this->findByEnName($en_name, $parent_id);
    if ($location) {
      $locationId = $location['id'];
    } else {
      $insertData = [
        'en_name' => $en_name,
        'parent_id' => $parent_id,
      ];

      $locationId = $this->insert($insertData);
      if (!$locationId) {
        return null;
      }
    }

    if (!empty($location_source_id)) {
      $locationSourceModel = new LocationSourceModel();
      $locationSourceModel->insert([
        'location_id' => $locationId,
        'source_id' => $source['id'],
        'location_source_id' => $location_source_id
      ]);
    }

    return $this->find($locationId);
  }

  protected function slugExists($slug)
  {
    return $this->where('slug', $slug)->countAllResults() > 0;
  }
}
