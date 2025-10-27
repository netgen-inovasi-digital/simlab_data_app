<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
  public function run()
  {
    // Hapus semua record di roles (tetap aman meski ada foreign key)
    $this->db->query('DELETE FROM roles');

    // Reset auto-increment
    $this->db->query('ALTER TABLE roles AUTO_INCREMENT = 1');

    // Data roles sesuai kolom yang ada
    $data = [
      ['id_role' => 1, 'nama_role' => 'Admin', 'status_role' => 1],
      ['id_role' => 2, 'nama_role' => 'Kepala Lab', 'status_role' => 1],
      ['id_role' => 8, 'nama_role' => 'Super Admin',  'status_role' => 1],
      ['id_role' => 9, 'nama_role' => 'Asesor',  'status_role' => 1],
      ['id_role' => 10, 'nama_role' => 'Tim Akreditasi', 'status_role' => 1],
    ];

    // Insert batch
    $this->db->table('roles')->insertBatch($data);
  }
}
