<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('dokumen', ['namespace' => 'Modules\Dokumen\Controllers'], function($subroutes){

    $subroutes->get('/', 'Dokumen::index');
    $subroutes->get('(:any)', 'Dokumen::$1');
    $subroutes->post('submit', 'Dokumen::submit');
    $subroutes->post('edit', 'Dokumen::edit');
    $subroutes->post('delete', 'Dokumen::delete');
    $subroutes->post('updated', 'Dokumen::updated');
    $subroutes->post('toggle', 'Dokumen::toggle');

});
