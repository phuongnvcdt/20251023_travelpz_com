<?php

namespace App\Libraries;

use DateTime;

class Carla
{
  public static function isValid($code)
  {
    // Tối thiểu phải có 11 ký tự (10 cho PlusCode, 1+ cho ID)
    if (strlen($code) < 11) {
      return false;
    }

    // Phần PlusCode gốc (10 ký tự đầu)
    $plusPart = substr($code, 0, 10);
    // Phần ID (sau 10 ký tự)
    $idPart = substr($code, 10);

    // Kiểm tra phần PlusCode chỉ chứa ký tự hợp lệ trong bảng mã
    $validChars = '23456789CFGHJMPQRVWX';
    if (strspn($plusPart, $validChars) !== strlen($plusPart)) {
      return false;
    }

    // Kiểm tra phần ID là số nguyên dương
    if (!preg_match('/^[0-9]+$/', $idPart)) {
      return false;
    }

    return true;
  }

  public static function encode($carlaId)
  {
    $lat = $carlaId['lat'] ?? 0;
    $lng = $carlaId['lng'] ?? 0;
    $id = $carlaId['id'] ?? 0;

    // Encode chuẩn bằng thư viện
    $code = PlusCode::encode($lat, $lng, 10);

    // Loại bỏ dấu '+'
    $code = str_replace('+', '', $code);

    // Nối ID số vào cuối nếu có
    if (!is_null($id)) {
      $code .= $id;
    }

    return strtoupper($code);
  }

  public static function decode($code)
  {
    // 10 ký tự đầu tiên là phần mã gốc
    $plusCodePart = substr($code, 0, 10);
    // Thêm lại dấu + tại vị trí thứ 8
    $plusCode = substr($plusCodePart, 0, 8) . '+' . substr($plusCodePart, 8);

    // Phần còn lại là id số
    $idPart = substr($code, 10);
    $id = is_numeric($idPart) ? (int)$idPart : null;

    $decoded = PlusCode::decode($plusCode);

    return [
      'lat' => $decoded['latitudeCenter'] ?? null,
      'lng' => $decoded['longitudeCenter'] ?? null,
      'id'  => $id,
      'code' => $plusCode
    ];
  }

  public static function affiliateLink($lat, $lng)
  {
    if (empty($lat) || empty($lng)) {
      return 'https://www.kqzyfj.com/6r105js0ys-FHGGIKPOPKFHNGPKJJO';
    }

    $now = new DateTime();
    $checkIn = Carla::addMonths($now, 1);
    $checkInStr = Carla::formatDate($checkIn) . "1200";

    $checkOut = Carla::addDays($checkIn, 1);
    $checkOutStr = Carla::formatDate($checkOut) . "1200";

    // Tạo URL khách sạn
    $hotelUrl = "https://rentcarla.com/hotel-results/{$lat}/{$lng}/{$checkInStr}/{$checkOutStr}";

    // Gắn link affiliate
    return "https://www.kqzyfj.com/6r105js0ys-FHGGIKPOPKFHNGPKJJO?url=" . urlencode($hotelUrl);
  }

  public static function rentCarLink()
  {
    return 'https://www.dpbolvw.net/bd111ar-xrzEGFFHJONOJEGMFMKGNK';
  }

  private static function addMonths($date, $months)
  {
    $newDate = clone $date;
    $newDate->modify("+{$months} month");
    return $newDate;
  }

  // Hàm thêm số ngày
  private static function addDays($date, $days)
  {
    $newDate = clone $date;
    $newDate->modify("+{$days} day");
    return $newDate;
  }

  // Hàm định dạng ngày theo dạng yyyyMMdd
  private static function formatDate($date)
  {
    return $date->format('Ymd');
  }
}
