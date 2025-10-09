<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DokumenSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_dokumen' => 1,
        'id_personel' => 49,
        'nama_asli_file' => '2 - Rekursif dan Looping.pdf',
        'nama_file_tersimpan' => '17597271417558ae7d18___2 - Rekursif dan Looping.pdf',
        'tipe_dokumen' => 'cv',
        'path_file' => 'uploads/17597271417558ae7d18___2 - Rekursif dan Looping.pdf',
        'created_at' => '2025-10-06 05:05:41',
      ],
    ];

    $this->db->table('dokumen')->insertBatch($data);
  }
}
