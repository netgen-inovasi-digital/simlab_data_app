<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_role' => 1, 'nama_role' => 'Admin', 'status_role' => 1],
            ['id_role' => 2, 'nama_role' => 'Kepala Lab', 'status_role' => 1],
            ['id_role' => 8, 'nama_role' => 'Super Admin', 'status_role' => 1],
            ['id_role' => 9, 'nama_role' => 'Asesor', 'status_role' => 1],
            ['id_role' => 10, 'nama_role' => 'Tim Akreditasi', 'status_role' => 1],
        ];

        // Using query builder to insert data
        $this->db->table('roles')->insertBatch($data);
    }
}
