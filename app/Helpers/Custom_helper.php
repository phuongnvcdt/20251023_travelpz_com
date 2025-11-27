<?php

use App\Models\SourceModel;

if (!function_exists('trans')) {
  function trans(string $key, array $args = [], string $locale = 'en-US')
  {
    $defaultLocale = config('App')->defaultLocale;
    $currentLocale = config('App')->currentLocale;

    $keyWithPrefix = (strpos($key, '.') !== false) ? $key : 'App.' . $key;

    // 1️⃣ Thử dịch theo ngôn ngữ hiện tại
    $text = lang($keyWithPrefix, $args, correct_locale($currentLocale));

    // 2️⃣ Nếu chưa có bản dịch, thử fallback sang ngôn ngữ mặc định
    if ($text === $keyWithPrefix || $text === $key) {
      if ($currentLocale !== $defaultLocale) {
        $text = lang($keyWithPrefix, $args, $defaultLocale);
      }
    }

    // 3️⃣ Nếu vẫn không có, hiển thị chính key gốc (loại bỏ prefix App.)
    if ($text === $keyWithPrefix || $text === $key) {
      $text = $key;
    }

    return $text;
  }
}

if (!function_exists('correct_locale')) {
  function correct_locale($locale)
  {
    return preg_replace_callback('/^([a-z]{2})[-_]?([a-zA-Z]{2})$/i', fn($m) => strtolower($m[1]) . '-' . strtoupper($m[2]), $locale);
  }
}

if (!function_exists('render_stars')) {
  function render_stars($rating)
  {
    $html = '<div class="review-star" style="display:inline-block;"><span style="cursor: default;">';
    $rating = max(0, min(5, floatval($rating)));
    $full = floor($rating);
    $decimal = $rating - $full;

    for ($i = 1; $i <= 5; $i++) {
      $width = '0px';
      if ($i <= $full) {
        $width = 'auto';
      } elseif ($i == $full + 1 && $decimal > 0) {
        $width = floor($decimal * 100) . '%';
      }

      $html .= '
        <div class="rating-symbol" style="display:inline-block; position: relative;">
            <div class="rating-symbol-background fa fa-star" style="visibility: visible;"></div>
            <div class="rating-symbol-foreground" style="display:inline-block; position:absolute; overflow:hidden; left:0; width:' . $width . ';">
                <span class="fa fa-star symbol-filled"></span>
            </div>
        </div>';
    }

    $html .= '</span></div>';
    return $html;
  }
}

if (!function_exists('short_number')) {
  function short_number($num)
  {
    if ($num >= 1000000000) {
      return round($num / 1000000000, 1) . 'B';
    } elseif ($num >= 1000000) {
      return round($num / 1000000, 1) . 'M';
    } elseif ($num >= 1000) {
      return round($num / 1000, 1) . 'k';
    }
    return (string)$num;
  }
}

if (!function_exists('item_title')) {
  function item_title($item)
  {
    return str_replace('{{name}}', $item['trans_name'] ?? $item['en_name'] ?? '', trans('F_ItemTitle'));
  }
}

if (!function_exists('relate_items_title')) {
  function relate_items_title($category, $location)
  {
    $result = str_replace('{{category}}', $category['trans_name'] ?? $category['en_name'], trans('F_RelateItemsTitle'));
    $result = str_replace('{{location}}', $location['trans_name'] ?? $location['en_name'] ?? trans('the world'), $result);
    return $result;
  }
}

if (!function_exists('meta_title')) {
  function meta_title($name = null)
  {
    $title = str_replace('{{name}}', $name ?? trans('the world'), trans('F_MetaTitle'));
    $fullTitle = 'TravelPZ - ' . $title;
    return mb_strlen($fullTitle) < 70 ? $fullTitle : mb_strimwidth($title, 0, 69, '...');
  }
}

if (!function_exists('meta_description')) {
  function meta_description($category = null, $location = null)
  {
    $result = str_replace('{{category}}', strtolower($category['trans_name'] ?? $category['en_name'] ?? trans('destinations and tours')), trans('F_MetaDescription'));
    $result = str_replace('{{location}}', $location['trans_name'] ?? $location['en_name'] ?? trans('the world'), $result);
    return mb_strimwidth($result, 0, 160, '...');
  }
}

