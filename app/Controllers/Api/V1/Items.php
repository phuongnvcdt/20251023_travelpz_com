<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CategoryModel;
use App\Models\SourceModel;
use App\Models\LocationModel;
use App\Libraries\Agoda;
use App\Models\ItemSubCategoryModel;

class Items extends ResourceController
{
  protected $format = 'json';
  protected $modelName = 'App\Models\ItemModel';

  public function index()
  {
    $items = $this->model->findAll();
    return $this->respond($items);
  }

  public function show($id = null)
  {
    $item = $this->model->find($id);
    if (!$item) return $this->failNotFound('Item not found');
    return $this->respond($item);
  }

  public function create()
  {
    $data = $this->request->getJSON();
    if (!isset($data->id, $data->category, $data->source)) {
      return $this->respond([
        'success' => false,
        'message' => 'Invalid parameters',
      ], $this->codes['invalid_data']);
    }

    $categoryModel = new CategoryModel();
    $category = $categoryModel
      ->where('slug', $data->category)
      ->first();

    if (!$category) {
      return $this->respond([
        'success' => false,
        'message' => 'Invalid category',
      ], $this->codes['invalid_data']);
    }

    $sourceModel = new SourceModel();
    $source = $sourceModel
      ->where('slug', $data->source)
      ->first();

    if (!$source) {
      return $this->respond([
        'success' => false,
        'message' => 'Invalid source',
      ], $this->codes['invalid_data']);
    }

    $existing = $this->model->where('category_id', $category['id'])
      ->where('source_id', $source['id'])
      ->where('source_item_id', (string) $data->id)
      ->first();
    if ($existing) {
      if ($existing['youtube_id'] == $data->youtube) {
        return $this->respond([
          'success' => true,
          'message' => 'Item already exists',
          'data' => $existing
        ], $this->codes['resource_exists']);
      }

      $this->model->update($existing['id'], [
        'youtube_id' => $data->youtube,
      ]);
      $existing = $this->model->find($existing['id']);

      return $this->respondUpdated([
        'success' => true,
        'message' => 'Item updated',
        'data' => $existing
      ]);
    }

    switch ($source['name']) {
      case 'Agoda':
        $langId = $this->language['a_id'] ?? 1;
        $agoda = new Agoda();
        switch ($category['slug']) {
          case 'hotels':
            $detail = $agoda->getHotelDetail($data->id, $langId);
            break;
          case 'activities':
            $detail = $agoda->getActivityDetail($data->id, $langId);
            break;
        }

        return $this->addAgodaItem($data, $detail, $source, $category);

      case 'Klook':
        return $this->addKlookItem($data, $source, $category);

      case 'Kkday':
        return $this->addKkdayItem($data, $source, $category);

      case 'Carla':
        return $this->addCarlaItem($data, $source, $category);

      default:
        return $this->respond([
          'success' => false,
          'message' => 'Invalid source',
        ], $this->codes['invalid_data']);
    }
  }

  public function update($id = null)
  {
    $data = $this->request->getJSON();
    $this->model->update($id, $data);
    return $this->respond($data);
  }

  public function delete($id = null)
  {
    $this->model->delete($id);
    return $this->respondDeleted(['id' => $id]);
  }

