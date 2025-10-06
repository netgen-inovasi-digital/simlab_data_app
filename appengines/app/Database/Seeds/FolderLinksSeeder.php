<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FolderLinksSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id'         => 1,
        'parent_id'  => null,
        'child_id'   => 1,
        'sort_order' => 1
      ],
      [
        'id'         => 2,
        'parent_id'  => null,
        'child_id'   => 2,
        'sort_order' => 2
      ],
      [
        'id'         => 3,
        'parent_id'  => null,
        'child_id'   => 3,
        'sort_order' => 3
      ],
      [
        'id'         => 4,
        'parent_id'  => null,
        'child_id'   => 4,
        'sort_order' => 4
      ],
      [
        'id'         => 5,
        'parent_id'  => null,
        'child_id'   => 5,
        'sort_order' => 5
      ],
    ];

    $this->db->table('folder_links')->insertBatch($data);
  }
}
