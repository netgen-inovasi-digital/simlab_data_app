<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OtoritasFileSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_otoritas' => 1,
        'id_role'  => 8,
        'id_file' => 1,
        'can_view' => 1,
        'can_crud' => 1
      ],
      [
        'id_otoritas' => 2,
        'id_role'  => 8,
        'id_file' => 2,
        'can_view' => 1,
        'can_crud' => 1
      ],
    ];

    $this->db->table('otoritas_file')->insertBatch($data);
  }
}