  private function addAgodaItem($data, $detail, $source, $category)
  {
    if (empty($detail) || $detail['success'] == false) {
      return $this->respond([
        'success' => false,
        'message' => 'Failed to get detail',
      ], $this->codes['not_implemented']);
    }

    if (empty($detail['data'])) {
      return $this->respond([
        'success' => false,
        'message' => 'NO_DATA',
      ], $this->codes['resource_not_found']);
    }

    $locationModel = new LocationModel();
    if (isset($detail['data']['address']['country'])) {
      $country = $locationModel->findBySource($source, $detail['data']['address']['country']['id'] ?? null, null);
      if (!$country && !empty($detail['data']['address']['country']['name'])) {
        $country = $locationModel->insertBySource($source, $detail['data']['address']['country']['id'], $detail['data']['address']['country']['name']);
        if (!$country) {
          return $this->respond([
            'success' => false,
            'message' => 'Failed to create country',
          ], $this->codes['not_implemented']);
        }
      }
    }

    if (isset($detail['data']['address']['city'])) {
      $city = $locationModel->findBySource($source, $detail['data']['address']['city']['id'] ?? null, $country['id']);
      if (!$city && !empty($detail['data']['address']['city']['name'])) {
        $city = $locationModel->insertBySource($source, $detail['data']['address']['city']['id'], $detail['data']['address']['city']['name'], $country['id']);
        if (!$city) {
          return $this->respond([
            'success' => false,
            'message' => 'Failed to create city',
          ], $this->codes['not_implemented']);
        }
      }
    }

    $rating = $detail['data']['reviews']['score'];
    if (empty($rating) || $rating == 0) {
      $rating = random_float(4.5, 5);
    }

    $rating_count = $detail['data']['reviews']['count'];
    if (empty($rating_count) || $rating_count == 0) {
      $rating_count = rand(100, 999);
    }

    $insertData = [
      'en_name' => $detail['data']['en_name'],
      'source_id' => $source['id'],
      'source_item_id' => (string) $data->id,
      'category_id' => $category['id'],
      'country_id' => $country['id'] ?? null,
      'city_id' => $city['id'] ?? null,
      'image' => $detail['data']['images'][0]['url'] ?? '',
      'rating' => $rating,
      'rating_count' => $rating_count,
      'youtube_id' => $data->youtube,
    ];

    $itemId = $this->model->insert($insertData);
    $item = $this->model->find($itemId);

    $sub_categories = $detail['data']['categories'] ?? [];
    if (!empty($sub_categories)) {
      $categoryModel = new CategoryModel();
      $subs = $categoryModel->insertSubCategories($category, $sub_categories);
      $itemSubCategory = new ItemSubCategoryModel();
      foreach ($subs as $sub) {
        $itemSubCategory->insert([
          'item_id' => $item['id'],
          'sub_category_id' => $sub['id']
        ]);
      }
    }

    return $this->respondCreated([
      'success' => true,
      'message' => 'Item created',
      'data' => $item
    ]);
  }

  private function addKlookItem($data, $source, $category)
  {
    $detail = $data->detail;
    if (!$detail) {
      return $this->respond([
        'success' => false,
        'message' => 'Failed to get detail',
      ], $this->codes['invalid_data']);
    }

    $detail = json_decode(json_encode($detail), true);

    $locationModel = new LocationModel();
    $country_id = $detail['address']['country']['id'] ?? null;
    $country_name = $detail['address']['country']['name'] ?? null;
    if (!empty($country_id)) {
      $country = $locationModel->findBySource($source, $country_id, null);
      if (!$country && !empty($country_name)) {
        $country = $locationModel->insertBySource($source, $country_id, $country_name);
        if (!$country) {
          return $this->respond([
            'success' => false,
            'message' => 'Failed to create country',
          ], $this->codes['not_implemented']);
        }
      }
    }

    if (isset($country)) {
      $city_id = $detail['address']['city']['id'] ?? null;
      $city_name = $detail['address']['city']['name'] ?? null;
      if (!empty($city_id)) {
        $city = $locationModel->findBySource($source, $city_id, $country['id']);
        if (!$city && !empty($city_name)) {
          $city = $locationModel->insertBySource($source, $city_id, $city_name, $country['id']);
          if (!$city) {
            return $this->respond([
              'success' => false,
              'message' => 'Failed to create city',
            ], $this->codes['not_implemented']);
          }
        }
      }
    }

    $rating = $detail['reviews']['score'];
    if (empty($rating) || $rating == 0) {
      $rating = random_float(4.5, 5);
    }

    $rating_count = $detail['reviews']['count'];
    if (empty($rating_count) || $rating_count == 0) {
      $rating_count = rand(100, 999);
    }

    $insertData = [
      'en_name' => $detail['title'],
      'source_id' => $source['id'],
      'source_item_id' => (string) $data->id,
      'category_id' => $category['id'],
      'country_id' => $country['id'] ?? null,
      'city_id' => $city['id'] ?? null,
      'en_description' => nl2br($detail['description'] ?? ''),
      'image' => $detail['images'][0] ?? '',
      'rating' => $rating,
      'rating_count' => $rating_count,
      'youtube_id' => $data->youtube,
    ];

    $itemId = $this->model->insert($insertData);
    $item = $this->model->find($itemId);

    $sub_categories = $detail['categories'] ?? [];
    if (!empty($sub_categories)) {
      $categoryModel = new CategoryModel();
      $subs = $categoryModel->insertSubCategories($category, $sub_categories);
      $itemSubCategory = new ItemSubCategoryModel();
      foreach ($subs as $sub) {
        $itemSubCategory->insert([
          'item_id' => $item['id'],
          'sub_category_id' => $sub['id']
        ]);
      }
    }

    return $this->respondCreated([
      'success' => true,
      'message' => 'Item created',
      'data' => $item
    ]);
  }

