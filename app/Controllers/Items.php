<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SourceModel;
use App\Libraries\Agoda;
use App\Libraries\Booking;
use App\Libraries\Carla;
use App\Libraries\Kkday;
use App\Libraries\Klook;
use App\Libraries\YoutubeWrap;

class Items extends BaseController
{
  protected $sourceModel;

  public function __construct()
  {
    $this->sourceModel = new SourceModel();
  }

  /**
   * $categorySlug = 'hotels' hoặc 'activities'
   * $sourceSlug = 'a' hoặc 'k'
   * $sourceId = số nguồn
   */
  public function show($categorySlug, $sourceSlug, $sourceId, $slug)
  {
    if (!is_bot($this->request)) {
      // TBD
    }

    // Tìm category_id theo tên
    $category = $this->categoryModel
      ->where('slug', $categorySlug)
      ->first();
    $source = $this->sourceModel
      ->where('slug', $sourceSlug)
      ->first();

    if (!$category || !$source) {
      return $this->responseError404();
      // throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    $this->base_data['languages'] = sourceLanguages($this->base_data['languages'], $source);
    if (empty(array_find($this->base_data['languages'], fn($lang) => strtolower($lang['locale']) == strtolower($this->language['locale'] ?? '')))) {
      return redirect()->to(language_link(config('App')->defaultLocale), 301);
    }

    $this->base_data['meta_hreflangs'] = array_intersect_key($this->base_data['meta_hreflangs'], array_flip(array_map(fn($lang) => $lang['code'], $this->base_data['languages'])));

    // Tìm item theo category_id, source_id và source_item_id
    $item = $this->itemModel
      ->where('category_id', $category['id'])
      ->where('source_id', $source['id'])
      ->where('source_item_id', $sourceId)
      ->first();

    if (!empty($item)) {
      $currentUrl = (string) current_url(true);
      $correctUrl = item_link($item);
      if ($currentUrl != $correctUrl) {
        return redirect()->to($correctUrl, 301);
      }
    }

    switch ($source['name']) {
      case 'Agoda':
        $langId = $this->language['a_id'] ?? 1;
        $agoda = new Agoda();
        switch ($category['slug']) {
          case 'hotels':
            $detail = $agoda->getHotelDetail($sourceId, $langId)['data'] ?? null;
            break;
          case 'activities':
            $detail = $agoda->getActivityDetail($sourceId, $langId)['data'] ?? null;
            break;
          default:
            $detail = null;
        }

        return $this->showItemDetail($item, $detail, $category, $source, $sourceId);

      case 'Klook':
        $locale = $this->language['locale'] ?? config('App')->defaultLocale;
        $klook = new Klook();
        switch ($category['slug']) {
          case 'activities':
            $reviews = $klook->getActivityReviews($sourceId, $locale)['data'] ?? null;
            break;
          default:
            $reviews = null;
        }
        return $this->showItemReviews($item, null, $reviews, $category, $source, $sourceId, $slug);

      case 'Kkday':
        $locale = $this->language['kd_code'] ?? '';
        $kkday = new Kkday();
        $reviews = $kkday->getItemReviews($sourceId, $locale)['data'] ?? null;
        return $this->showItemReviews($item, null, $reviews, $category, $source, $sourceId, $slug);

      case 'Carla':
        if (!empty($this->language['locale']) && $this->language['locale'] != config('App')->defaultLocale) {
          return redirect()->to(language_link(config('App')->defaultLocale));
        }

        if (!Carla::isValid($sourceId)) {
          return $this->responseError404();
          // throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $carlaId = Carla::decode($sourceId);
        $booking = new Booking();
        switch ($category['slug']) {
          case 'hotels':
            $reviews = $booking->getHotelReviews($carlaId['id'])['data']['reviews'] ?? null;
            $reviews_count = $booking->getHotelReviews($carlaId['id'])['data']['count'] ?? null;
            break;
          default:
            $reviews = null;
            $reviews_count = null;
        }

        $detail = [
          'map' => [
            'lat' => $carlaId['lat'],
            'long' => $carlaId['lng'],
          ]
        ];

        return $this->showItemReviews($item, $detail, $reviews, $category, $source, $sourceId, $slug, $reviews_count);

      default:
        return $this->responseError404();
        // throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
  }

  public function showImg($category_slug, $source_slug, $type, $source_id)
  {
    $cacheDir = WRITEPATH . "cache/{$type}/";
    $cacheTtl = ($type == 'thumb' ? 7 : 3) * 24 * 60 * 60; // 7 ngày
    $maxWidth = $type == 'thumb' ? 395 : 960;
    $maxHeigt = $type == 'thumb' ? 225 : 540;

    // Tạo thư mục cache nếu chưa có
    if (!is_dir($cacheDir)) {
      mkdir($cacheDir, 0755, true);
    }

    // Dọn cache cũ (1 lần mỗi 100 request để tiết kiệm tài nguyên)
    if (mt_rand(1, 100) === 1) {
      $this->cleanOldCache($cacheDir, $cacheTtl);
    }

    // Tìm category_id theo tên
    $category = $this->categoryModel
      ->where('slug', $category_slug)
      ->first();
    $source = $this->sourceModel
      ->where('slug', $source_slug)
      ->first();

    if (!$category || !$source) {
      return $this->sendLocalImage();
    }

    // Tìm item theo category_id, source_id và source_item_id
    $item = $this->itemModel
      ->where('category_id', $category['id'])
      ->where('source_id', $source['id'])
      ->where('source_item_id', $source_id)
      ->first();

    if (!empty($item)) {
      $currentUrl = (string) current_url(true);
      $correctUrl = $type == 'thumb' ? itemThumbLink($item) : item_img_link($item);
      if ($currentUrl != $correctUrl) {
        return redirect()->to($correctUrl, 301);
      }
    }

    $imageUrl = $item['image'] ?? '';
    if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
      return $this->sendLocalImage();
    }

    // Đường dẫn cache
    $cacheFile = $cacheDir . md5($imageUrl) . '.webp';

    // Nếu ảnh đã cache
    if (is_file($cacheFile)) {
      return $this->sendLocalImage($cacheFile);
    }

    return $this->showImgByUrl($imageUrl, $cacheFile, $maxWidth, $maxHeigt);
  }

  public function book($categorySlug, $sourceSlug, $sourceId, $slug)
  {
    // Tìm category_id theo tên
    $category = $this->categoryModel
      ->where('slug', $categorySlug)
      ->first();
    $source = $this->sourceModel
      ->where('slug', $sourceSlug)
      ->first();

    if (!$category || !$source) {
      return redirect()->to(locale_url());
    }

    // Tìm item theo category_id, source_id và source_item_id
    $item = $this->itemModel
      ->where('category_id', $category['id'])
      ->where('source_id', $source['id'])
      ->where('source_item_id', $sourceId)
      ->first();

    if (!$item) {
      $item = [
        'category' => $category,
        'source' => $source,
        'source_item_id' => $sourceId,
        'slug' => $slug,
      ];
    }

    return redirect()->to(item_aff_link($item));
  }

  public function map()
  {
    $lat = esc($this->request->getGet('lat'));
    $lng = esc($this->request->getGet('lng'));
    return redirect()->to('https://www.google.com/maps/dir/?api=1&destination=' . $lat . ',' . $lng . '&hl=' . $this->language['code'] ?? '');
  }

  private function sendLocalImage($path = null)
  {
    if (empty($path) || !is_file($path)) {
      $path = FCPATH . 'assets/img/no_img.webp';
    }

    $mimeType = mime_content_type($path) ?: 'image/webp';
    $content  = file_get_contents($path);

    return $this->response
      ->setHeader('Content-Type', $mimeType)
      ->setBody($content);
  }

  private function cleanOldCache($dir, $ttl)
  {
    foreach (glob($dir . '*.img') as $file) {
      if (filemtime($file) < (time() - $ttl)) {
        @unlink($file);
      }
    }
  }

  private function showItemDetail($item, $detail, $category, $source, $sourceItemId)
  {
    if (isset($item)) {
      $updateData = [];
    } else {
      $item = [
        'category_id' => $category['id'],
        'source_id' => $source['id'],
        'source_item_id' => $sourceItemId,
        'en_name' => '',
        'rating' => 0,
        'rating_count' => 0,
        'image' => '',
      ];

      if (empty($detail)) {
        return redirect()->to(item_aff_link($item));
      }
    }

    $category['trans_name'] = $this->categoryTranslationModel->getTransName($category, $this->language);
    $item['category'] = $category;
    $item['source'] = $source;

    if (!empty($detail)) {
      $item['trans_name'] = $detail['trans_name'];

      if (!empty($detail['en_name']) && $detail['en_name'] != $item['en_name']) {
        $item['en_name'] = $detail['en_name'];
        if (isset($updateData)) {
          $updateData['en_name'] = $item['en_name'];
        }
      }

      if (!empty($detail['reviews']['score']) && $detail['reviews']['score'] != $item['rating']) {
        $item['rating'] = $detail['reviews']['score'];
        if (isset($updateData)) {
          $updateData['rating'] = $item['rating'];
        }
      }

      if (!empty($detail['reviews']['count']) && $detail['reviews']['count'] != $item['rating_count']) {
        $item['rating_count'] = $detail['reviews']['count'];
        if (isset($updateData)) {
          $updateData['rating_count'] = $item['rating_count'];
        }
      }

      if (!empty($detail['images']) && $detail['images'][0]['url'] != $item['image']) {
        $item['image'] = $detail['images'][0]['url'];
        if (isset($updateData)) {
          $updateData['image'] = $item['image'];
        }
      }

      if (!empty($updateData)) {
        $this->itemModel->update($item['id'], $updateData);
      }
    }

    $country = $this->locationModel->find($item['country_id'] ?? 0);
    if ($country) {
      $country['trans_name'] = $this->locationTranslationModel->getTransName($country, $this->language);
      $new_country_trans_name = $detail['address']['country']['name'] ?? '';
      if (!empty($new_country_trans_name) && $new_country_trans_name != $country['trans_name']) {
        $country['trans_name'] = $new_country_trans_name;
        $this->locationTranslationModel->updateTransName($country, $this->language, $country['trans_name']);
      }
    }

    $city = $this->locationModel->find($item['city_id'] ?? 0);
    if ($city) {
      $city['trans_name'] = $this->locationTranslationModel->getTransName($city, $this->language);
      $new_city_trans_name = $detail['address']['city']['name'] ?? '';
      if (!empty($new_city_trans_name) && $new_city_trans_name != $city['trans_name']) {
        $city['trans_name'] = $new_city_trans_name;
        $this->locationTranslationModel->updateTransName($city, $this->language, $city['trans_name']);
      }
    }

    $breadcrumbs_data = $this->getLocBreadcrumbs([$country, $city], item_title($item));
    $sidebar_data = $this->getSidebarData($country ?? null);
    $relate_items = $this->getRelateItems($city ?? null, $country ?? null, $item['id'] ?? null);
    $tags = $this->getItemTags($item, $country, $city, $category, $detail['categories'] ?? []);
    $this->updateItemMetaData($item, $category, $detail);

    $this->response->removeHeader('Cache-Control');
    $this->response->setHeader('Cache-Control', 'public, max-age=3600');

    return view('items/show', [
      'base_data' => $this->base_data,
      'item' => $item,
      'detail' => $detail,
      'breadcrumb_data' => $breadcrumbs_data,
      'sidebar_data' => $sidebar_data,
      'relate_items' => $relate_items,
      'tags' => $tags,
      'js_sources' => [
        base_url('assets/js/item.show.js'),
      ]
    ]);
  }

  private function showItemReviews($item, $detail, $reviews, $category, $source, $sourceItemId, $slug, $reviews_count = null)
  {
    if (empty($item)) {
      $item = [
        'category_id' => $category['id'],
        'source_id' => $source['id'],
        'source_item_id' => $sourceItemId,
        'en_name' => slugToTitle($slug),
        'rating' => random_float(4.5, 5),
        'rating_count' => rand(100, 999),
        'image' => '',
        'slug' => $slug
      ];

      if (empty($reviews)) {
        return redirect()->to(item_aff_link($item));
      }
    }

    $category['trans_name'] = $this->categoryTranslationModel->getTransName($category, $this->language);
    $item['category'] = $category;
    $item['source'] = $source;

    if (!empty($reviews_count) && $reviews_count != $item['rating_count']) {
      $item['rating_count'] = $reviews_count;
      if (isset($item['id'])) {
        $this->itemModel->update($item['id'], ['rating_count' => $item['rating_count']]);
      }
    }

    $country = $this->locationModel->find($item['country_id'] ?? 0);
    $city = $this->locationModel->find($item['city_id'] ?? 0);

    $breadcrumbs_data = $this->getLocBreadcrumbs([$country, $city], item_title($item));
    $sidebar_data = $this->getSidebarData($country ?? null);
    $relate_items = $this->getRelateItems($city ?? null, $country ?? null, $item['id'] ?? null);
    $tags = $this->getItemTags($item, $country, $city, $category);
    $this->updateItemMetaData($item, $category);

    $this->response->removeHeader('Cache-Control');
    $this->response->setHeader('Cache-Control', 'public, max-age=3600');

    return view('items/show', [
      'base_data' => $this->base_data,
      'item' => $item,
      'detail' => $detail,
      'reviews' => $reviews,
      'breadcrumb_data' => $breadcrumbs_data,
      'sidebar_data' => $sidebar_data,
      'relate_items' => $relate_items,
      'tags' => $tags,
      'js_sources' => [
        base_url('assets/js/item.show.js'),
      ],
      'css_sources' => [
        base_url('assets/css/r_style.css')
      ]
    ]);
  }

  private function updateItemMetaData($item, $category, $detail = null)
  {
    $fullTitle = 'TravelPZ - ' . item_title($item);
    $this->base_data['meta_title'] = mb_strlen($fullTitle) < 70 ? $fullTitle : mb_strimwidth(item_title($item), 0, 69, '...');
    $this->base_data['meta_image'] = item_img_link($item);

    $meta_descriptions = [
      $detail['address']['full'] ?? '',
      strip_tags($detail['description'] ?? $item['en_description'] ?? ''),
      item_title($item)
    ];
    $this->base_data['meta_description'] = mb_strimwidth(implode('. ', array_filter($meta_descriptions, fn($des) => !empty($des))), 0, 160, '...');

    $this->base_data['meta_json_ld'] = [
      "@context" => "https://schema.org/",
      "@type" => 'Review',
      "author" => [
        "@type" => "Organization",
        "name" => "TravelPZ"
      ],
      "reviewRating" => [
        "@type" => "Rating",
        "ratingValue" => $item['rating'],
        "bestRating" => 5,
      ],
      "itemReviewed" => [
        "@type" => $category['en_name'] == 'Hotels' ? 'Hotel' : 'Product',
        "name" => $item['trans_name'] ?? $item['en_name'],
        "image" => item_img_link($item),
        "aggregateRating" => [
          "@type" => "AggregateRating",
          "ratingValue" => $item['rating'],
          "reviewCount" => $item['rating_count']
        ]
      ],
    ];

    if (!empty($item['youtube_id'])) {
      $videoInfo = YoutubeWrap::getVideoInfo($item['youtube_id'], $this->language['code'] ?? '');
      if (!empty($videoInfo)) {
        $videoTitle = $videoInfo['snippet']['localized']['title'] ?? $videoInfo['snippet']['title'] ?? item_title($item);
        $videoDescription = $videoInfo['snippet']['localized']['description'] ?? $videoInfo['snippet']['description'] ?? str_replace('{{name}}', $item['trans_name'] ?? $item['en_name'] ?? '', trans('F_MetaVideoDescription'));
        $videoDescription = preg_replace('/https?:\/\/\S+|www\.\S+/', '', $videoDescription);
        $videoDescription = preg_replace('/#\S+/', '', $videoDescription);
        $videoDescription = str_replace('_lct_', '', $videoDescription);
        $videoDescription = trim(preg_replace('/\s+/', ' ', $videoDescription));

        $this->base_data['meta_json_ld']['itemReviewed']['video'] = [
          "@type" => "VideoObject",
          "name" => $videoTitle,
          "description" => mb_strimwidth($videoDescription, 0, 160, '...'),
          "thumbnailUrl" => [
            "https://i.ytimg.com/vi/{$item['youtube_id']}/maxresdefault.jpg"
          ],
          "uploadDate" => $videoInfo['snippet']['publishedAt'] ?? '',
          "embedUrl" => "https://www.youtube.com/embed/{$item['youtube_id']}",
          "contentUrl" => "https://www.youtube.com/watch?v={$item['youtube_id']}"
        ];
      }
    }

    if (!empty($detail['address'])) {
      $this->base_data['meta_json_ld']['itemReviewed']['address'] = [
        "@type" => "PostalAddress",
        "streetAddress" => $detail['address']['address'] ?? '',
        "addressLocality" => $detail['address']['city']['name'] ?? '',
        "addressCountry" => $detail['address']['country']['name'] ?? '',
        "postalCode" => $detail['address']['post_code'] ?? '',
      ];
    }

    $this->base_data['meta_keywords'] = array_merge([
      $detail['trans_name'] ?? $item['en_name'],
      $category['trans_name'] ?? $category['en_name'],
      $detail['address']['full'] ?? '',
    ], $detail['categories'] ?? []);
  }

  private function getItemTags($item, $country, $city, $category, $new_sub_categories = null)
  {
    $tags = [];
    if (!empty($country)) {
      array_push($tags, [
        'link' => location_link($country),
        'name' => $country['trans_name'] ?? $country['en_name'] ?? ''
      ]);
    }

    if (!empty($city)) {
      array_push($tags, [
        'link' => location_link($city),
        'name' => $city['trans_name'] ?? $city['en_name'] ?? ''
      ]);
    }

    if (!empty($category)) {
      array_push($tags, [
        'link' => category_link($category),
        'name' => $category['trans_name'] ?? $category['en_name'] ?? ''
      ]);
    }

    $sub_categories = $this->categoryModel->getSubCategories($item['id'] ?? null) ?? [];
    if (!empty($sub_categories) && !empty($new_sub_categories)) {
      $this->categoryTranslationModel->upsertTransNames($sub_categories, $this->language, $new_sub_categories);
      $sub_categories = $this->categoryModel->getSubCategories($item['id'] ?? null) ?? [];
    }

    if (!empty($sub_categories)) {
      $tags = array_merge($tags, array_map(fn($sc) => [
        'link' => category_link($sc),
        'name' => $sc['trans_name'] ?? $sc['en_name'] ?? ''
      ], $sub_categories));
    }

    return $tags;
  }

  private function showImgByUrl($image_url, $cache_file, $max_width, $max_height)
  {
    // Tải ảnh từ URL với timeout
    $context = stream_context_create([
      'http' => [
        'timeout' => 3, // tối đa 3s
        'header'  => "User-Agent: Mozilla/5.0"
      ]
    ]);

    // Lấy nội dung ảnh từ URL
    $imageContent = @file_get_contents($image_url, false, $context);
    if ($imageContent === false || strlen($imageContent) > 5 * 1024 * 1024) { // >5MB
      return $this->sendLocalImage();
    }

    if (function_exists('imagecreatefromstring')) {
      $srcImg = @imagecreatefromstring($imageContent);
      if ($srcImg) {
        $width = imagesx($srcImg);
        $height = imagesy($srcImg);

        if ($width > $max_width || $height > $max_height) {
          $ratio = min($max_width / $width, $max_height / $height);
          $newWidth  = (int)($width * $ratio);
          $newHeight = (int)($height * $ratio);

          $dstImg = imagecreatetruecolor($newWidth, $newHeight);
          imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        } else {
          $dstImg = $srcImg;
        }

        // Lưu theo mime gốc
        ob_start();
        imagewebp($dstImg, null, 70); // chất lượng 70%
        $finalContent = ob_get_clean();

        imagedestroy($srcImg);
        if ($dstImg !== $srcImg) imagedestroy($dstImg);

        file_put_contents($cache_file, $finalContent);
      } else {
        file_put_contents($cache_file, $imageContent);
      }
    } else {
      // fallback: lưu gốc nhưng convert sang webp nếu có GD
      $tmp = imagecreatefromstring($imageContent);
      if ($tmp) {
        ob_start();
        imagewebp($tmp, null, 70);
        $finalContent = ob_get_clean();
        imagedestroy($tmp);

        file_put_contents($cache_file, $finalContent);
        return $this->response
          ->setHeader('Content-Type', 'image/webp')
          ->setBody($finalContent);
      }

      // fallback nếu không có GD
      file_put_contents($cache_file, $imageContent);
    }

    // Trả về file cache cuối cùng (gốc hoặc webp)
    $mimeType = 'image/webp';
    return $this->response
      ->setHeader('Content-Type', $mimeType)
      ->setBody(file_get_contents($cache_file));
  }
}
