<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('folder', ['namespace' => 'Modules\Folder\Controllers'], function($subroutes){

    $subroutes->get('/', 'Folder::index');
    $subroutes->get('(:any)', 'Folder::$1');
    $subroutes->post('submit', 'Folder::submit');
    $subroutes->post('edit', 'Folder::edit');
    $subroutes->post('delete', 'Folder::delete');
    $subroutes->post('updated', 'Folder::updated');
    $subroutes->post('toggle', 'Folder::toggle');

});
