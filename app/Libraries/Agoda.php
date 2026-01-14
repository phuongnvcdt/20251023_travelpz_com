<?php

namespace App\Libraries;

use DateTime;

class Agoda extends BaseAPI
{
  const AFFILIATE_ID = 1953113;

  protected $ag_platform_id;

  public function __construct($ag_platform_id = 1)
  {
    parent::__construct();
    $this->ag_platform_id = $ag_platform_id;
  }

  public static function affiliateLink($category, $locale, $id, $slug)
  {
    switch ($category) {
      case 'Hotels':
        return "https://www.agoda.com/{$locale}/partners/partnersearch.aspx?cid=" . self::AFFILIATE_ID . "&hid={$id}&hotel={$slug}&pcs=1&partner=travelpz";
      case 'Activities':
        return "https://www.agoda.com/{$locale}/activities/detail?cid=" . self::AFFILIATE_ID . "&activityId={$id}&activity={$slug}&partner=travelpz";
    }

    return 'https://www.agoda.com/affiliate?cid=' . self::AFFILIATE_ID;
  }

  public function getHotelDetail($id, $langId)
  {
    $now = new DateTime();
    $now->modify('+30 days');
    $nowStr = $now->format('Y-m-d');
    $url = "https://www.agoda.com/api/cronos/property/BelowFoldParams/GetSecondaryData?adults=2&rooms=1&checkIn={$nowStr}&los=1&hotel_id={$id}";

    $headers = [
      'ag-language-id' => $langId
    ];

    try {
      $response = $this->client->get($url, [
        'headers' => array_merge($this->defaultHeaders(), $headers),
        'decode_content' => true,
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Agoda] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $defaultSearchURL = $json['searchbox']['config']['defaultSearchURL'] ?? '';
    if (empty($defaultSearchURL)) {
      return [
        'success' => true,
        'data' => null
      ];
    }

    $countryId = $json['hotelInfo']['address']['countryId'] ?? null;
    if ($countryId == 132 || $countryId == 169) { //Hong Kong & Macau
      $country = [
        'id' => $countryId,
        'name' => $json['hotelInfo']['address']['cityName'] ?? null
      ];
      $city = null;
    } else {
      $country = [
        'id' => $countryId,
        'name' => $json['hotelInfo']['address']['countryName'] ?? null
      ];
      $city = [
        'id' => $json['hotelInfo']['address']['cityId'] ?? null,
        'name' => $json['hotelInfo']['address']['cityName'] ?? null,
      ];
    }

    $data = [
      'en_name' => $json['hotelInfo']['englishName'] ?? null,
      'trans_name' => $json['hotelInfo']['name'] ?? null,
      'star_rating' => $json['hotelInfo']['starRating']['value'] ?? null,
      'address' => [
        'country' => $country,
        'city' => $city,
        'area' => $json['hotelInfo']['address']['areaName'] ?? null,
        'address' => $json['hotelInfo']['address']['address'] ?? null,
        'post_code' => $json['hotelInfo']['address']['postalCode'] ?? null,
        'full' => $json['hotelInfo']['address']['full'] ?? null
      ],
      'description' => $json['aboutHotel']['hotelDesc']['overview'] ?? null,
      'notes' => $json['aboutHotel']['importantNotes'] ?? [],
      'features' => array_map(fn($featureGroup) => [
        'title' => $featureGroup['name'] ?? '',
        'list' => array_map(fn($ft) => $ft['name'] ?? null, $featureGroup['feature'] ?? [])
      ], $json['aboutHotel']['featureGroups'] ?? []),
      'love_features' => [
        'title' => $json['featuresYouLove']['title'] ?? null,
        'list' => array_map(fn($ft) => $ft['text'] ?? null, $json['featuresYouLove']['features'] ?? []),
      ],
      'images' => array_map(fn($image) => [
        'title' => $image['title'] ?? null,
        'url' => empty($image['location']) ? '' : 'https:' . $image['location']
      ], $json['mosaicInitData']['images'] ?? []),
      'map' => [
        'url' => empty($json['mapParams']['staticMapUrl']) ? null : 'https:' . $json['mapParams']['staticMapUrl'],
        'lat' => $json['mapParams']['latlng'][0],
        'long' => $json['mapParams']['latlng'][1],
      ],
      'reviews' => [
        'score' => (float)($json['reviews']['score'] ?? 0) / 2,
        'count' => $json['reviews']['reviewsCount'] ?? 0,
        'text' => $json['reviews']['scoreText'] ?? null
      ]
    ];

    return [
      'success' => true,
      'data' => $data
    ];
  }

  public function getActivityDetail($id, $langId = 1)
  {
    $url = 'https://www.agoda.com/api/activities/graphql';

    $headers = [
      'ag-language-id' => $langId,
      'ag-cid' => self::AFFILIATE_ID,
      'ag-platform-id' => $this->ag_platform_id
    ];

    $body = [
      'variables' => [
        'DetailsRequest' => [
          'context' => [
            'currency' => 'USD',
            'experimentInfo' => (object)[]
          ],
          'contentRequest' => [
            'imageRequest' => [
              'count' => 100,
              'width' => 1280,
              'height' => 720
            ]
          ],
          'detailsRequest' => [
            'activityId' => (int) $id
          ]
        ]
      ],
      'query' => 'query details ($DetailsRequest: DetailsRequest!) { details (DetailsRequest: $DetailsRequest) { isSuccess, result { isCompleted, activity { masterActivityId, masterSupplierId, activityRepresentativeInfo { activityId, activityToken, pricingSummary { pricing { currency, display { perBook { displayType, quantity, total { exclusive { chargeTotal, crossedOut }, allInclusive { chargeTotal, crossedOut } }, loyaltyOffers { loyaltyOfferType, loyaltyToken, earnOffer { points }, burnOffer { points, userPayableAmount, itemPriceInPoints } }, promocodeItems { promotionCode, discountedValue, isApplied, isAllowPricePeek, isAutoApply }, badges { activityHighlights { badgeType, enrichmentData }, deals { badgeType, enrichmentData }, bookingInfo { badgeType, enrichmentData }, keyAttributes { badgeType, enrichmentData }, promocodes { badgeType, enrichmentData } } }, perPax { displayType, quantity, total { exclusive { chargeTotal, crossedOut }, allInclusive { chargeTotal, crossedOut } }, loyaltyOffers { loyaltyOfferType, loyaltyToken, earnOffer { points }, burnOffer { points, userPayableAmount, itemPriceInPoints } } } } } }, supplierActivityCode, cancellationPolicy { cancellationType, policies { hoursFrom, hoursUntil, penaltyCode } }, confirmMinutes }, content { activity { title, categories, description, duration { minutes, minutesUntil, durationType }, location { city { id, name }, addressLine, geo { lat, long }, country { id, name }, postalCode } }, detail { genericSection { title, sectionType, content { title, description, media { description, source, mediaType } } }, locations { id, name, address { addressLine } }, inclusions { id, benefitItems { name, description } }, exclusions { id, benefitItems { name, description } }, additionalDetails { additionalType, description }, itineraries { description { section { title, descriptions } }, id, itineraryType, itineraryRoutes { routes { duration { minutes, durationType, minutesUntil }, description { section { title, descriptions } }, stops { locationRefId, description }, pointsOfInterest { locationRefId, description } }, description { section { title, descriptions } } }, itineraryDays { description { section { title, descriptions } }, dayNumber, itineraries { duration { minutes, durationType, minutesUntil }, description { section { title, descriptions } }, admissionIncluded, pointsOfInterest { description, locationRefId }, media { mediaType, source, description } } }, duration { minutes, durationType, minutesUntil } }, offerDetails { title, description, offerDetailRef, inclusionRefId, exclusionRefId }, offerGroupDetails { offerGroupDetailsReference, offerGroupTitle }, logistics { logisticType, startEnd { endLocation { description, locationRefId }, startLocation { description, locationRefId } } }, ticketing { ticketTypes }, aboutActivity }, images { description, imageSize { width, height }, imageType, url }, reviewSummary { averageScore, totalCount }, contentLocale, supportedLanguages { locale }, badges { activityHighlights { badgeType, enrichmentData }, deals { badgeType, enrichmentData }, bookingInfo { badgeType, enrichmentData }, keyAttributes { badgeType, enrichmentData } }, activityHighlights { code } } }, fieldContentTypes { fieldType, contentType } }, errors { errorCode, subErrorCode, message } } } '
    ];

    try {
      $response = $this->client->post($url, [
        'headers' => array_merge($this->defaultHeaders(), $headers),
        'decode_content' => true,
        'json' => $body
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Agoda] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $activity = $json['data']['details']['result']['activity'] ?? null;
    if (!isset($activity)) {
      return [
        'success' => true,
        'data' => null
      ];
    }

    $countryId = $activity['content']['activity']['location']['country']['id'] ?? null;
    if ($countryId == 132 || $countryId == 169) { //Hong Kong & Macau
      $country = [
        'id' => $countryId,
        'name' => $activity['content']['activity']['location']['city']['name'] ?? null
      ];
      $city = null;
    } else {
      $country = [
        'id' => $countryId,
        'name' => $activity['content']['activity']['location']['country']['name'] ?? null
      ];
      $city = [
        'id' => $activity['content']['activity']['location']['city']['id'] ?? null,
        'name' => $activity['content']['activity']['location']['city']['name'] ?? null,
      ];
    }

    $addresses = [
      $activity['content']['activity']['location']['addressLine'] ?? '',
      $activity['content']['activity']['location']['city']['name'] ?? '',
      $activity['content']['activity']['location']['country']['name'] ?? '',
      $activity['content']['activity']['location']['postalCode'] ?? ''
    ];
    $addresses = array_filter($addresses, fn($adr) => !empty($adr));

    $title = $activity['content']['activity']['title'] ?? null;
    $data = [
      'en_name' => $langId == 1 ? $title : null,
      'trans_name' => $title,
      'categories' => $activity['content']['activity']['categories'] ?? [],
      'address' => [
        'country' => $country,
        'city' => $city,
        'full' => trim(implode(', ', $addresses))
      ],
      'description' => nl2br($activity['content']['activity']['description'] ?? null),
      'images' => array_map(fn($image) => [
        'title' => $image['description'] ?? null,
        'url' => $image['url'] ?? ''
      ], $activity['content']['images'] ?? []),
      'map' => [
        'lat' => $activity['content']['activity']['location']['geo']['lat'] ?? null,
        'long' => $activity['content']['activity']['location']['geo']['long'] ?? null,
      ],
      'reviews' => [
        'score' => $activity['content']['reviewSummary']['averageScore'] ?? 5,
        'count' => $activity['content']['reviewSummary']['totalCount'] ?? rand(1000, 9999)
      ]
    ];

    return [
      'success' => true,
      'data' => $data
    ];
  }

  public function getLocationDetail($id, $type, $langId)
  {
    $url = "https://www.agoda.com/api/cronos/geo/AboutPage?pageTypeId={$type}&objectId={$id}";

    $headers = [
      'ag-language-id' => $langId
    ];

    try {
      $response = $this->client->get($url, [
        'headers' => array_merge($this->defaultHeaders(), $headers),
        'decode_content' => true,
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Agoda] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $data = [
      'title' => $json['title'] ?? '',
      'description' => $json['about'] ?? '',
    ];

    return [
      'success' => true,
      'data' => $data
    ];
  }
}
