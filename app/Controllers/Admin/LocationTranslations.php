<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LocationTranslations extends BaseController
{
	protected $locationTranslationModel;
	protected $locationModel;
	protected $languageModel;

	public function __construct()
	{
		$this->locationTranslationModel = new \App\Models\LocationTranslationModel();
		$this->locationModel = new \App\Models\LocationModel();
		$this->languageModel = new \App\Models\LanguageModel();
	}

	public function index($locationId)
	{
		$location = $this->locationModel->find($locationId);
		if (!$location) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$location_trans = $this->locationTranslationModel
			->select('location_translations.*, languages.name as language_name')
			->join('languages', 'languages.id = location_translations.language_id')
			->where('location_translations.location_id', $locationId)
			->findAll();

		return view('admin/location_translations/index', [
			'location' => $location,
			'location_trans' => $location_trans
		]);
	}

	public function create($locationId)
	{
		$location = $this->locationModel->find($locationId);
		$languages = $this->languageModel
			->whereNotIn('id', function ($builder) use ($locationId) {
				$builder->select('language_id')
					->from('location_translations')
					->where('location_id', $locationId);
			})
			->findAll();

		return view('admin/location_translations/create', [
			'location' => $location,
			'languages' => $languages
		]);
	}

	public function store($locationId)
	{
		$this->locationTranslationModel->save([
			'location_id'   => $locationId,
			'language_id'   => esc($this->request->getPost('language_id')) ?: null,
			'name'          => esc($this->request->getPost('name')) ?: null
		]);

		return redirect()->to("/admin/locations/$locationId/trans")->with('success', 'Translation added');
	}

	public function edit($locationId, $id)
	{
		$record = $this->locationTranslationModel->find($id);
		$languages = $this->languageModel->findAll();
		$location = $this->locationModel->find($locationId);

		return view('admin/location_translations/edit', [
			'location' => $location,
			'record' => $record,
			'languages' => $languages
		]);
	}

	public function update($locationId, $id)
	{
		$this->locationTranslationModel->update($id, [
			'name' => esc($this->request->getPost('name')) ?: null
		]);

		return redirect()->to("/admin/locations/$locationId/trans")->with('success', 'Translation updated');
	}

	public function delete($locationId, $id)
	{
		$this->locationTranslationModel->delete($id);
		return redirect()->to("/admin/locations/$locationId/trans")->with('success', 'Translation deleted');
	}
}
