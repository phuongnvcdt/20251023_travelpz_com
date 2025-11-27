<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationTranslationModel extends Model
{
	protected $table = 'location_translations';
	protected $primaryKey = 'id';
	protected $allowedFields = ['location_id', 'language_id', 'name'];
	protected $useTimestamps = true;

	public function getTransName($location, $lang)
	{
		if ($lang['locale'] != config('App')->defaultLocale) {
			return $this->select('name')
				->where('location_id', $location['id'])
				->where('language_id', $lang['id'])
				->get()
				->getRow('name');
		}
	}

	public function updateTransName($location, $lang, $trans_name)
	{
		if ($lang['locale'] != config('App')->defaultLocale) {
			$existing = $this->where('location_id', $location['id'])
				->where('language_id', $lang['id'])
				->first();

			$data = [
				'location_id' => $location['id'],
				'language_id' => $lang['id'],
				'name'        => $trans_name
			];

			if ($existing) {
				$data['id'] = $existing['id'];
			}

			return $this->save($data);
		}
	}
}
