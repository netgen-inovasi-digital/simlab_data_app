<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OtoritasSeeder extends Seeder
{
    public function run()
    {
        // Semua data otoritas (155 baris)
        $allData = [
            ['id_otoritas' => 1, 'role_id' => 1, 'kode_menu' => '3', 'status_otoritas' => 1],
            ['id_otoritas' => 2, 'role_id' => 1, 'kode_menu' => '1', 'status_otoritas' => 1],
            ['id_otoritas' => 3, 'role_id' => 1, 'kode_menu' => '4', 'status_otoritas' => 1],
            ['id_otoritas' => 4, 'role_id' => 1, 'kode_menu' => '5', 'status_otoritas' => 1],
            ['id_otoritas' => 5, 'role_id' => 1, 'kode_menu' => '6', 'status_otoritas' => 0],
            ['id_otoritas' => 6, 'role_id' => 1, 'kode_menu' => '7', 'status_otoritas' => 0],
            ['id_otoritas' => 7, 'role_id' => 1, 'kode_menu' => '2', 'status_otoritas' => 1],
            ['id_otoritas' => 20, 'role_id' => 1, 'kode_menu' => '1', 'status_otoritas' => 0],
            ['id_otoritas' => 21, 'role_id' => 1, 'kode_menu' => '4', 'status_otoritas' => 0],
            ['id_otoritas' => 22, 'role_id' => 1, 'kode_menu' => '5', 'status_otoritas' => 0],
            ['id_otoritas' => 23, 'role_id' => 1, 'kode_menu' => '3', 'status_otoritas' => 0],
            ['id_otoritas' => 24, 'role_id' => 1, 'kode_menu' => '8', 'status_otoritas' => 1],
            ['id_otoritas' => 29, 'role_id' => 1, 'kode_menu' => '9', 'status_otoritas' => 1],
            ['id_otoritas' => 30, 'role_id' => 1, 'kode_menu' => '10', 'status_otoritas' => 1],
            ['id_otoritas' => 61, 'role_id' => 1, 'kode_menu' => '12.2', 'status_otoritas' => 1],
            ['id_otoritas' => 62, 'role_id' => 1, 'kode_menu' => '11', 'status_otoritas' => 1],
            ['id_otoritas' => 66, 'role_id' => 1, 'kode_menu' => '10.1', 'status_otoritas' => 1],
            ['id_otoritas' => 68, 'role_id' => 1, 'kode_menu' => '10.2', 'status_otoritas' => 1],
            ['id_otoritas' => 87, 'role_id' => 1, 'kode_menu' => '9.1', 'status_otoritas' => 1],
            ['id_otoritas' => 88, 'role_id' => 1, 'kode_menu' => '9.3', 'status_otoritas' => 1],
            ['id_otoritas' => 89, 'role_id' => 1, 'kode_menu' => '9.2', 'status_otoritas' => 1],
            ['id_otoritas' => 90, 'role_id' => 1, 'kode_menu' => '9.4', 'status_otoritas' => 1],
            ['id_otoritas' => 113, 'role_id' => 1, 'kode_menu' => '12', 'status_otoritas' => 1],
            ['id_otoritas' => 126, 'role_id' => 8, 'kode_menu' => '1', 'status_otoritas' => 1],
            ['id_otoritas' => 127, 'role_id' => 8, 'kode_menu' => '3', 'status_otoritas' => 1],
            ['id_otoritas' => 128, 'role_id' => 8, 'kode_menu' => '4', 'status_otoritas' => 1],
            ['id_otoritas' => 129, 'role_id' => 8, 'kode_menu' => '9', 'status_otoritas' => 1],
            ['id_otoritas' => 130, 'role_id' => 8, 'kode_menu' => '9.1', 'status_otoritas' => 1],
            ['id_otoritas' => 131, 'role_id' => 8, 'kode_menu' => '9.3', 'status_otoritas' => 1],
            ['id_otoritas' => 132, 'role_id' => 8, 'kode_menu' => '9.5', 'status_otoritas' => 1],
            ['id_otoritas' => 133, 'role_id' => 8, 'kode_menu' => '9.4', 'status_otoritas' => 1],
            ['id_otoritas' => 134, 'role_id' => 8, 'kode_menu' => '9.2', 'status_otoritas' => 1],
            ['id_otoritas' => 135, 'role_id' => 8, 'kode_menu' => '9.6', 'status_otoritas' => 1],
            ['id_otoritas' => 136, 'role_id' => 8, 'kode_menu' => '11', 'status_otoritas' => 1],
            ['id_otoritas' => 137, 'role_id' => 8, 'kode_menu' => '12', 'status_otoritas' => 1],
            ['id_otoritas' => 138, 'role_id' => 8, 'kode_menu' => '12.1', 'status_otoritas' => 1],
            ['id_otoritas' => 139, 'role_id' => 8, 'kode_menu' => '12.3', 'status_otoritas' => 1],
            ['id_otoritas' => 140, 'role_id' => 8, 'kode_menu' => '12.2', 'status_otoritas' => 1],
            ['id_otoritas' => 141, 'role_id' => 8, 'kode_menu' => '12.4', 'status_otoritas' => 1],
            ['id_otoritas' => 142, 'role_id' => 8, 'kode_menu' => '2', 'status_otoritas' => 1],
            ['id_otoritas' => 143, 'role_id' => 8, 'kode_menu' => '10', 'status_otoritas' => 1],
            ['id_otoritas' => 144, 'role_id' => 8, 'kode_menu' => '10.1', 'status_otoritas' => 1],
            ['id_otoritas' => 145, 'role_id' => 8, 'kode_menu' => '7', 'status_otoritas' => 1],
            ['id_otoritas' => 146, 'role_id' => 8, 'kode_menu' => '5', 'status_otoritas' => 1],
            ['id_otoritas' => 147, 'role_id' => 8, 'kode_menu' => '8', 'status_otoritas' => 1],
            ['id_otoritas' => 148, 'role_id' => 2, 'kode_menu' => '1', 'status_otoritas' => 1],
            ['id_otoritas' => 149, 'role_id' => 2, 'kode_menu' => '3', 'status_otoritas' => 1],
            ['id_otoritas' => 150, 'role_id' => 2, 'kode_menu' => '4', 'status_otoritas' => 1],
            ['id_otoritas' => 151, 'role_id' => 1, 'kode_menu' => '9.5', 'status_otoritas' => 1],
            ['id_otoritas' => 152, 'role_id' => 1, 'kode_menu' => '9.6', 'status_otoritas' => 1],
            ['id_otoritas' => 153, 'role_id' => 1, 'kode_menu' => '12.1', 'status_otoritas' => 1],
            ['id_otoritas' => 154, 'role_id' => 1, 'kode_menu' => '12.3', 'status_otoritas' => 1],
            ['id_otoritas' => 155, 'role_id' => 1, 'kode_menu' => '12.4', 'status_otoritas' => 1],
        ];

        // Ambil semua kode_menu yang ada di tabel menus
        $existingMenus = $this->db->table('menus')->select('kode_menu')->get()->getResultArray();
        $existingMenus = array_column($existingMenus, 'kode_menu');

        // Filter data agar hanya kode_menu yang ada di tabel menus yang di-insert
        $filteredData = array_filter($allData, function($item) use ($existingMenus) {
            return in_array($item['kode_menu'], $existingMenus);
        });

        // Insert batch
        if (!empty($filteredData)) {
            $this->db->table('otoritas')->insertBatch($filteredData);
        }
    }
}
