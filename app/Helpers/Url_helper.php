<?php

use App\Libraries\Agoda;
use App\Libraries\Carla;
use App\Libraries\Kkday;
use App\Libraries\Klook;
use App\Models\ItemModel;
use App\Models\LanguageModel;
use CodeIgniter\HTTP\URI;

if (!function_exists('locale_url')) {
  function locale_url($path = '')
  {
    $locale = config('App')->currentLocale;
    $defaultLocale = config('App')->defaultLocale;
    if (strtolower($locale) == strtolower($defaultLocale)) {
      return base_url(trim($path, '/'));
    } else {
      return base_url(trim($locale . '/' . $path, '/'));
    }
  }
}

if (!function_exists('clean_first_page_url')) {
  function clean_first_page_url($url)
  {
    $parts = parse_url($url);
    parse_str($parts['query'] ?? '', $query);

    if (isset($query['page']) && (int)$query['page'] === 1) {
      unset($query['page']); // Xoá ?page=1
    }

    $newQuery = http_build_query($query);

    // Build lại URL đầy đủ
    $scheme   = $parts['scheme'] ?? 'https';
    $host     = $parts['host'] ?? '';
    $port     = isset($parts['port']) ? ':' . $parts['port'] : '';
    $path     = $parts['path'] ?? '';
    $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

    return $scheme . '://' . $host . $port . $path . ($newQuery ? '?' . $newQuery : '') . $fragment;
  }
}

if (!function_exists('language_link')) {
  function language_link($locale, $url = null)
  {
    $locale = strtolower($locale);
    $supportedLocales = config('App')->supportedLocales;
    $defaultLocale = config('App')->defaultLocale;

    $uri = $url ? new URI($url) : current_url(true);
    $segments = $uri->getSegments();
    $queryString = $uri->getQuery();

    // Nếu segment đầu là locale thì thay thế
    if (!empty($segments) && in_array(strtolower($segments[0]), array_map('strtolower', $supportedLocales))) {
      $segments[0] = $locale;
    } else {
      // Nếu chưa có locale ở đầu → thêm vào
      array_unshift($segments, $locale);
    }

    if ($locale == strtolower($defaultLocale)) {
      array_shift($segments);
    }

    $newPath = implode('/', $segments);
    $finalUrl = base_url($newPath);

    if (!empty($queryString)) {
      $finalUrl .= '?' . $queryString;
    }

    return $finalUrl;
  }
}

if (!function_exists('category_link')) {
  function category_link($category)
  {
    return locale_url('cat/' . $category['slug']);
  }
}

if (!function_exists('location_link')) {
  function location_link($location)
  {
    return locale_url('loc/' . $location['slug']);
  }
}

if (!function_exists('relate_items_link')) {
  function relate_items_link($category, $location)
  {
    if (!empty($location['slug'])) {
      $params = '?loc=' . $location['slug'];
    }

    return locale_url('cat/' . $category['slug'] . ($params ?? ''));
  }
}

if (!function_exists('search_link')) {
  function search_link($params = [])
  {
    return locale_url('search') . '?' . http_build_query($params);
  }
}

if (!function_exists('item_link')) {
  function item_link($item)
  {
    $itemModel = new ItemModel();
    $itemModel->updateBaseData($item);
    if (empty($item['category']) || empty($item['source'])) {
      return '#';
    }

    $path = $item['category']['slug'] . '/' . $item['source']['slug'] . '/' . $item['source_item_id'] . '-' . $item['slug'];
    if (!empty($item['locales']) && !in_array(strtolower(config('App')->currentLocale), $item['locales'])) {
      return base_url($path);
    }

    return locale_url($path);
  }
}

if (!function_exists('item_img_link')) {
  function item_img_link($item)
  {
    $itemModel = new ItemModel();
    $itemModel->updateBaseData($item);
    if (empty($item['category']) || empty($item['source'])) {
      return no_img_link();
    }

    $path = $item['category']['slug'] . '/' . $item['source']['slug'] . '/img/' . $item['source_item_id'] . '-' . $item['slug'] . '.webp';
    return base_url($path);
  }
}

if (!function_exists('itemThumbLink')) {
  function itemThumbLink($item)
  {
    $itemModel = new ItemModel();
    $itemModel->updateBaseData($item);
    if (empty($item['category']) || empty($item['source'])) {
      return no_img_link();
    }

    $path = $item['category']['slug'] . '/' . $item['source']['slug'] . '/thumb/' . $item['source_item_id'] . '-' . $item['slug'] . '.webp';
    return base_url($path);
  }
}

if (!function_exists('item_book_link')) {
  function item_book_link($item)
  {
    $itemModel = new ItemModel();
    $itemModel->updateBaseData($item);
    if (empty($item['category']) || empty($item['source'])) {
      return '#';
    }

    $path = $item['category']['slug'] . '/' . $item['source']['slug'] . '/book/' . $item['source_item_id'] . '-' . $item['slug'];
    if (!empty($item['locales']) && !in_array(strtolower(config('App')->currentLocale), $item['locales'])) {
      return base_url($path);
    }

    return locale_url($path);
  }
}

if (!function_exists('item_aff_link')) {
  function item_aff_link($item)
  {
    $locale = config('App')->currentLocale ?? config('App')->defaultLocale;

    $itemModel = new ItemModel();
    $itemModel->updateBaseData($item);
    if (empty($item['category']) || empty($item['source'])) {
      return '#';
    }

    switch ($item['source']['name']) {
      case 'Agoda':
        return Agoda::affiliateLink($item['category']['en_name'], $locale, $item['source_item_id'], $item['slug']);

      case 'Klook':
        $languageModel = new LanguageModel();
        $language = $languageModel->where('LOWER(locale)', strtolower($locale))
          ->first();
        $kCode = $language['k_code'] ?? 'en-US';

        return Klook::affiliateLink($item['category']['en_name'], $kCode, $item['source_item_id'], $item['slug']);

      case 'Kkday':
        $languageModel = new LanguageModel();
        $language = $languageModel->where('LOWER(locale)', strtolower($locale))
          ->first();
        $kdCode = $language['kd_code'] ?? 'en-US';

        return Kkday::affiliateLink($kdCode, $item['source_item_id'], $item['slug']);

      case 'Carla':
        $calarId = Carla::decode($item['source_item_id']);

        switch ($item['category']['en_name']) {
          case 'Hotels':
            return Carla::affiliateLink($calarId['lat'] ?? null, $calarId['lng'] ?? null);
        }
        break;
    }

    return item_link($item);
  }
}

if (!function_exists('no_img_link')) {
  function no_img_link()
  {
    return base_url('assets/img/no_img.webp');
  }
}

if (!function_exists('thumbnailLink')) {
  function thumbnailLink()
  {
    return base_url('assets/img/thumbnail.webp');
  }
}

if (!function_exists('avatarLink')) {
  function avatarLink()
  {
    return base_url('assets/img/user.webp');
  }
}

if (!function_exists('item_map_link')) {
  function item_map_link($map)
  {
    return locale_url('map?lat=' . $map['lat'] . '&lng=' . $map['long']);
  }
}
