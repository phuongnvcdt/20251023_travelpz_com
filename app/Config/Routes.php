<?php

use App\Controllers\Home;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Admin routes
$routes->group('admin', [
  'namespace' => 'App\Controllers\Admin',
  'filter'    => 'adminauth'
], function ($routes) {
  $routes->get('dashboard', 'Dashboard::index');

  // Users
  $routes->get('users', 'Users::index');
  $routes->get('users/create', 'Users::create');
  $routes->post('users/store', 'Users::store');
  $routes->get('users/(:num)/edit', 'Users::edit/$1');
  $routes->post('users/(:num)/update', 'Users::update/$1');
  $routes->get('users/(:num)/delete', 'Users::delete/$1');

  // Languages
  $routes->get('languages', 'Languages::index');
  $routes->get('languages/create', 'Languages::create');
  $routes->post('languages/store', 'Languages::store');
  $routes->get('languages/(:num)/edit', 'Languages::edit/$1');
  $routes->post('languages/(:num)/update', 'Languages::update/$1');
  $routes->get('languages/(:num)/delete', 'Languages::delete/$1');

  // Sources
  $routes->get('sources', 'Sources::index');
  $routes->get('sources/create', 'Sources::create');
  $routes->post('sources/store', 'Sources::store');
  $routes->get('sources/(:num)/edit', 'Sources::edit/$1');
  $routes->post('sources/(:num)/update', 'Sources::update/$1');
  $routes->get('sources/(:num)/delete', 'Sources::delete/$1');

  // Locations
  $routes->get('locations', 'Locations::index');
  $routes->get('locations/create', 'Locations::create');
  $routes->post('locations/store', 'Locations::store');
  $routes->get('locations/(:num)/edit', 'Locations::edit/$1');
  $routes->post('locations/(:num)/update', 'Locations::update/$1');
  $routes->get('locations/(:num)/delete', 'Locations::delete/$1');

  // Location Sources
  $routes->get('locations/(:num)/sources', 'LocationSources::index/$1');           // List
  $routes->get('locations/(:num)/sources/create', 'LocationSources::create/$1');   // Form thêm
  $routes->post('locations/(:num)/sources/store', 'LocationSources::store/$1');    // Lưu mới
  $routes->get('locations/(:num)/sources/(:num)/edit', 'LocationSources::edit/$1/$2'); // Edit
  $routes->post('locations/(:num)/sources/(:num)/update', 'LocationSources::update/$1/$2'); // Update
  $routes->get('locations/(:num)/sources/(:num)/delete', 'LocationSources::delete/$1/$2'); // Delete

  // Location Translations
  $routes->get('locations/(:num)/trans', 'LocationTranslations::index/$1');           // List
  $routes->get('locations/(:num)/trans/create', 'LocationTranslations::create/$1');   // Form thêm
  $routes->post('locations/(:num)/trans/store', 'LocationTranslations::store/$1');    // Lưu mới
  $routes->get('locations/(:num)/trans/(:num)/edit', 'LocationTranslations::edit/$1/$2'); // Edit
  $routes->post('locations/(:num)/trans/(:num)/update', 'LocationTranslations::update/$1/$2'); // Update
  $routes->get('locations/(:num)/trans/(:num)/delete', 'LocationTranslations::delete/$1/$2'); // Delete

  // Categories
  $routes->get('categories', 'Categories::index');
  $routes->get('categories/create', 'Categories::create');
  $routes->post('categories/store', 'Categories::store');
  $routes->get('categories/(:num)/edit', 'Categories::edit/$1');
  $routes->post('categories/(:num)/update', 'Categories::update/$1');
  $routes->get('categories/(:num)/delete', 'Categories::delete/$1');

  // Category Translations
  $routes->get('categories/(:num)/trans', 'CategoryTranslations::index/$1');           // List
  $routes->get('categories/(:num)/trans/create', 'CategoryTranslations::create/$1');   // Form thêm
  $routes->post('categories/(:num)/trans/store', 'CategoryTranslations::store/$1');    // Lưu mới
  $routes->get('categories/(:num)/trans/(:num)/edit', 'CategoryTranslations::edit/$1/$2'); // Edit
  $routes->post('categories/(:num)/trans/(:num)/update', 'CategoryTranslations::update/$1/$2'); // Update
  $routes->get('categories/(:num)/trans/(:num)/delete', 'CategoryTranslations::delete/$1/$2'); // Delete

  // Items
  $routes->get('items', 'Items::index');
  $routes->get('items/create', 'Items::create');
  $routes->post('items/store', 'Items::store');
  $routes->get('items/(:num)/edit', 'Items::edit/$1');
  $routes->post('items/(:num)/update', 'Items::update/$1');
  $routes->get('items/(:num)/delete', 'Items::delete/$1');
});

