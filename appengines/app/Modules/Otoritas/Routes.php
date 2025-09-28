<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('otoritas', ['namespace' => 'Modules\Otoritas\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Otoritas::index');
  $subroutes->post('submit', 'Otoritas::submit');
  $subroutes->post('akses', 'Otoritas::submitAuthorizationDocs');
  $subroutes->post('edit', 'Otoritas::edit');
  $subroutes->get('show', 'Otoritas::editOtorisasi');
  $subroutes->post('delete', 'Otoritas::delete');
  $subroutes->get('(:any)', 'Otoritas::$1');
});
