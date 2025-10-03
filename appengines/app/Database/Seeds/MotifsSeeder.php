<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MotifsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id'        => 1,
            'name'      => 'Dragon',
            'deskripsi' => 'Dragon adalah naga',
            'foto'      => '1754487655f4c01333af.jpg',
        ];

        // Insert data ke tabel motifs
        $this->db->table('motifs')->insert($data);
    }
}
