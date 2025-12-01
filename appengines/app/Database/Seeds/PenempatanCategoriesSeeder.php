<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenempatanCategoriesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_penempatan' => 1, 'nama' => 'Lab Terpadu', 'slug' => 'lab-terpadu', 'created_at' => '2025-11-12 16:36:23'],
            ['id_penempatan' => 2, 'nama' => 'Mutu dan Administrasi', 'slug' => 'mutu-dan-administrasi', 'created_at' => '2025-11-11 19:18:55'],
            ['id_penempatan' => 3, 'nama' => 'Lab Tanah', 'slug' => 'lab-tanah', 'created_at' => '2025-10-28 16:25:41'],
            ['id_penempatan' => 4, 'nama' => 'Lab Kualitas Air', 'slug' => 'lab-kualitas-air', 'created_at' => '2025-11-13 22:12:49'],
            ['id_penempatan' => 5, 'nama' => 'Lab Udara(PPLH)', 'slug' => 'lab-udara(pplh)', 'created_at' => '2025-10-29 17:54:32'],
            ['id_penempatan' => 6, 'nama' => 'Lab Struktur dan Material', 'slug' => 'lab-struktur-dan-material', 'created_at' => '2025-11-10 21:54:50'],
        ];

        $this->db->table('penempatan_categories')->insertBatch($data);
    }
}
