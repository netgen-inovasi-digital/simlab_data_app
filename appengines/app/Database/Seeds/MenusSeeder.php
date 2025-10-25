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
        'id_menu' => 18,
        'kode_menu' => '4',
        'kode_induk' => '0',
        'nama' => 'Pengaturan',
        'link' => '#',
        'icon' => 'bi-gear',
        'sort_order' => 20,
      ],
      [
        'id_menu' => 19,
        'kode_menu' => '4.1',
        'kode_induk' => '4',
        'nama' => 'Pengguna',
        'link' => 'user',
        'icon' => 'bi-person',
        'sort_order' => 21,
      ],
      [
        'id_menu' => 20,
        'kode_menu' => '4.3',
        'kode_induk' => '4',
        'nama' => 'Role',
        'link' => 'role',
        'icon' => 'bi-shield-lock',
        'sort_order' => 24,
      ],
      [
        'id_menu' => 21,
        'kode_menu' => '4.2',
        'kode_induk' => '4',
        'nama' => 'Otoritas',
        'link' => 'otoritas',
        'icon' => 'bi-shield-check',
        'sort_order' => 23,
      ],
      [
        'id_menu' => 23,
        'kode_menu' => '4.4',
        'kode_induk' => '4',
        'nama' => 'Menu',
        'link' => 'menu',
        'icon' => 'bi-people',
        'sort_order' => 25,
      ],
      [
        'id_menu' => 24,
        'kode_menu' => '4.5',
        'kode_induk' => '4',
        'nama' => 'Konfigurasi Email',
        'link' => 'mail',
        'icon' => 'bi bi-envelope-at',
        'sort_order' => 22,
      ],
      [
        'id_menu' => 45,
        'kode_menu' => '2',
        'kode_induk' => '0',
        'nama' => 'Personel',
        'link' => 'personel',
        'icon' => 'bi bi-person',
        'sort_order' => 8,
      ],
      [
        'id_menu' => 46,
        'kode_menu' => '3',
        'kode_induk' => '0',
        'nama' => 'Dokumen Akreditasi',
        'link' => 'folder',
        'icon' => 'bi bi-folder',
        'sort_order' => 6,
      ],
    ];

    $this->db->table('menus')->insertBatch($data);
  }
}
