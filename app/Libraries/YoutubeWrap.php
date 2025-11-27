<?php

namespace App\Libraries;

class YoutubeWrap
{
  public static function getVideoInfo($id, $lang = '')
  {
    $apiKeys = json_decode(getenv('YTB_KEYS'), true);
    $key_index = rand(0, count($apiKeys) - 1);
    $retry_remain = count($apiKeys);

    while (true) {
      $key = $apiKeys[$key_index];
      $youtube = new Youtube($key);
      $json = $youtube->videoInfo($id, $lang);

      if (isset($json['success']) && $json['success'] == true) {
        return $json['data'];
      }

      $retry_remain--;
      if ($retry_remain <= 0) {
        break;
      }

      $key_index++;
      if ($key_index >= count($apiKeys)) {
        $key_index = 0;
      }
    }

    return null;
  }
}
