<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenusSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_menu' => 1,
        'kode_menu' => '1',
        'kode_induk' => '0',
        'nama' => 'Dashboard',
        'link' => 'dashboard/load',
        'icon' => 'bi-house',
        'sort_order' => 1,
      ],
      [
        'id_menu' => 2,
        'kode_menu' => '2',
        'kode_induk' => '0',
        'nama' => 'Data File',
        'link' => 'berkas',
        'icon' => 'bi-file-earmark-arrow-up',
        'sort_order' => 2,
      ],
      [
        'id_menu' => 3,
        'kode_menu' => '3',
        'kode_induk' => '0',
        'nama' => 'Data Category',
        'link' => 'categories',
        'icon' => 'bi bi-tags',
        'sort_order' => 3,
      ],
      [
        'id_menu' => 4,
        'kode_menu' => '4',
        'kode_induk' => '0',
        'nama' => 'Dokumen Akreditasi',
        'link' => 'folder',
        'icon' => 'bi bi-folder',
        'sort_order' => 4,
      ],
      [
        'id_menu' => 5,
        'kode_menu' => '5',
        'kode_induk' => '0',
        'nama' => 'Personel',
        'link' => 'personel',
        'icon' => 'bi bi-person',
        'sort_order' => 5,
      ],
      [
        'id_menu' => 6,
        'kode_menu' => '6',
        'kode_induk' => '0',
        'nama' => 'Pengaturan',
        'link' => '#',
        'icon' => 'bi-gear',
        'sort_order' => 6,
      ],
      [
        'id_menu' => 7,
        'kode_menu' => '6.1',
        'kode_induk' => '6',
        'nama' => 'Pengguna',
        'link' => 'user',
        'icon' => 'bi-person',
        'sort_order' => 7,
      ],
      [
        'id_menu' => 8,
        'kode_menu' => '6.2',
        'kode_induk' => '6',
        'nama' => 'Konfigurasi Email',
        'link' => 'mail',
        'icon' => 'bi bi-envelope-at',
        'sort_order' => 8,
      ],
      [
        'id_menu' => 9,
        'kode_menu' => '6.3',
        'kode_induk' => '6',
        'nama' => 'Role',
        'link' => 'role',
        'icon' => 'bi-shield-lock',
        'sort_order' => 9,
      ],
      [
        'id_menu' => 10,
        'kode_menu' => '6.4',
        'kode_induk' => '6',
        'nama' => 'Otoritas',
        'link' => 'otoritas',
        'icon' => 'bi-shield-check',
        'sort_order' => 10,
      ],
      [
        'id_menu' => 11,
        'kode_menu' => '6.5',
        'kode_induk' => '6',
        'nama' => 'Menu',
        'link' => 'menu',
        'icon' => 'bi-people',
        'sort_order' => 11,
      ],
    ];

    $this->db->table('menus')->insertBatch($data);
  }
}
