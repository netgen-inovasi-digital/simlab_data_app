<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OtoritasSeeder extends Seeder
{
  public function run()
  {
    $allData = [
      ['id_otoritas' => 1, 'role_id' => 1, 'kode_menu' => '1', 'status_otoritas' => 1],
      ['id_otoritas' => 2, 'role_id' => 1, 'kode_menu' => '2', 'status_otoritas' => 1],
      ['id_otoritas' => 3, 'role_id' => 1, 'kode_menu' => '3', 'status_otoritas' => 1],
      ['id_otoritas' => 4, 'role_id' => 1, 'kode_menu' => '4', 'status_otoritas' => 1],
      ['id_otoritas' => 5, 'role_id' => 1, 'kode_menu' => '5', 'status_otoritas' => 0],
      ['id_otoritas' => 6, 'role_id' => 1, 'kode_menu' => '6', 'status_otoritas' => 1],
      ['id_otoritas' => 7, 'role_id' => 1, 'kode_menu' => '7', 'status_otoritas' => 1],
      ['id_otoritas' => 8, 'role_id' => 1, 'kode_menu' => '7.1', 'status_otoritas' => 1],
      ['id_otoritas' => 9, 'role_id' => 1, 'kode_menu' => '7.2', 'status_otoritas' => 0],
      ['id_otoritas' => 10, 'role_id' => 1, 'kode_menu' => '7.3', 'status_otoritas' => 0],
      ['id_otoritas' => 11, 'role_id' => 1, 'kode_menu' => '7.4', 'status_otoritas' => 0],
      ['id_otoritas' => 12, 'role_id' => 1, 'kode_menu' => '7.5', 'status_otoritas' => 0],

      ['id_otoritas' => 13, 'role_id' => 2, 'kode_menu' => '1', 'status_otoritas' => 1],
      ['id_otoritas' => 14, 'role_id' => 2, 'kode_menu' => '2', 'status_otoritas' => 0],
      ['id_otoritas' => 15, 'role_id' => 2, 'kode_menu' => '3', 'status_otoritas' => 0],
      ['id_otoritas' => 16, 'role_id' => 2, 'kode_menu' => '4', 'status_otoritas' => 0],
      ['id_otoritas' => 17, 'role_id' => 2, 'kode_menu' => '5', 'status_otoritas' => 1],
      ['id_otoritas' => 18, 'role_id' => 2, 'kode_menu' => '6', 'status_otoritas' => 1],
      ['id_otoritas' => 19, 'role_id' => 2, 'kode_menu' => '7', 'status_otoritas' => 0],
      ['id_otoritas' => 20, 'role_id' => 2, 'kode_menu' => '7.1', 'status_otoritas' => 0],
      ['id_otoritas' => 21, 'role_id' => 2, 'kode_menu' => '7.2', 'status_otoritas' => 0],
      ['id_otoritas' => 22, 'role_id' => 2, 'kode_menu' => '7.3', 'status_otoritas' => 0],
      ['id_otoritas' => 23, 'role_id' => 2, 'kode_menu' => '7.4', 'status_otoritas' => 0],
      ['id_otoritas' => 24, 'role_id' => 2, 'kode_menu' => '7.5', 'status_otoritas' => 0],

      ['id_otoritas' => 25, 'role_id' => 8, 'kode_menu' => '1', 'status_otoritas' => 1],
      ['id_otoritas' => 26, 'role_id' => 8, 'kode_menu' => '2', 'status_otoritas' => 1],
      ['id_otoritas' => 27, 'role_id' => 8, 'kode_menu' => '3', 'status_otoritas' => 1],
      ['id_otoritas' => 28, 'role_id' => 8, 'kode_menu' => '4', 'status_otoritas' => 1],
      ['id_otoritas' => 29, 'role_id' => 8, 'kode_menu' => '5', 'status_otoritas' => 1],
      ['id_otoritas' => 30, 'role_id' => 8, 'kode_menu' => '6', 'status_otoritas' => 1],
      ['id_otoritas' => 31, 'role_id' => 8, 'kode_menu' => '7', 'status_otoritas' => 1],
      ['id_otoritas' => 32, 'role_id' => 8, 'kode_menu' => '7.1', 'status_otoritas' => 1],
      ['id_otoritas' => 33, 'role_id' => 8, 'kode_menu' => '7.2', 'status_otoritas' => 1],
      ['id_otoritas' => 34, 'role_id' => 8, 'kode_menu' => '7.3', 'status_otoritas' => 1],
      ['id_otoritas' => 35, 'role_id' => 8, 'kode_menu' => '7.4', 'status_otoritas' => 1],
      ['id_otoritas' => 36, 'role_id' => 8, 'kode_menu' => '7.5', 'status_otoritas' => 1],

      ['id_otoritas' => 37, 'role_id' => 9, 'kode_menu' => '1', 'status_otoritas' => 1],
      ['id_otoritas' => 38, 'role_id' => 9, 'kode_menu' => '2', 'status_otoritas' => 0],
      ['id_otoritas' => 39, 'role_id' => 9, 'kode_menu' => '3', 'status_otoritas' => 0],
      ['id_otoritas' => 40, 'role_id' => 9, 'kode_menu' => '4', 'status_otoritas' => 0],
      ['id_otoritas' => 41, 'role_id' => 9, 'kode_menu' => '5', 'status_otoritas' => 1],
      ['id_otoritas' => 42, 'role_id' => 9, 'kode_menu' => '6', 'status_otoritas' => 1],
      ['id_otoritas' => 43, 'role_id' => 9, 'kode_menu' => '7', 'status_otoritas' => 0],
      ['id_otoritas' => 44, 'role_id' => 9, 'kode_menu' => '7.1', 'status_otoritas' => 0],
      ['id_otoritas' => 45, 'role_id' => 9, 'kode_menu' => '7.2', 'status_otoritas' => 0],
      ['id_otoritas' => 46, 'role_id' => 9, 'kode_menu' => '7.3', 'status_otoritas' => 0],
      ['id_otoritas' => 47, 'role_id' => 9, 'kode_menu' => '7.4', 'status_otoritas' => 0],
      ['id_otoritas' => 48, 'role_id' => 9, 'kode_menu' => '7.5', 'status_otoritas' => 0],

      ['id_otoritas' => 49, 'role_id' => 10, 'kode_menu' => '1', 'status_otoritas' => 1],
      ['id_otoritas' => 50, 'role_id' => 10, 'kode_menu' => '2', 'status_otoritas' => 0],
      ['id_otoritas' => 51, 'role_id' => 10, 'kode_menu' => '3', 'status_otoritas' => 0],
      ['id_otoritas' => 52, 'role_id' => 10, 'kode_menu' => '4', 'status_otoritas' => 0],
      ['id_otoritas' => 53, 'role_id' => 10, 'kode_menu' => '5', 'status_otoritas' => 1],
      ['id_otoritas' => 54, 'role_id' => 10, 'kode_menu' => '6', 'status_otoritas' => 1],
      ['id_otoritas' => 55, 'role_id' => 10, 'kode_menu' => '7', 'status_otoritas' => 0],
      ['id_otoritas' => 56, 'role_id' => 10, 'kode_menu' => '7.1', 'status_otoritas' => 0],
      ['id_otoritas' => 57, 'role_id' => 10, 'kode_menu' => '7.2', 'status_otoritas' => 0],
      ['id_otoritas' => 58, 'role_id' => 10, 'kode_menu' => '7.3', 'status_otoritas' => 0],
      ['id_otoritas' => 59, 'role_id' => 10, 'kode_menu' => '7.4', 'status_otoritas' => 0],
      ['id_otoritas' => 60, 'role_id' => 10, 'kode_menu' => '7.5', 'status_otoritas' => 0],
    ];

    // Ambil semua kode_menu yang ada di tabel menus
    $existingMenus = $this->db->table('menus')->select('kode_menu')->get()->getResultArray();
    $existingMenus = array_column($existingMenus, 'kode_menu');

    // Filter data agar hanya kode_menu yang ada di tabel menus yang di-insert
    $filteredData = array_filter($allData, function ($item) use ($existingMenus) {
      return in_array($item['kode_menu'], $existingMenus);
    });

    // Insert batch
    if (!empty($filteredData)) {
      $this->db->table('otoritas')->insertBatch($filteredData);
    }
  }
}
