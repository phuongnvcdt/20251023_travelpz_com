<?php

namespace App\Libraries;

use DateTime;

class Booking extends BaseAPI
{

  public function getHotelReviews($id, $skip = 0, $limit = 20, $sort = 'SCORE_DESC', $filter = null)
  {
    $url = 'https://www.booking.com/dml/graphql';

    $body = [
      'operationName' => 'ReviewList',
      'variables' => [
        'shouldShowReviewListPhotoAltText' => true,
        'input' => [
          'hotelId' => $id,
          'ufi' => 1,
          'hotelCountryCode' => '',
          'sorter' => $sort,
          'filters' => $filter ?? (object) [],
          'skip' => $skip,
          'limit' => $limit,
          'searchFeatures' => [
            'destId' => $id,
            'destType' => 'HOTEL'
          ]
        ]
      ],
      'query' => "query ReviewList(\$input: ReviewListFrontendInput!, \$shouldShowReviewListPhotoAltText: Boolean = false) {\n  reviewListFrontend(input: \$input) {\n    ... on ReviewListFrontendResult {\n      ratingScores {\n        name\n        translation\n        value\n        ufiScoresAverage {\n          ufiScoreLowerBound\n          ufiScoreHigherBound\n          __typename\n        }\n        __typename\n      }\n      topicFilters {\n        id\n        name\n        isSelected\n        translation {\n          id\n          name\n          __typename\n        }\n        __typename\n      }\n      reviewScoreFilter {\n        name\n        value\n        count\n        __typename\n      }\n      languageFilter {\n        name\n        value\n        count\n        countryFlag\n        __typename\n      }\n      timeOfYearFilter {\n        name\n        value\n        count\n        __typename\n      }\n      customerTypeFilter {\n        count\n        name\n        value\n        __typename\n      }\n      reviewCard {\n        reviewUrl\n        guestDetails {\n          username\n          avatarUrl\n          countryCode\n          countryName\n          avatarColor\n          showCountryFlag\n          anonymous\n          guestTypeTranslation\n          __typename\n        }\n        bookingDetails {\n          customerType\n          roomId\n          roomType {\n            id\n            name\n            __typename\n          }\n          checkoutDate\n          checkinDate\n          numNights\n          stayStatus\n          __typename\n        }\n        reviewedDate\n        isTranslatable\n        helpfulVotesCount\n        reviewScore\n        textDetails {\n          title\n          positiveText\n          negativeText\n          textTrivialFlag\n          lang\n          __typename\n        }\n        isApproved\n        partnerReply {\n          reply\n          __typename\n        }\n        positiveHighlights {\n          start\n          end\n          __typename\n        }\n        negativeHighlights {\n          start\n          end\n          __typename\n        }\n        editUrl\n        photos {\n          id\n          urls {\n            size\n            url\n            __typename\n          }\n          kind\n          mlTagHighestProbability @include(if: \$shouldShowReviewListPhotoAltText)\n          __typename\n        }\n        __typename\n      }\n      reviewsCount\n      sorters {\n        name\n        value\n        __typename\n      }\n      __typename\n    }\n    ... on ReviewsFrontendError {\n      statusCode\n      message\n      __typename\n    }\n    __typename\n  }\n}\n"
    ];

    try {
      $response = $this->client->post($url, [
        'decode_content' => true,
        'json' => $body
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Booking] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $reviewCards = $json['data']['reviewListFrontend']['reviewCard'] ?? [];
    $reviews = [];
    foreach ($reviewCards as $reviewCard) {
      $username = $reviewCard['guestDetails']['username'] ?? '';
      $firstChar = strtolower(substr(trim($username), 0, 1));
      if (!preg_match('/[a-z]/', $firstChar)) {
        $firstChar = 'a';
      }
      $avatar = "https://xx.bstatic.com/static/img/review/avatars/ava-{$firstChar}.png";

      $lang = $reviewCard['textDetails']['lang'] ?? '';
      if ($lang != 'en' && $lang != 'xu') {
        $textDetailsTrans = $this->translateReview($id, $reviewCard['reviewUrl']);
      }

      if (!isset($textDetailsTrans)) {
        $textDetailsTrans = $reviewCard['textDetails'] ?? (object)[];
      }

      $review = [
        'user' => [
          'name' => $username,
          'avatar' => $avatar,
        ],
        'rating' => (float)($reviewCard['reviewScore'] ?? 0) / 2,
        'title' => $textDetailsTrans['title'] ?? null,
        'time' => (new DateTime())->setTimestamp($reviewCard['reviewedDate'] ?? time()),
        'org_content' => $reviewCard['textDetails']['positiveText'] ?? null,
        'trans_content' => $textDetailsTrans['positiveText'] ?? null,
        'product_name' => $reviewCard['bookingDetails']['roomType']['name'] ?? null,
        'images' => array_map(fn($img) => $img['urls'][0]['url'] ?? null, $review['photos'] ?? []),
      ];
      array_push($reviews, $review);
    }

    return [
      'success' => true,
      'data' => [
        'count' => $json['data']['reviewListFrontend']['reviewsCount'] ?? 0,
        'reviews' => $reviews
      ]
    ];
  }

  public function translateReview($hotelId, $reviewUrl, $lang = 'en')
  {
    $url = "https://www.booking.com/dml/graphql?lang={$lang}";

    $data = [
      'operationName' => 'TranslateReview',
      'variables' => [
        'input' => [
          'hotelId' => $hotelId ?? "",
          'reviewUrl' => $reviewUrl ?? "",
        ]
      ],
      'query' => "query TranslateReview(\$input: TranslateReviewInput) {\n  translateReview(input: \$input) {\n    ... on TranslateReviewResult {\n      translatedReview {\n        reviewUrl\n        title\n        positiveText\n        negativeText\n        translationProvider\n        __typename\n      }\n      __typename\n    }\n    ... on TranslateReviewError {\n      statusCode\n      message\n      __typename\n    }\n    __typename\n  }\n}\n"
    ];

    try {
      $response = $this->client->post($url, [
        'decode_content' => true,
        'json' => $data
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Booking] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    return [
      'success' => true,
      'data' => $json['data']['translateReview']['translatedReview'] ?? null
    ];
  }
}
