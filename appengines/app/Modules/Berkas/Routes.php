<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('berkas', ['namespace' => 'Modules\Berkas\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Berkas::index');
  $subroutes->post('submit', 'Berkas::submit');
  $subroutes->get('edit', 'Berkas::edit');
  $subroutes->post('delete/(:any)', 'Berkas::delete/$1');
  $subroutes->post('upload', 'Berkas::upload');
  $subroutes->get('(:any)', 'Berkas::$1');
});
