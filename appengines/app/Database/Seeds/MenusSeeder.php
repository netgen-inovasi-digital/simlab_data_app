<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenusSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_menu' => 6,
        'kode_menu' => '1',
        'kode_induk' => '0',
        'nama' => 'Dashboard',
        'link' => 'dashboard/load',
        'icon' => 'bi-house',
        'sort_order' => 1,
      ],
      [
        'id_menu' => 15,
        'kode_menu' => '9',
        'kode_induk' => '0',
        'nama' => 'Informasi',
        'link' => 'konfigurasi',
        'icon' => 'bi-info-circle',
        'sort_order' => 13,
      ],
      [
        'id_menu' => 18,
        'kode_menu' => '12',
        'kode_induk' => '0',
        'nama' => 'Pengaturan',
        'link' => '#',
        'icon' => 'bi-gear',
        'sort_order' => 20,
      ],
      [
        'id_menu' => 19,
        'kode_menu' => '12.1',
        'kode_induk' => '12',
        'nama' => 'Pengguna',
        'link' => 'user',
        'icon' => 'bi-person',
        'sort_order' => 21,
      ],
      [
        'id_menu' => 20,
        'kode_menu' => '12.3',
        'kode_induk' => '12',
        'nama' => 'Role',
        'link' => 'role',
        'icon' => 'bi-shield-lock',
        'sort_order' => 23,
      ],
      [
        'id_menu' => 21,
        'kode_menu' => '12.2',
        'kode_induk' => '12',
        'nama' => 'Otoritas',
        'link' => 'otoritas',
        'icon' => 'bi-shield-check',
        'sort_order' => 22,
      ],
      [
        'id_menu' => 23,
        'kode_menu' => '12.4',
        'kode_induk' => '12',
        'nama' => 'Menu',
        'link' => 'menu',
        'icon' => 'bi-people',
        'sort_order' => 24,
      ],
      [
        'id_menu' => 45,
        'kode_menu' => '8',
        'kode_induk' => '0',
        'nama' => 'Personel',
        'link' => 'personel',
        'icon' => 'bi bi-person',
        'sort_order' => 8,
      ],
      [
        'id_menu' => 46,
        'kode_menu' => '6',
        'kode_induk' => '0',
        'nama' => 'Folder',
        'link' => 'folder',
        'icon' => 'bi bi-folder',
        'sort_order' => 6,
      ],
    ];

    $this->db->table('menus')->insertBatch($data);
  }
}
