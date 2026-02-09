<?php

namespace App\Controllers\Api\Booking\V1;

use CodeIgniter\Controller;
use App\Libraries\Booking;

class BookingController extends Controller
{
  public function getReviews()
  {
    $id = $this->request->getPost('id');
    $page = $this->request->getPost('page') ?? 1;
    $size = $this->request->getPost('size') ?? 10;
    $sort = $this->request->getPost('sort') ?? 'SCORE_DESC';
    $filter = $this->request->getPost('filter') ?? null;

    if (!$id) {
      return $this->response->setStatusCode(400)
        ->setJSON(['error' => 'Invalid parameters']);
    }

    $booking = new Booking();
    return $this->response->setJSON($booking->getHotelReviews((int)$id, ((int)$page - 1) * (int)$size, (int)$size, $sort, $filter));
  }
}
