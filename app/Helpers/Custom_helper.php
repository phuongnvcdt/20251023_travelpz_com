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
    if ($agent && method_exists($agent, 'isRobot') && $agent->isRobot()) {
      return true;
    }

    $ua = $agent ? $agent->getAgentString() : ($_SERVER['HTTP_USER_AGENT'] ?? '');

    if (!$ua) {
      return true; // không có UA -> bot
    }

    $ua = trim($ua);

    // 0. HTTP header fingerprint — browser thật luôn gửi đủ các header này
    if (!$request->getHeaderLine('Accept-Language')) {
      return true;
    }

    if (!$request->getHeaderLine('Accept-Encoding')) {
      return true;
    }

    // $accept = $request->getHeaderLine('Accept');
    // if ($accept && !str_contains($accept, 'text/html') && !str_contains($accept, '*/*')) {
    //   return true;
    // }

    // 1. Keyword bot
    $extraBots = [
      'bot',
      'tool',
      'spider',
      'crawler',
      'crawl',
      'GoogleOther',
      'YouTube',
      'Pinterest',
      'Slurp',
      'YandexImages',
      'Sogou',
      'ia_archiver',
      'facebookexternalhit',
      'meta-externalagent',
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
      'curl',
      'wget',
      'python',
      'httpclient',
      'scrapy',
      'selenium',
      'playwright',
      'phantom',
      // Social media link preview
      'WhatsApp',
      'Telegram',
      'Discordbot',
      'Slackbot',
      'Twitterbot',
      'LinkedInBot',
      // SEO crawlers
      'AhrefsBot',
      'SemrushBot',
      'MJ12bot',
      'DotBot',
      'Baiduspider',
      'applebot',
      // Monitoring
      'UptimeRobot',
      'StatusCake',
      'Site24x7',
      // Office/custom tools
      'BKOffice',
    ];

    $pattern = '/' . implode('|', array_map('preg_quote', $extraBots)) . '/i';
    if (preg_match($pattern, $ua)) {
      return true;
    }

    // 2. UA bất thường
    $suspiciousPatterns = [
      '/windows 9[58]/i',           // Windows 98/95 — discontinued
      '/windows nt [1-5]\./i',     // quá cũ
      '/windows nt 1[1-9]\./i',    // NT 11+ không tồn tại (Win11 vẫn là NT 10.0)
      '/iphone os [1-7]_/i',       // quá cũ
      '/ppc mac os/i',             // cực hiếm

      '/firefox\/[1-5]\d\./i',      // Firefox < 60 (2019): quá cũ
      '/firefox\/\d{3,}/i',        // version vô lý

      '/gecko\/\d{4}-\d{2}-\d{2}/i', // sai format

      // Chrome/Edge version thiếu octet (phải là x.x.x.x)
      '/(?:chrome|edg(?:e|ios)?)\/\d+\.\d+\.\d+(?!\.\d)/i',

      // Android device model là 1 ký tự (K, X, ...) → bot fingerprint
      '/Android \d+; [A-Z]\)/i',

      // Opera/Presto — engine chết từ 2013
      '/Presto\/\d/i',

      // WebKit quá cũ (≤ 534 = trước 2011)
      '/AppleWebKit\/([1-4]\d{2}|5[0-2]\d|53[0-4])\./i',

      // Android 4.x trở xuống (2013 trở về trước)
      '/Android [1-4]\./i',
    ];

    foreach ($suspiciousPatterns as $regex) {
      if (preg_match($regex, $ua)) {
        return true;
      }
    }

    // 3. UA quá ngắn (bot thường rút gọn)
    if (strlen($ua) < 20) {
      return true;
    }

    // 4. Không có browser phổ biến → nghi bot
    if (!preg_match('/(chrome|safari|firefox|edg|opera)/i', $ua)) {
      return true;
    }

    // 5. Logic consistency (GIẢM false positive)
    $hasChrome  = stripos($ua, 'Chrome') !== false;
    $hasSafari  = stripos($ua, 'Safari') !== false;
    $hasFirefox = stripos($ua, 'Firefox') !== false;

    // Chrome mà không có Safari → đáng nghi
    if ($hasChrome && !$hasSafari) {
      return true;
    }

    // Safari thật (không phải Chrome) phải có Version/
    if ($hasSafari && !$hasChrome && stripos($ua, 'Version/') === false) {
      return true;
    }

    // Firefox mà không có Gecko → sai cấu trúc
    if ($hasFirefox && stripos($ua, 'Gecko/') === false) {
      return true;
    }

    // 6. Detect Chrome version bất thường
    if (preg_match('/chrome\/(\d{2,3})/i', $ua, $m)) {
      $version = (int)$m[1];

      // Chrome < 90 (Apr 2021): quá cũ, dùng năm 2026 là bất thường
      if ($version < 90) {
        return true;
      }

      // Chrome version quá cao so với hiện tại (~12 version/năm, buffer 2 năm)
      // offset 24146 = calibrated: Chrome 146 tháng 4/2026; buffer +24 (~2 năm)
      $maxChrome = (int) date('Y') * 12 + (int) date('n') - 24146;
      if ($version > $maxChrome) {
        return true;
      }
    }

    // 7. Datacenter / known bot IP
    if (is_datacenter_ip($request->getIPAddress())) {
      return true;
    }

    return false;
  }
}

