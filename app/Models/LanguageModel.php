<?php

namespace App\Models;

use CodeIgniter\Model;

class LanguageModel extends Model
{
    protected $table = 'languages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'active', 'code', 'locale', 'a_id', 'k_code', 'kd_code'];
    protected $useTimestamps = true;
        
    public function getActiveList()
    {
        return $this->where('active', 1)
        ->orderBy('code', 'ASC')
        ->findAll();
    }
}
