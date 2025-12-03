<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('personel', ['namespace' => 'Modules\Personel\Controllers'], function ($subroutes) {

    $subroutes->get('/', 'Personel::index');
    $subroutes->get('fileList', 'Personel::fileList'); // [BARU] Route untuk mengambil daftar file
    $subroutes->get('(:any)', 'Personel::$1');
    $subroutes->post('submit', 'Personel::submit');
    $subroutes->post('edit', 'Personel::edit');
    $subroutes->post('delete', 'Personel::delete');
    $subroutes->post('updated', 'Personel::updated');
    $subroutes->post('toggle', 'Personel::toggle');
});