if (!function_exists('is_bot')) {
  function is_bot($request)
  {
    $agent = $request->getUserAgent();
    if ($agent->isRobot()) {
      return true;
    }

    $extraBots = [
      'bot',
      'tool',
      'spider',
      'GoogleOther',
      'YouTube',
      'Pinterest',
      'Slurp',
      'YandexImages',
      'Sogou',
      'ia_archiver',
      'facebookexternalhit',
      'meta-externalagent',
      'crawl',
      'slurp',
      'mediapartners-google',
      'HeadlessChrome',
      'okhttp',
      'Pingdom',
      'BingPreview',
      'Google-AdWords-Express',
      'Google-Safety',
      'CMS-Checker',
      'python-httpx',
      'Go-http-client',
      'Lighthouse',
      'Google-Read-Aloud',
      'ChatGPT-User',
    ];

    $ua = $agent->getAgentString();
    $pattern = '/' . implode('|', array_map('preg_quote', $extraBots)) . '/i';
    return preg_match($pattern, $ua) === 1;
  }
}

if (!function_exists('is_mobile')) {
  function is_mobile($request)
  {
    $agent = $request->getUserAgent();
    return $agent->isMobile();
  }
}

if (!function_exists('random_float')) {
  function random_float(float $min, float $max, int $precision = 2): float
  {
    $random = $min + (random_int(0, PHP_INT_MAX) / PHP_INT_MAX) * ($max - $min);
    return round($random, $precision);
  }
}

if (!function_exists('pluralToSingular')) {
  function pluralToSingular($word)
  {
    $word = strtolower(trim($word));

    // Quy tắc đặc biệt
    $irregular = [
      'people' => 'person',
      'men' => 'man',
      'women' => 'woman',
      'children' => 'child',
      'teeth' => 'tooth',
      'feet' => 'foot',
      'mice' => 'mouse',
      'geese' => 'goose',
    ];
    if (isset($irregular[$word])) {
      return $irregular[$word];
    }

    // Quy tắc chung
    if (preg_match('/ies$/', $word)) {
      return preg_replace('/ies$/', 'y', $word); // activities -> activity
    } elseif (preg_match('/ves$/', $word)) {
      return preg_replace('/ves$/', 'f', $word); // leaves -> leaf
    } elseif (preg_match('/oes$/', $word)) {
      return preg_replace('/oes$/', 'o', $word); // heroes -> hero
    } elseif (preg_match('/s$/', $word) && !preg_match('/ss$/', $word)) {
      return preg_replace('/s$/', '', $word); // hotels -> hotel
    }

    return $word;
  }
}

if (!function_exists('slugToTitle')) {
  function slugToTitle(string $slug): string
  {
    // Thay dấu gạch ngang hoặc gạch dưới bằng khoảng trắng
    $title = str_replace(['-', '_'], ' ', $slug);
    // Viết hoa chữ cái đầu mỗi từ
    return ucwords($title);
  }
}

if (!function_exists('dotBackground')) {
  function dotBackground($item)
  {
    if (empty($item['source'])) {
      $sourceModel = new SourceModel();
      $item['source'] = $sourceModel->find($item['source_id'] ?? 0);
    }

    switch ($item['source']['name'] ?? '') {
      case 'Agoda':
        return 'bg-success';

      case 'Klook':
        return 'bg-warning';

      case 'Kkday':
        return 'bg-info';

      case 'Carla':
        return 'bg-danger';

      default:
        return 'bg-primary';
    }
  }
}

if (!function_exists('sourceLanguages')) {
  function sourceLanguages($languages, $source = null)
  {
    switch ($source['name'] ?? '') {
      case 'Agoda':
        return array_filter($languages, fn($lang) => !empty($lang['a_id']));

      case 'Klook':
        return array_filter($languages, fn($lang) => !empty($lang['k_code']));

      case 'Kkday':
        return array_filter($languages, fn($lang) => !empty($lang['kd_code']));

      case 'Carla':
        return array_filter($languages, fn($lang) => $lang['locale'] == 'en-US');

      default:
        return $languages;
    }
  }
}
