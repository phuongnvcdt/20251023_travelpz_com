<?php

namespace App\Models;

use CodeIgniter\Model;

class SourceModel extends Model
{
    protected $table = 'sources';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug'];
    protected $useTimestamps = true;
}