if (!function_exists('is_datacenter_ip')) {
  function is_datacenter_ip(string $ip): bool
  {
    // IPv6: check prefix
    if (str_contains($ip, ':')) {
      foreach (
        [
          '2a03:2880:', // Facebook/Meta crawlers
          '2001:4860:', // Google
          '2607:f8b0:', // Google
          '2404:6800:', // Google APAC
          '2a00:1450:', // Google EU
        ] as $prefix
      ) {
        if (stripos($ip, $prefix) === 0) {
          return true;
        }
      }
      return false;
    }

    // IPv4: CIDR check
    $long = ip2long($ip);
    if ($long === false) return false;

    foreach (
      [
        // Googlebot
        '66.249.64.0/19',
        '66.249.96.0/19',
        '64.233.160.0/19',
        '74.125.0.0/16',
        // Facebook/Meta
        '31.13.24.0/21',
        '31.13.64.0/18',
        '66.220.144.0/20',
        '69.63.176.0/20',
        '69.171.224.0/19',
        '173.252.64.0/18',
        '204.15.20.0/22',
        // AWS
        '3.0.0.0/8',
        '16.0.0.0/8',
        '18.128.0.0/9',
        '34.192.0.0/10',
        '35.80.0.0/12',
        '35.160.0.0/13',
        '44.192.0.0/10',
        '52.0.0.0/8',
        '54.0.0.0/8',
        // Limelight Networks / Edgio CDN
        '216.229.64.0/18',
        // QuadraNet / ColoCrossing
        '38.87.64.0/18',
        // Tencent Cloud
        '43.128.0.0/10',
        '81.70.0.0/16',
        '82.156.0.0/15',
        '129.226.0.0/16',
        '140.143.0.0/16',
        '152.136.0.0/16',
        '170.106.0.0/16',
      ] as $cidr
    ) {
      [$subnet, $bits] = explode('/', $cidr);
      $mask = -1 << (32 - (int) $bits);
      if (($long & $mask) === (ip2long($subnet) & $mask)) {
        return true;
      }
    }

    return false;
  }
}

if (!function_exists('is_ip_rate_limited')) {
  function is_ip_rate_limited($request, int $maxPerMinute = 30): bool
  {
    $throttler = \Config\Services::throttler();
    $key = 'rl_' . md5($request->getIPAddress());
    return !$throttler->check($key, $maxPerMinute, MINUTE);
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
