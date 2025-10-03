<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MitraSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_mitra' => 14,
                'nama' => 'BIMA',
                'foto' => '175293305338af992638.png',
                'urutan' => 3,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 16,
                'nama' => 'Tut Wuri Handayani',
                'foto' => '17529330137b4677912f.png',
                'urutan' => 1,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 19,
                'nama' => 'LPPM',
                'foto' => '1752933028429a38c250.png',
                'urutan' => 2,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 20,
                'nama' => 'DIKTISAINTEK BERDAMPAK',
                'foto' => '1752933097403cb1c616.png',
                'urutan' => 4,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 23,
                'nama' => 'Tut Wuri Handayani',
                'foto' => '175293315576c3452791.png',
                'urutan' => 5,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 24,
                'nama' => 'LPPM',
                'foto' => '1752933169766c556b5d.png',
                'urutan' => 6,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 25,
                'nama' => 'BIMA',
                'foto' => '1752933187684626179c.png',
                'urutan' => 7,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 26,
                'nama' => 'DIKTISAINTEK BERDAMPAK',
                'foto' => '17529332156624c13061.png',
                'urutan' => 8,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 27,
                'nama' => 'Tut Wuri Handayani',
                'foto' => '1752933992112ee8c1dc.png',
                'urutan' => 9,
                'status' => 'Y',
            ],
            [
                'id_mitra' => 28,
                'nama' => 'LPPM',
                'foto' => '1752934005f52689f1b2.png',
                'urutan' => 10,
                'status' => 'Y',
            ],
        ];

        // Insert batch
        $this->db->table('mitra')->insertBatch($data);
    }
}
