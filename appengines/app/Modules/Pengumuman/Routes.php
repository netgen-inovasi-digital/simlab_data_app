<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('pengumuman', ['namespace' => 'Modules\Pengumuman\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Pengumuman::index');
  $subroutes->get('(:any)', 'Pengumuman::$1');
  $subroutes->post('submit', 'Pengumuman::submit');
  $subroutes->post('edit', 'Pengumuman::edit');
  $subroutes->post('delete', 'Pengumuman::delete');
  $subroutes->post('upload', 'Pengumuman::upload');
  $subroutes->post('toggle', 'Pengumuman::toggle');
});
