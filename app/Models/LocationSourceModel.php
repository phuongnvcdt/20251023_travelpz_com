<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationSourceModel extends Model
{
	protected $table = 'location_sources';
	protected $primaryKey = 'id';
	protected $allowedFields = ['location_id', 'source_id', 'location_source_id'];
	public $timestamps = true;
}
