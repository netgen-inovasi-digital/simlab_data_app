<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('categories', ['namespace' => 'Modules\Categories\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Categories::index');
  $subroutes->get('(:any)', 'Categories::$1');
  $subroutes->post('submit', 'Categories::submit');
  $subroutes->post('edit', 'Categories::edit');
  $subroutes->post('delete', 'Categories::delete');
  // $subroutes->get('delete/(:any)', 'Categories::delete/$1');
  // $subroutes->get('edit/(:any)', 'Categories::edit/$1');
});
