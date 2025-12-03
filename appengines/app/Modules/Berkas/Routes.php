<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('berkas', ['namespace' => 'Modules\Berkas\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Berkas::index');
  $subroutes->post('submit', 'Berkas::submit');
  $subroutes->post('submitLinks', 'Berkas::submitLinks');
  $subroutes->post('deleteLinks/(:any)', 'Berkas::deleteLinks/$1');
  $subroutes->get('edit', 'Berkas::edit');
  $subroutes->post('delete', 'Berkas::delete');
  $subroutes->post('upload', 'Berkas::upload');
  $subroutes->get('(:any)', 'Berkas::$1');
});
