<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('otoritas', ['namespace' => 'Modules\Otoritas\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Otoritas::index');
  $subroutes->post('submit', 'Otoritas::submit');
  $subroutes->post('edit', 'Otoritas::edit');
  $subroutes->get('akses', 'Otoritas::editOtorisasi');
  $subroutes->post('delete', 'Otoritas::delete');
  $subroutes->get('(:any)', 'Otoritas::$1');
});
