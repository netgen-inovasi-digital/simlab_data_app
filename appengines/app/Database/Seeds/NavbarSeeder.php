<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NavbarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_navbar'   => 11,
                'kode_navbar' => '2',
                'kode_induk'  => '0',
                'nama'        => 'News',
                'kategori'    => 'folder',
                'url'         => 'berita',
                'status'      => 'N',
                'sort_order'  => 2,
            ],
            [
                'id_navbar'   => 31,
                'kode_navbar' => '3',
                'kode_induk'  => '0',
                'nama'        => 'Youtube',
                'kategori'    => 'folder',
                'url'         => 'https://youtube.com/',
                'status'      => 'N',
                'sort_order'  => 3,
            ],
            [
                'id_navbar'   => 33,
                'kode_navbar' => '4',
                'kode_induk'  => '0',
                'nama'        => 'Layanan',
                'kategori'    => 'folder',
                'url'         => '#services',
                'status'      => 'Y',
                'sort_order'  => 4,
            ],
            [
                'id_navbar'   => 34,
                'kode_navbar' => '5',
                'kode_induk'  => '0',
                'nama'        => 'Team',
                'kategori'    => 'folder',
                'url'         => '#team',
                'status'      => 'N',
                'sort_order'  => 5,
            ],
            [
                'id_navbar'   => 35,
                'kode_navbar' => '6',
                'kode_induk'  => '0',
                'nama'        => 'Pengumuman',
                'kategori'    => 'folder',
                'url'         => '#notice',
                'status'      => 'Y',
                'sort_order'  => 6,
            ],
            [
                'id_navbar'   => 36,
                'kode_navbar' => '8',
                'kode_induk'  => '0',
                'nama'        => 'Mitra',
                'kategori'    => 'folder',
                'url'         => '#partner',
                'status'      => 'Y',
                'sort_order'  => 8,
            ],
            [
                'id_navbar'   => 38,
                'kode_navbar' => '7',
                'kode_induk'  => '0',
                'nama'        => 'Berita',
                'kategori'    => 'folder',
                'url'         => '#news',
                'status'      => 'Y',
                'sort_order'  => 7,
            ],
            [
                'id_navbar'   => 43,
                'kode_navbar' => '9',
                'kode_induk'  => '0',
                'nama'        => 'Profil',
                'kategori'    => null,
                'url'         => 'hal/profil',
                'status'      => null,
                'sort_order'  => 0,
            ],
            [
                'id_navbar'   => 46,
                'kode_navbar' => '9',
                'kode_induk'  => '0',
                'nama'        => 'Profil',
                'kategori'    => null,
                'url'         => 'hal/profil',
                'status'      => null,
                'sort_order'  => 0,
            ]
        ];

        // Insert batch
        $this->db->table('navbar')->insertBatch($data);
    }
}
