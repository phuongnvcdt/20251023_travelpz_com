<?php

namespace App\Libraries;

use DateTime;

class Klook extends BaseAPI
{
  const AFFILIATE_ID = 103647;
  const HOTELS_ADID = 1156497;
  const ACTIVITIES_ADID = 1156498;
  const SEARCH_ADID = 1156499;

  public static function affiliateLink($category, $lang, $id, $slug)
  {
    switch ($category) {
      case 'Hotels':
        return "https://www.klook.com/{$lang}/hotels/detail/{$id}-{$slug}/?aid=" . self::AFFILIATE_ID . '&aff_adid=' . self::HOTELS_ADID . '&partner=travelpz';
      case 'Activities':
        return "https://www.klook.com/{$lang}/activity/{$id}-{$slug}/?aid=" . self::AFFILIATE_ID . '&aff_adid=' . self::ACTIVITIES_ADID . '&partner=travelpz';
    }

    return 'https://www.klook.com/?aid=' . self::AFFILIATE_ID;
  }

  public function getActivityReviews($id, $locale, $page = 1, $curency = 'USD', $sort = 'sort_score_high_to_low', $filter = '')
  {
    if (!isset($id) || !isset($locale) || !isset($page) || !isset($curency) || !isset($sort) || !isset($filter)) {      
      return [
        'success' => false,
        'message' => 'Invalid params'
      ];
    }

    $url = "https://www.klook.com/v1/platformbffsrv/reviewcomponent/service/get_review_list?k_lang={$locale}&k_currency={$curency}&page={$page}&sort_key={$sort}&filter_key={$filter}&aggregate_id={$id}&template_id=1";

    $headers = [
      'accept-language' => $locale
    ];

    try {
      $response = $this->client->get($url, [
        'headers' => array_merge($this->defaultHeaders(), $headers),
        'decode_content' => true
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Klook] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $reviews = $json['result']['review_list'] ?? null;
    if (!isset($reviews)) {
      return [
        'success' => true,
        'data' => null
      ];
    }

    $data = array_map(fn($review) => [
      'user' => [
        'name' => $review['user_info']['user_name'] ?? null,
        'avatar' => $review['user_info']['user_avatar'] ?? null,
      ],
      'rating' => (float)($review['user_info']['user_rating'] ?? 5),
      'title' => $review['user_info']['rating_desc'] ?? null,
      'time' => new DateTime($review['user_info']['rating_time'] ?? ''),
      'org_content' => $review['review_content'] ?? null,
      'trans_content' => $review['translate_content'] ?? null,
      'product_name' => $review['product_info']['product_name'] ?? null,
      'images' => array_map(fn($img) => $img['url'] ?? null, $review['review_image_list'] ?? []),
    ], $reviews);

    return [
      'success' => true,
      'data' => $data
    ];
  }
}
