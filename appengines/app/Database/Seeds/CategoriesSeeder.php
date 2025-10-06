<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_categories' => 49,
                'nama'          => 'dokumen A',
                'slug'          => 'dokumen-a',
                'created_at'    => '2025-08-14 16:54:43'
            ],
            [
                'id_categories' => 68,
                'nama'          => 'Code of Conduct',
                'slug'          => 'code-of-conduct',
                'created_at'    => '2025-08-19 20:20:59'
            ],
            [
                'id_categories' => 69,
                'nama'          => 'Material Resource',
                'slug'          => 'material-resource',
                'created_at'    => '2025-08-19 21:29:21'
            ],
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
