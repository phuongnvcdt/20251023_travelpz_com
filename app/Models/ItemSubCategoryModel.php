<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemSubCategoryModel extends Model
{
	protected $table = 'item_sub_categories';
	protected $primaryKey = 'id';
	protected $allowedFields = ['item_id', 'sub_category_id'];
	public $timestamps = true;
}
