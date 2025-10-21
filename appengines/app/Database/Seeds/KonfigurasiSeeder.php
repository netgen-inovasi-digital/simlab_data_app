<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id_konfigurasi'                => 1,
            'email'                          => 'ulmsimlab@gmail.com',
        ];

        $this->db->table('konfigurasi')->insert($data);
    }
}
