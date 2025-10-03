<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FolderSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_folder'  => 1,
                'nama'       => 'test1',
                'slug'       => 'test1',
                'status'     => 'aktif',
                'created_at' => '2025-01-01 00:00:00',
                'updated_at' => '2025-01-01 00:00:00',
                'sort_order' => 1,
                'flag'       => 0
            ],
            [
                'id_folder'  => 2,
                'nama'       => 'test2',
                'slug'       => 'test2',
                'status'     => 'aktif',
                'created_at' => '2025-01-02 00:00:00',
                'updated_at' => '2025-01-02 00:00:00',
                'sort_order' => 2,
                'flag'       => 0
            ],
            [
                'id_folder'  => 3,
                'nama'       => 'test3',
                'slug'       => 'test3',
                'status'     => 'aktif',
                'created_at' => '2025-01-03 00:00:00',
                'updated_at' => '2025-01-03 00:00:00',
                'sort_order' => 3,
                'flag'       => 0
            ],
            [
                'id_folder'  => 4,
                'nama'       => 'test4',
                'slug'       => 'test4',
                'status'     => 'aktif',
                'created_at' => '2025-01-04 00:00:00',
                'updated_at' => '2025-01-04 00:00:00',
                'sort_order' => 4,
                'flag'       => 0
            ],
            [
                'id_folder'  => 7,
                'nama'       => 'test5',
                'slug'       => 'test5',
                'status'     => 'aktif',
                'created_at' => '2025-01-05 00:00:00',
                'updated_at' => '2025-01-05 00:00:00',
                'sort_order' => 5,
                'flag'       => 0
            ],
        ];

        $this->db->table('folder')->insertBatch($data);
    }
}
