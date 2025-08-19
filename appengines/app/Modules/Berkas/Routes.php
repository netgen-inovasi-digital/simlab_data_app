<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('berkas', ['namespace' => 'Modules\Berkas\Controllers'], function ($subroutes) {

    $subroutes->get('/', 'Berkas::index');
    $subroutes->get('(:any)', 'Berkas::$1');
    $subroutes->post('submit', 'Berkas::submit');
    $subroutes->post('edit', 'Berkas::edit');
    $subroutes->post('delete', 'Berkas::delete');
    $subroutes->post('upload', 'Berkas::upload');
});
