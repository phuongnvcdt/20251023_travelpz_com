<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\LanguageModel;
use App\Models\CategoryModel;
use App\Models\CategoryTranslationModel;
use App\Models\ItemModel;
use App\Models\LocationModel;
use App\Models\LocationTranslationModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
  /**
   * Instance of the main Request object.
   *
   * @var CLIRequest|IncomingRequest
   */
  protected $request;

  /**
   * An array of helpers to be loaded automatically upon
   * class instantiation. These helpers will be available
   * to all other controllers that extend BaseController.
   *
   * @var list<string>
   */
  protected $helpers = [
    'Admin',
    'Url',
    'Custom'
  ];

  /**
   * Be sure to declare properties for any property fetch you initialized.
   * The creation of dynamic property is deprecated in PHP 8.2.
   */
  // protected $session;

  protected $itemModel;
  protected $categoryModel;
  protected $locationModel;
  protected $categoryTranslationModel;
  protected $locationTranslationModel;

  protected $language;
  protected $language_id;
  protected $base_data;

  /**
   * @return void
   */
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
    // Do Not Edit This Line
    parent::initController($request, $response, $logger);

    // Preload any models, libraries, etc, here.

    // E.g.: $this->session = service('session');

    // Nạp danh sách locale từ DB

    $this->categoryModel = new CategoryModel();
    $this->locationModel = new LocationModel();
    $this->itemModel = new ItemModel();
    $this->categoryTranslationModel = new CategoryTranslationModel();
    $this->locationTranslationModel = new LocationTranslationModel();

    $languageModel = new LanguageModel();
    $languages = $languageModel->getActiveList();

    if ($languages) {
      config('App')->supportedLocales = array_map(fn($lang) => $lang['locale'], $languages);
      foreach ($languages as $lang) {
        $meta_hreflangs[$lang['code']] = language_link($lang['locale']);
      }
    }

    $locale = config('App')->currentLocale ?? config('App')->defaultLocale;
    $this->language = array_find($languages, fn($lang) => strtolower($lang['locale']) == strtolower($locale));
    $this->language_id = $this->language['id'] ?? 0;

    $this->base_data['is_mobile'] = is_mobile($this->request);
    $this->base_data['categories'] = $this->categoryModel->select('categories.*, category_translations.name as trans_name')
      ->join('category_translations', "category_translations.category_id = categories.id AND category_translations.language_id = {$this->language_id}", 'left')
      ->where('parent_id', null)->findAll();
    $this->base_data['languages'] = $languages;
    $this->base_data['language'] = $this->language;
    $this->base_data['meta_title'] = meta_title();
    $this->base_data['meta_description'] = meta_description();
    $this->base_data['meta_image'] = thumbnailLink();

    $this->base_data['meta_json_ld'] = [
      "@context" => "https://schema.org/",
      "@type" => "Organization",
      "name" => 'TravelPZ',
      "url" => "https://travelpz.com",
      "description" => $this->base_data['meta_description'],
      "logo" => base_url('assets/img/logo.png'),
      "image" => [
        $this->base_data['meta_image']
      ],
      "sameAs" => [
        "https://www.facebook.com/travelpzt",
        "https://www.instagram.com/travel.pz",
        "https://www.youtube.com/@travelpz",
        "https://www.threads.com/@travel.pz",
        "https://linkedin.com/in/travelpz",
        "https://dailymotion.com/travel.pz"
      ],
    ];

    $this->base_data['meta_hreflangs'] = $meta_hreflangs;
    $this->base_data['meta_keywords'] = [
      'travel',
      'tourism',
      'cheap travel',
      'travel deals',
      'holiday packages',
      'travel agency',
      'vacation ideas',
      'places to visit',
      'travel guide',
      'backpacking',
    ];
    $this->base_data['ga_id'] = env('GA_ID');
    $this->base_data['bw_id'] = env('BW_ID');
    $this->base_data['cl_id'] = env('CL_ID');
    $this->base_data['nv_id'] = env('NV_ID');
  }

  protected function responseError404()
  {
    return service('response')
      ->setStatusCode(404)
      ->setBody($this->viewError404());
  }

  protected function viewError404()
  {
    $this->base_data['meta_title'] = 'Error 404';
    $this->base_data['meta_description'] = 'Error 404 - ' . trans('Page not found');
    $this->base_data['meta_keywords'] = ['error', '404'];
    $this->base_data['meta_image'] = thumbnailLink();

    return view('errors/custom_404', [
        'base_data' => $this->base_data
      ]);
  }

  protected function getCatBreadcrumbs($cat, $loc, $title)
  {
    $breadcrumbs = [];
    if ($cat['parent_id'] != null) {
      $parent_cat = $this->categoryModel->find($cat['parent_id']);
      if ($parent_cat) {
        $parent_cat['trans_name'] = $this->categoryTranslationModel->getTransName($parent_cat, $this->language);
        array_push($breadcrumbs, [
          'name' => $loc ? relate_items_title($parent_cat, $loc) : $parent_cat['trans_name'] ?? $parent_cat['en_name'],
          'link' => $loc ? relate_items_link($parent_cat, $loc) : category_link($parent_cat),
        ]);
      }
    }

    array_push($breadcrumbs, [
      'name' => $title,
      'link' => '',
    ]);

    return [
      'title' => $title,
      'list' => $breadcrumbs
    ];
  }

  protected function getLocBreadcrumbs($locs, $title)
  {
    $breadcrumbs = [];
    foreach ($locs as $loc) {
      if (empty($loc)) {
        continue;
      }

      if (empty($loc['trans_name'])) {
        $loc['trans_name'] = $this->locationTranslationModel->getTransName($loc, $this->language);
      }

      array_push($breadcrumbs, [
        'name' => $loc['trans_name'] ?? $loc['en_name'],
        'link' => location_link($loc),
      ]);
    }

    array_push($breadcrumbs, [
      'name' => $title,
      'link' => '',
    ]);

    return [
      'title' => $title,
      'list' => $breadcrumbs
    ];
  }

  protected function getSidebarData($country)
  {
    if (!empty($country)) {
      $sidebar_data = $this->getSidebarDataByLoc($country);
    }

    if (empty($sidebar_data)) {
      $sidebar_data = $this->getSidebarDataByLoc();
    }

    return $sidebar_data;
  }

  protected function getRelateItems($city, $country, $itemId = null)
  {
    if (!empty($city)) {
      $relate_items = $this->getRelateItemsByLoc($itemId, $city);
    }

    if (empty($relate_items) && !empty($country)) {
      $relate_items = $this->getRelateItemsByLoc($itemId, $country);
    }

    if (empty($relate_items)) {
      $relate_items = $this->getRelateItemsByLoc($itemId);
    }

    return $relate_items;
  }

  private function getSidebarDataByLoc($country = null)
  {
    $sidebar_builder = $this->locationModel->select('locations.*, location_translations.name as trans_name, COUNT(DISTINCT items.id) as item_count')
      ->join('location_translations', "location_translations.location_id = locations.id AND location_translations.language_id = {$this->language_id}", 'left');

    if (!empty($country)) {
      $sidebar_title = trans('Cities');
      $sidebar_builder->join('items', 'items.city_id = locations.id', 'left')
        ->where('locations.parent_id', $country['id']);
    } else {
      $sidebar_title = trans('Countries');
      $sidebar_builder->join('items', 'items.country_id = locations.id', 'left')
        ->where('locations.parent_id', null);
    }

    $sidebar_list = $sidebar_builder->groupBy('locations.id')
      ->orderBy('en_name', 'ASC')
      ->findAll();

    if (empty($sidebar_list)) {
      return null;
    }

    $sidebar_data = [
      'title' => $sidebar_title,
      'list' => array_map(fn($loc) => [
        'en_name' => $loc['en_name'],
        'trans_name' => $loc['trans_name'],
        'item_count' => $loc['item_count'],
        'link' => location_link($loc),
      ], $sidebar_list ?? [])
    ];

    return $sidebar_data;
  }

  private function getRelateItemsByLoc($itemId = null, $loc = null)
  {
    $relate_items = [];
    foreach ($this->base_data['categories'] as $cat) {
      $relate_builder = $this->itemModel->where('category_id', $cat['id']);
      if (!empty($loc)) {
        if (empty($loc['parent_id'])) {
          $relate_builder->where('country_id', $loc['id']);
        } else {
          $relate_builder->where('city_id', $loc['id']);
        }
      }

      $cat_items = $relate_builder->where('id !=', $itemId)
        ->orderBy('created_at', 'DESC')
        ->findAll(3);

      if (!empty($cat_items)) {
        array_push($relate_items, [
          'title' => relate_items_title($cat, $loc),
          'link' => relate_items_link($cat, $loc),
          'items' => $cat_items
        ]);
      }
    }

    return $relate_items;
  }
}
