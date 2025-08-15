<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('dokumen', ['namespace' => 'Modules\Dokumen\Controllers'], function ($subroutes) {

  // $subroutes->get('/', 'Dokumen::index');           // /doc → index default
  $subroutes->get('(:num)', 'Dokumen::index/$1');  // /doc/5 → index($docId)
  $subroutes->get('datalist', 'Dokumen::dataList');
  $subroutes->post('submit', 'Dokumen::submit');

  // $subroutes->post('edit', 'Dokumen::edit');
  // $subroutes->post('delete', 'Dokumen::delete');

  $subroutes->get('(:num)/edit/(:any)', 'Dokumen::edit/$1/$2');
  $subroutes->get('(:num)/delete/(:any)', 'Dokumen::delete/$1/$2');

  $subroutes->post('upload', 'Dokumen::upload');
  $subroutes->post('toggle', 'Dokumen::toggle');
});
