<?php

namespace App\Libraries;

class Youtube extends BaseAPI
{
  protected $apiKey;

  public function __construct($apiKey)
  {
    parent::__construct();
    $this->apiKey = $apiKey;
  }

  public function videoInfo($id, $lang = '')
  {
    $url = "https://www.googleapis.com/youtube/v3/videos?id={$id}&key={$this->apiKey}&part=snippet&hl={$lang}";

    try {
      $response = $this->client->get($url, [
        'decode_content' => true,
      ]);

      $json = $this->handleResponse($response);
    } catch (\Throwable $e) {
      log_message('error', '[Youtube] API request failed: ' . $e->getMessage());
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    return [
      'success' => true,
      'data' => $json['items'][0] ?? null
    ];
  }
}
