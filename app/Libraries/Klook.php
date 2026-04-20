<?php

namespace App\Libraries;

use DateTime;

class Klook extends BaseAPI
{
  const AFFILIATE_ID = 103647;
  const HOTELS_ADID = 1156497;
  const ACTIVITIES_ADID = 1156498;
  const SEARCH_ADID = 1156499;
  const WIDGET_ADID = 1252423;

  const SOCIAL_AID = 82772;
  const FB_ADID = 1156556;
  const DM_ADID = 1156557;
  const LK_ADID = 1156558;
  const THR_ADID = 1156560;
  const YTB_HOTELS_ADID = 1156547;
  const YTB_ACTIVITIES_ADID = 1156552;

  public static function affiliateLink($category, $lang, $id, $slug, $s = null)
  {
    switch ($s) {
      case 'fb':
        $aid = self::SOCIAL_AID;
        $aff_adid = self::FB_ADID;
        break;

      case 'dm':
        $aid = self::SOCIAL_AID;
        $aff_adid = self::DM_ADID;
        break;

      case 'lk':
        $aid = self::SOCIAL_AID;
        $aff_adid = self::LK_ADID;
        break;

      case 'thr':
        $aid = self::SOCIAL_AID;
        $aff_adid = self::THR_ADID;
        break;

      case 'ytb':
        $aid = self::SOCIAL_AID;
        if ($category == 'Hotels') {
          $aff_adid = self::YTB_HOTELS_ADID;
        } else if ($category == 'Activities') {
          $aff_adid = self::YTB_ACTIVITIES_ADID;
        } else {
          $aff_adid = '';
        }
        break;

      default:
        $aid = self::AFFILIATE_ID;
        if ($category == 'Hotels') {
          $aff_adid = self::HOTELS_ADID;
        } else if ($category == 'Activities') {
          $aff_adid = self::ACTIVITIES_ADID;
        } else {
          $aff_adid = '';
        }
        break;
    }

    return "https://www.klook.com/{$lang}/hotels/detail/{$id}-{$slug}/?aid=" . $aid . '&aff_adid=' . $aff_adid . '&partner=travelpz';
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
      log_message('error', '[Klook] API request failed (' . $url . '): ' . $e->getMessage());
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
