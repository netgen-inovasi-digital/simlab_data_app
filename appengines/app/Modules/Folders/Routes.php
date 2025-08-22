<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('folders', ['namespace' => 'Modules\Folders\Controllers'], function ($subroutes) {

  // $subroutes->get('/', 'Folders::index');           // /folders → index default
  $subroutes->get('(:num)', 'Folders::index/$1');  // /folders/5 → index($foldersId)
  $subroutes->get('datalist', 'Folders::dataList');
  $subroutes->post('submit', 'Folders::submit');

  // $subroutes->post('edit', 'Folders::edit');
  // $subroutes->post('delete', 'Folders::delete');

  $subroutes->get('(:num)/edit/(:any)', 'Folders::edit/$1/$2');
  $subroutes->get('(:num)/delete/(:any)', 'Folders::delete/$1/$2');

  // $subroutes->post('(:num)/edit/(:any)', 'Folders::edit/$1/$2');
  // $subroutes->post('(:num)/delete/(:any)', 'Folders::delete/$1/$2');

  $subroutes->post('upload', 'Folders::upload');
  $subroutes->post('toggle', 'Folders::toggle');
});
