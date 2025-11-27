<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\SourceModel;
use App\Models\CategoryModel;
use App\Models\LocationModel;

class Items extends BaseController
{
    protected $itemModel;
    protected $sourceModel;
    protected $categoryModel;
    protected $locationModel;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
        $this->sourceModel = new SourceModel();
        $this->categoryModel = new CategoryModel();
        $this->locationModel = new LocationModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $items = $this->itemModel
            ->select('items.*, s.name as source_name, c.en_name as category_name, l1.en_name as country_name, l2.en_name as city_name')
            ->join('sources s', 's.id = items.source_id', 'left')
            ->join('categories c', 'c.id = items.category_id', 'left')
            ->join('locations l1', 'l1.id = items.country_id', 'left')
            ->join('locations l2', 'l2.id = items.city_id', 'left')
            ->findAll();

        foreach ($items as &$item) {
            $subs = $this->db->table('item_sub_categories as isc')
                ->join('categories as c', 'c.id = isc.sub_category_id')
                ->where('isc.item_id', $item['id'])
                ->get()->getResultArray();

            $item['sub_categories'] = implode(', ', array_column($subs, 'en_name'));
        }

        return view('admin/items/index', ['items' => $items]);
    }

    public function create()
    {
        $data['sources'] = $this->sourceModel->findAll();
        $data['categories'] = $this->categoryModel->where('parent_id', null)->findAll(); // chỉ category cha
        $data['sub_categories'] = $this->categoryModel->where('parent_id !=', null)->findAll(); // sub categories
        $data['locations'] = $this->locationModel->findAll();
        $data['countries'] = $this->locationModel->where('parent_id', null)->findAll();
        $data['cities'] = $this->locationModel->where('parent_id !=', null)->findAll();

        return view('admin/items/create', $data);
    }

    public function store()
    {
        $itemId = $this->itemModel->insert([
            'en_name' => $this->request->getPost('en_name'),
            'source_id' => esc($this->request->getPost('source_id')),
            'source_item_id' => esc($this->request->getPost('source_item_id')),
            'category_id' => esc($this->request->getPost('category_id')),
            'country_id' => esc($this->request->getPost('country_id')),
            'city_id' => esc($this->request->getPost('city_id')),
            'en_description' => esc($this->request->getPost('en_description')),
            'image' => esc($this->request->getPost('image')),
            'rating' => esc($this->request->getPost('rating')),
            'rating_count' => esc($this->request->getPost('rating_count')),
            'youtube_id' => esc($this->request->getPost('youtube_id')),
        ]);

        $subCategories = esc($this->request->getPost('sub_categories')) ?? [];
        foreach ($subCategories as $subId) {
            $this->db->table('item_sub_categories')->insert([
                'item_id' => $itemId,
                'sub_category_id' => $subId
            ]);
        }

        return redirect()->to('/admin/items');
    }

    public function edit($id)
    {
        $item = $this->itemModel->find($id);

        $selected_subs = $this->db->table('item_sub_categories')
            ->where('item_id', $id)
            ->get()
            ->getResultArray();
        $selected_subs = array_column($selected_subs, 'sub_category_id');

        $data = [
            'item' => $item,
            'sources' => $this->sourceModel->findAll(),
            'categories' => $this->categoryModel->where('parent_id', null)->findAll(),
            'sub_categories' => $this->categoryModel->where('parent_id !=', null)->findAll(),
            'locations' => $this->locationModel->findAll(),
            'countries' => $this->locationModel->where('parent_id', null)->findAll(),
            'cities' => $this->locationModel->where('parent_id !=', null)->findAll(),
            'selected_subs' => $selected_subs
        ];

        return view('admin/items/edit', $data);
    }

    public function update($id)
    {
        $this->itemModel->update($id, [
            'en_name' => $this->request->getPost('en_name'),
            'source_id' => esc($this->request->getPost('source_id')),
            'source_item_id' => esc($this->request->getPost('source_item_id')),
            'category_id' => esc($this->request->getPost('category_id')),
            'country_id' => esc($this->request->getPost('country_id')),
            'city_id' => esc($this->request->getPost('city_id')),
            'en_description' => esc($this->request->getPost('en_description')),
            'image' => esc($this->request->getPost('image')),
            'rating' => esc($this->request->getPost('rating')),
            'rating_count' => esc($this->request->getPost('rating_count')),
            'youtube_id' => esc($this->request->getPost('youtube_id')),
        ]);

        $this->db->table('item_sub_categories')->where('item_id', $id)->delete();
        $subCategories = esc($this->request->getPost('sub_categories')) ?? [];
        foreach ($subCategories as $subId) {
            $this->db->table('item_sub_categories')->insert([
                'item_id' => $id,
                'sub_category_id' => $subId
            ]);
        }

        return redirect()->to('/admin/items');
    }

    public function delete($id)
    {
        $this->itemModel->delete($id);
        $this->db->table('item_sub_categories')->where('item_id', $id)->delete();
        return redirect()->to('/admin/items');
    }
}
