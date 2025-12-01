<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user' => 11,
                'role_id' => 2,
                'nama' => 'solihin',
                'email' => 'kepalalab@gmail.com',
                'username' => 'kepalalab',
                'password' => '$2y$10$fY4ye1b5LxrPCETE9z2Hju9DNJuVnqZXRZ3mxgDXkwddIKXgnHeWe',
                'last_login' => '2025-11-13 18:24:44',
                'status_user' => 1,
                'alamat' => '0',
                'telepon' => '0',
                'foto' => 'profile_1762173177.png',
            ],
            [
                'id_user' => 12,
                'role_id' => 8,
                'nama' => 'Super Admin',
                'email' => 'alialfatih303@gmail.com',
                'username' => 'superadmin',
                'password' => '$2y$10$7qmnMG5YlxgW6RPaSZTmVupVuJfXo1bckyyVZdYFBss9V/zyQYjwK',
                'last_login' => '2025-12-01 09:38:56',
                'status_user' => 1,
                'alamat' => '0',
                'telepon' => '0',
                'foto' => '0',
            ],
            [
                'id_user' => 13,
                'role_id' => 9,
                'nama' => 'Asesor',
                'email' => 'asesor@gmail.com',
                'username' => 'asesor',
                'password' => '$2y$10$X3Ykzx.fTnl2lzRlH8eOo.k8cA08F4Ti9uePzZ2RAApwQA3BpVDS6',
                'last_login' => '2025-11-02 21:16:49',
                'status_user' => 1,
                'alamat' => null,
                'telepon' => null,
                'foto' => null,
            ],
            [
                'id_user' => 14,
                'role_id' => 10,
                'nama' => 'Tim Akreditasi',
                'email' => 'timakreditasi@gmail.com',
                'username' => 'timakreditasi',
                'password' => '$2y$10$CEcXgUDH53VQYz1lvjCLLOscsU/rHGWkp.htMCAYxbhy6QRctM2s6',
                'last_login' => '2025-11-02 21:17:04',
                'status_user' => 1,
                'alamat' => null,
                'telepon' => null,
                'foto' => null,
            ],
            [
                'id_user' => 15,
                'role_id' => 1,
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'username' => 'admin',
                'password' => '$2y$10$hC.WvAVkoaNyppMrg6bIbufv5m685tQPLE16D.ihHqGcZpKe3E7EO',
                'last_login' => null,
                'status_user' => 1,
                'alamat' => null,
                'telepon' => null,
                'foto' => null,
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
