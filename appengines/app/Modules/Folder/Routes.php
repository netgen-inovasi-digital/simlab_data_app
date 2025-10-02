<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('folder', ['namespace' => 'Modules\Folder\Controllers'], function ($subroutes) {

    $subroutes->get('/', 'Folder::index');
    // Rute spesifik dipindahkan ke atas
    $subroutes->get('detail-file/(:any)', 'Folder::detailFile/$1');
    $subroutes->get('edit-file/(:any)', 'Folder::editFile/$1');
    $subroutes->post('submit', 'Folder::submit');
    $subroutes->post('submit-folder-baru', 'Folder::submitFolderBaru'); // Rute baru
    $subroutes->post('edit', 'Folder::edit'); // Seharusnya ini juga 'edit/(:any)' jika edit folder menggunakan ID di URL
    $subroutes->post('delete/(:any)', 'Folder::delete/$1'); // <-- PERBAIKAN DI SINI
    $subroutes->post('updated', 'Folder::updated');
    $subroutes->post('toggle', 'Folder::toggle');
    // Rute wildcard (:any) diletakkan di bagian bawah grup
    $subroutes->get('(:any)', 'Folder::$1');
});
