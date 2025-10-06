<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FilesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_files'      => 1,
                'categories_id' => 68,
                'user_id'       => 12,
                'id_folder'     => 3,
                'nomor_dokumen' => 'LPDDR29',
                'title'         => 'hehhehehe',
                'slug'          => 'hehhehehe',
                'berkas'        => '1758267754670b248a56.pdf',
                'revisi'        => 1,
                'created_at'    => '2025-09-19 00:00:00',
                'updated_at'    => '2025-09-19 15:42:34',
            ],
            [
                'id_files'      => 2,
                'categories_id' => 69,
                'user_id'       => 12,
                'id_folder'     => 4,
                'nomor_dokumen' => 'LPDDR20',
                'title'         => 'Gacor fix sih ini Anjay',
                'slug'          => 'gacor-fix-sih-ini-anjay',
                'berkas'        => '175826954466fec0aac6.pdf', 
                'revisi'        => 7,
                'created_at'    => '2025-09-19 00:00:00',
                'updated_at'    => '2025-09-19 16:12:24',
            ]
        ];

        // Insert batch
        $this->db->table('files')->insertBatch($data);
    }
}
