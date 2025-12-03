<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('categories', ['namespace' => 'Modules\Categories\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Categories::index');
  $subroutes->post('submit', 'Categories::submit');
  $subroutes->get('edit', 'Categories::edit');
  $subroutes->post('delete', 'Categories::delete');
  $subroutes->get('(:any)', 'Categories::$1');
});
