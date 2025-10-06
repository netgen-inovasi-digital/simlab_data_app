<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OtoritasFolderSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_otoritas' => 1,
        'id_role'  => 8,
        'id_folder' => 1,
        'can_view' => 1,
        'can_crud' => 1
      ],
      [
        'id_otoritas' => 2,
        'id_role'  => 8,
        'id_folder' => 2,
        'can_view' => 1,
        'can_crud' => 1
      ],
      [
        'id_otoritas' => 3,
        'id_role'  => 8,
        'id_folder' => 3,
        'can_view' => 1,
        'can_crud' => 1
      ],
      [
        'id_otoritas' => 4,
        'id_role'  => 8,
        'id_folder' => 4,
        'can_view' => 1,
        'can_crud' => 1
      ],
      [
        'id_otoritas' => 5,
        'id_role'  => 8,
        'id_folder' => 5,
        'can_view' => 1,
        'can_crud' => 1
      ],
    ];

    $this->db->table('otoritas_folder')->insertBatch($data);
  }
}
