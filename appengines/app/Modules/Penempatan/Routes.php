<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('penempatan', ['namespace' => 'Modules\Penempatan\Controllers'], function ($subroutes) {

    $subroutes->get('/', 'Penempatan::index');
    $subroutes->post('submit', 'Penempatan::submit');
    $subroutes->get('edit', 'Penempatan::edit');
    $subroutes->post('delete', 'Penempatan::delete');
    $subroutes->get('(:any)', 'Penempatan::$1');
});
