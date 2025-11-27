<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LocationSources extends BaseController
{
	protected $locationSourceModel;
	protected $locationModel;
	protected $sourceModel;

	public function __construct()
	{
		$this->locationSourceModel = new \App\Models\LocationSourceModel();
		$this->locationModel = new \App\Models\LocationModel();
		$this->sourceModel = new \App\Models\SourceModel();
	}

	public function index($locationId)
	{
		$location = $this->locationModel->find($locationId);
		if (!$location) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$location_sources = $this->locationSourceModel
			->select('location_sources.*, sources.name as source_name')
			->join('sources', 'sources.id = location_sources.source_id')
			->where('location_sources.location_id', $locationId)
			->findAll();

		return view('admin/location_sources/index', [
			'location' => $location,
			'location_sources' => $location_sources
		]);
	}

	public function create($locationId)
	{
		$location = $this->locationModel->find($locationId);
		$sources = $this->sourceModel
			->whereNotIn('id', function ($builder) use ($locationId) {
				$builder->select('source_id')
					->from('location_sources')
					->where('location_id', $locationId);
			})
			->findAll();

		return view('admin/location_sources/create', [
			'location' => $location,
			'sources' => $sources
		]);
	}

	public function store($locationId)
	{
		$this->locationSourceModel->save([
			'location_id'        => $locationId,
			'source_id'          => esc($this->request->getPost('source_id')) ?: null,
			'location_source_id' => esc($this->request->getPost('location_source_id')) ?: null
		]);

		return redirect()->to("/admin/locations/$locationId/sources")->with('success', 'Source added');
	}

	public function edit($locationId, $id)
	{
		$record = $this->locationSourceModel->find($id);
		$allSources = $this->sourceModel->findAll();
		$location = $this->locationModel->find($locationId);

		return view('admin/location_sources/edit', [
			'location' => $location,
			'record' => $record,
			'sources' => $allSources
		]);
	}

	public function update($locationId, $id)
	{
		$this->locationSourceModel->update($id, [
			'location_source_id' => esc($this->request->getPost('location_source_id')) ?: null
		]);

		return redirect()->to("/admin/locations/$locationId/sources")->with('success', 'Source updated');
	}

	public function delete($locationId, $id)
	{
		$this->locationSourceModel->delete($id);
		return redirect()->to("/admin/locations/$locationId/sources")->with('success', 'Source deleted');
	}
}
