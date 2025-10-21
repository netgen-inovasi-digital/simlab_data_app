<?php

if (!isset($routes)) {
  $routes = \Config\Services::routes(true);
}

$routes->group('mail', ['namespace' => 'Modules\Mail\Controllers'], function ($subroutes) {

  $subroutes->get('/', 'Mail::index');
  $subroutes->get('(:any)', 'Mail::$1');
  $subroutes->post('submit', 'Mail::submit');
});
