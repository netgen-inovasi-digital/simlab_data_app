<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FolderLinksSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'         => 3,
                'parent_id'  => 7,
                'child_id'   => 4,
                'sort_order' => 4
            ],
            [
                'id'         => 4,
                'parent_id'  => 4,
                'child_id'   => 2,
                'sort_order' => 5
            ],
            [
                'id'         => 5,
                'parent_id'  => null,
                'child_id'   => 3,
                'sort_order' => 1
            ],
            [
                'id'         => 7,
                'parent_id'  => 2,
                'child_id'   => 1,
                'sort_order' => 6
            ],
            [
                'id'         => 15,
                'parent_id'  => null,
                'child_id'   => 7,
                'sort_order' => 3
            ],
        ];

        $this->db->table('folder_links')->insertBatch($data);
    }
}