// User routes
$routes->group('user', function ($routes) {
  $routes->get('dashboard', 'User\Dashboard::index');
});

$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Location
$routes->get('loc/(:segment)', 'Locations::items/$1');
$routes->post('ajax/locations', 'Locations::list');

// Category
$routes->get('cat/(:segment)', 'Categories::items/$1');
$routes->post('ajax/categories', 'Categories::list');

// Search
$routes->get('search', 'Search::search');

// Item
$routes->get('(hotels|activities)/(:segment)/([A-Z0-9_]+)-(:any)', 'Items::show/$1/$2/$3/$4');
$routes->get('(hotels|activities)/(:segment)/(img|thumb)/([A-Z0-9_]+)-(:any)', 'Items::showImg/$1/$2/$3/$4');
$routes->get('(hotels|activities)/(:segment)/book/([A-Z0-9_]+)-(:any)', 'Items::book/$1/$2/$3/$4');
$routes->get('map', 'Items::map');

// Rentcar
$routes->get('rentcar/(:segment)', 'Home::rentCar/$1');

// 404 error
$routes->set404Override(function () {
  $controller = new Home();
  return $controller->error404();
});

// Sitemap
$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('sitemap/(:segment)-sitemap-(:num).xml', 'Sitemap::detail/$1/$2');
$routes->get('sitemap/pages-sitemap.xml', 'Sitemap::pages');

// ETL
$routes->post('etl/login', 'ETL::login');

// Any
$routes->add('(.*)', 'Home::any');

$routes->group('{locale}', ['filter' => 'localefilter'], function ($routes) {
  $routes->get('/', 'Home::index');
  $routes->get('login', 'Auth::index');
  $routes->post('login', 'Auth::login');
  $routes->get('logout', 'Auth::logout');

  // Location
  $routes->get('loc/(:segment)', 'Locations::items/$1');
  $routes->post('ajax/locations', 'Locations::list');

  // Category
  $routes->get('cat/(:segment)', 'Categories::items/$1');
  $routes->post('ajax/categories', 'Categories::list');

  // Search
  $routes->get('search', 'Search::search');

  // Item
  $routes->get('(hotels|activities)/(:segment)/([A-Z0-9_]+)-(:any)', 'Items::show/$1/$2/$3/$4');
  $routes->get('(hotels|activities)/(:segment)/(img|thumb)/([A-Z0-9_]+)-(:any)', 'Items::showImg/$1/$2/$3/$4');
  $routes->get('(hotels|activities)/(:segment)/book/([A-Z0-9_]+)-(:any)', 'Items::book/$1/$2/$3/$4');
  $routes->get('map', 'Items::map');

  // Rentcar
  $routes->get('rentcar/(:segment)', 'Home::rentCar/$1');
});

$routes->group('api', function ($routes) {
  $routes->group('v1', ['filter' => 'apiauth'], function ($routes) {
    $routes->get('items', 'Api\V1\Items::index');
    $routes->resource('items', ['controller' => 'Api\V1\Items']);
  });
});
