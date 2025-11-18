<?php

namespace Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Dashboard extends BaseController
{
  public function index()
  {
    $data = $this->getDashboardData();
    $data['title'] = 'Dashboard';
    $data['content'] = 'Modules\Dashboard\Views\v_dashboard';
    return view('template', $data);
  }

  public function load()
  {
    $data = $this->getDashboardData();
    $data['title'] = 'Dashboard';
    return view('Modules\Dashboard\Views\v_dashboard', $data);
  }


  private function getDashboardData()
  {
    $session = session();
    $nama = $session->get('nama');
    $role_id = $session->get('role_id');

    date_default_timezone_set('Asia/Makassar');
    $hour = date('H');
    $greeting = 'Selamat pagi';

    if ($hour >= 12 && $hour < 17) {
      $greeting = 'Selamat siang';
    } elseif ($hour >= 17 && $hour < 21) {
      $greeting = 'Selamat sore';
    } elseif ($hour >= 21 || $hour < 4) {
      $greeting = 'Selamat malam';
    }

    // [PERBAIKAN] Logika penghitungan file dan folder berdasarkan otorisasi role
    $modelFiles = new MyModel('files');
    $modelFolders = new MyModel('folder');
    $modelOtorFile = new MyModel('otoritas_file');
    $modelOtorFolder = new MyModel('otoritas_folder');
    $modelPersonel = new MyModel('personel');
    $modelCategories = new MyModel('categories');
    $modelUsers = new MyModel('users');

    $totalFiles = 0;
    $totalFolders = 0;
    $totalCategories = 0;

    if ($role_id != 9 || $role_id != 2) { // Melihat semua
      $totalFiles = $modelFiles->getCountAllbyManyWhere([]);
      $totalFolders = $modelFolders->getCountAllbyManyWhere([]);
      $totalCategories = $modelCategories->getCountAllbyManyWhere([]);
    } else if ($role_id) { // Role lain melihat berdasarkan otorisasi
      // Hitung file yang bisa dilihat
      $totalFiles = $modelOtorFile->getCountAllbyManyWhere([
        'id_role' => $role_id,
        'can_view' => 1
      ]);
      // Hitung folder yang bisa dilihat
      $totalFolders = $modelOtorFolder->getCountAllbyManyWhere([
        'id_role' => $role_id,
        'can_view' => 1
      ]);

      // [BARU] Hitung kategori unik dari file yang bisa dilihat
      $db = \Config\Database::connect();
      $totalCategories = $db->table('files')
        ->distinct()
        ->select('files.categories_id')
        ->join('otoritas_file', 'otoritas_file.id_file = files.id_files')
        ->where('otoritas_file.id_role', $role_id)
        ->where('otoritas_file.can_view', 1)
        ->where('files.categories_id IS NOT NULL')
        ->countAllResults();

      dd($totalFiles);
    }

    return [
      'totalFolders' => $totalFolders,
      'totalFiles' => $totalFiles,
      'totalPersonel' => $modelPersonel->getCountAllbyManyWhere([]),
      'totalCategories' => $totalCategories,
      'totalUsers' => $modelUsers->getCountAllbyManyWhere([]),
      'greeting' => $greeting,
      'nama_user' => $nama,
      'role_id' => $role_id,
    ];
  }
}