  private function addKkdayItem($data, $source, $category)
  {
    $detail = $data->detail;
    if (!$detail) {
      return $this->respond([
        'success' => false,
        'message' => 'Failed to get detail',
      ], $this->codes['invalid_data']);
    }

    $detail = json_decode(json_encode($detail), true);

    $locationModel = new LocationModel();
    $country_id = $detail['address']['country']['id'] ?? null;
    $country_name = $detail['address']['country']['name'] ?? null;
    if (!empty($country_id)) {
      $country = $locationModel->findBySource($source, $country_id, null);
      if (!$country && !empty($country_name)) {
        $country = $locationModel->insertBySource($source, $country_id, $country_name);
        if (!$country) {
          return $this->respond([
            'success' => false,
            'message' => 'Failed to create country',
          ], $this->codes['not_implemented']);
        }
      }
    }

    if (isset($country)) {
      $city_id = $detail['address']['city']['id'] ?? null;
      $city_name = $detail['address']['country']['name'] ?? null;
      if (!empty($city_id)) {
        $city = $locationModel->findBySource($source, $city_id, $country['id']);
        if (!$city && !empty($city_name)) {
          $city = $locationModel->insertBySource($source, $city_id, $city_name, $country['id']);
          if (!$city) {
            return $this->respond([
              'success' => false,
              'message' => 'Failed to create city',
            ], $this->codes['not_implemented']);
          }
        }
      }
    }

    $rating = $detail['reviews']['score'];
    if (empty($rating) || $rating == 0) {
      $rating = random_float(4.5, 5);
    }

    $rating_count = $detail['reviews']['count'];
    if (empty($rating_count) || $rating_count == 0) {
      $rating_count = rand(100, 999);
    }

    $insertData = [
      'en_name' => $detail['title'],
      'source_id' => $source['id'],
      'source_item_id' => (string) $data->id,
      'category_id' => $category['id'],
      'country_id' => $country['id'] ?? null,
      'city_id' => $city['id'] ?? null,
      'en_description' => nl2br($detail['description'] ?? ''),
      'image' => $detail['images'][0] ?? '',
      'rating' => $rating,
      'rating_count' => $rating_count,
      'youtube_id' => $data->youtube,
    ];

    $itemId = $this->model->insert($insertData);
    $item = $this->model->find($itemId);

    $sub_categories = $detail['categories'] ?? [];
    if (!empty($sub_categories)) {
      $categoryModel = new CategoryModel();
      $subs = $categoryModel->insertSubCategories($category, $sub_categories);
      $itemSubCategory = new ItemSubCategoryModel();
      foreach ($subs as $sub) {
        $itemSubCategory->insert([
          'item_id' => $item['id'],
          'sub_category_id' => $sub['id']
        ]);
      }
    }

    return $this->respondCreated([
      'success' => true,
      'message' => 'Item created',
      'data' => $item
    ]);
  }

  private function addCarlaItem($data, $source, $category)
  {
    $detail = $data->detail;
    if (!$detail) {
      return $this->respond([
        'success' => false,
        'message' => 'Failed to get detail',
      ], $this->codes['invalid_data']);
    }

    $detail = json_decode(json_encode($detail), true);

    $locationModel = new LocationModel();
    if (!empty($detail['country'])) {
      $country = $locationModel->insertBySource($source, null, $detail['country']);
      if (!$country) {
        return $this->respond([
          'success' => false,
          'message' => 'Failed to create country',
        ], $this->codes['not_implemented']);
      }
    }

    $rating = (float)($detail['rating']['rating'] ?? 0) / 2;
    if (empty($rating) || $rating == 0) {
      $rating = random_float(4.5, 5);
    }

    $rating_count = rand(100, 999);

    $insertData = [
      'en_name' => $detail['title'],
      'source_id' => $source['id'],
      'source_item_id' => (string) $data->id,
      'category_id' => $category['id'],
      'country_id' => $country['id'] ?? null,
      'en_description' => $detail['description'] ?? null,
      'image' => $detail['photos'][0] ?? '',
      'rating' => $rating,
      'rating_count' => $rating_count,
      'youtube_id' => $data->youtube,
    ];

    $itemId = $this->model->insert($insertData);
    $item = $this->model->find($itemId);

    return $this->respondCreated([
      'success' => true,
      'message' => 'Item created',
      'data' => $item
    ]);
  }
}
