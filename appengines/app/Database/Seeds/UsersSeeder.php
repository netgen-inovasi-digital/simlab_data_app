<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_user'     => 1,
        'role_id'     => 1,
        'nama'        => 'admin',
        'email'       => 'admin@gmail.com',
        'username'    => 'admin',
        'password'    => '$2y$10$xyGL25XKYGT5.ZRrLDuqm.WqYWAAXkf2v9gQ4dBDGBKY9kP3Z/WUe',
        'last_login'  => '2025-09-03 22:01:06',
        'status_user' => 1,
        'alamat'      => null,
        'telepon'     => null,
        'foto'        => null,
      ],
      [
        'id_user'     => 2,
        'role_id'     => 2,
        'nama'        => 'Kepala Lab',
        'email'       => 'kepalalab@gmail.com',
        'username'    => 'kepalalab',
        'password'    => '$2y$10$fY4ye1b5LxrPCETE9z2Hju9DNJuVnqZXRZ3mxgDXkwddIKXgnHeWe',
        'last_login'  => '2025-09-03 21:21:47',
        'status_user' => 1,
        'alamat'      => null,
        'telepon'     => null,
        'foto'        => null,
      ],
      [
        'id_user'     => 3,
        'role_id'     => 8,
        'nama'        => 'Super Admin',
        'email'       => 'alialfatih303@gmail.com',
        'username'    => 'superadmin',
        'password'    => '$2y$10$7qmnMG5YlxgW6RPaSZTmVupVuJfXo1bckyyVZdYFBss9V/zyQYjwK',
        'last_login'  => '2025-09-20 13:06:18',
        'status_user' => 1,
        'alamat'      => null,
        'telepon'     => null,
        'foto'        => null,
      ],
      [
        'id_user'     => 4,
        'role_id'     => 9,
        'nama'        => 'Asesor',
        'email'       => 'asesor@gmail.com',
        'username'    => 'asesor',
        'password'    => '$2y$10$7qmnMG5YlxgW6RPaSZTmVupVuJfXo1bckyyVZdYFBss9V/zyQYjwK',
        'last_login'  => '2025-09-21 13:06:18',
        'status_user' => 1,
        'alamat'      => null,
        'telepon'     => null,
        'foto'        => null,
      ],
      [
        'id_user'     => 5,
        'role_id'     => 10,
        'nama'        => 'Tim Akreditasi',
        'email'       => 'timakreditasi@gmail.com',
        'username'    => 'timakreditasi',
        'password'    => '$2y$10$7qmnMG5YlxgW6RPaSZTmVupVuJfXo1bckyyVZdYFBss9V/zyQYjwK',
        'last_login'  => '2025-09-22 13:06:18',
        'status_user' => 1,
        'alamat'      => null,
        'telepon'     => null,
        'foto'        => null,
      ],
      [
        'id_user'     => 6,
        'role_id'     => 11,
        'nama'        => 'Personel',
        'email'       => 'personel@gmail.com',
        'username'    => 'personel',
        'password'    => '$2y$10$7qmnMG5YlxgW6RPaSZTmVupVuJfXo1bckyyVZdYFBss9V/zyQYjwK',
        'last_login'  => '2025-09-22 13:06:18',
        'status_user' => 1,
        'alamat'      => null,
        'telepon'     => null,
        'foto'        => null,
      ]
    ];

    $this->db->table('users')->insertBatch($data);
  }
}
