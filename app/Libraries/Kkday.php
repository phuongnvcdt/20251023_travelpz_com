<?php

namespace App\Libraries;

use DateTime;

class Kkday extends BaseAPI
{
  const AFFILIATE_ID = 23173;
  const UD1 = 'tpz';

  public static function affiliateLink($lang, $id, $slug)
  {
    $url = "https://www.kkday.com";
    if (!empty($lang)) {
      $url .= '/' . $lang;
    }

    $url .= '/product/' . $id;
    if (!empty($slug)) {
      $url .= '-' . $slug;
    }

    $url .= '?cid=' . self::AFFILIATE_ID . '&ud1=' . self::UD1;

    return $url;
  }

  public function getItemReviews($id, $market = "", $page = 1, $sort = 'RATING_DESC', $tags = '')
  {
    $url = "https://www.kkday.com/api/_nuxt/cpath/fetch-product-comments-v2?prodId={$id}&page={$page}&sort={$sort}&tags={$tags}";

    if (!empty($market)) {
      $headers = [
        'market' => $market
      ];
    } else {
      $headers = [];
    }

    try {
      $response = $this->client->get($url, [
        'headers' => array_merge($this->defaultHeaders(), $headers),
        'decode_content' => true
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Kkday] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $reviews = $json['data']['comments'] ?? null;
    if (!isset($reviews)) {
      return [
        'success' => true,
        'data' => null
      ];
    }

    $data = array_map(fn($review) => [
      'user' => [
        'name' => $review['author']['name'] ?? null,
        'avatar' => $review['author']['avatar'] ?? null,
      ],
      'rating' => (float)($review['rating'] ?? 5),
      'title' => $review['title']['translated'] ?? null,
      'org_title' => $review['title']['origin'] ?? null,
      'time' => new DateTime($review['postDate'] ?? ''),
      'org_content' => $review['body']['origin'] ?? null,
      'trans_content' => $review['body']['translated'] ?? null,
      'images' => array_map(fn($img) => $img['url'] ?? null, $review['images'] ?? []),
    ], $reviews);

    return [
      'success' => true,
      'data' => $data
    ];
  }
}
