<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LayoutSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_layout'      => 1,
                'kode'           => 'hero',
                'html_section'   => "layout html\n",
                'konten_dinamis' => json_encode([
                    'judul' => 'Slider',
                    'deskripsi' => null
                ]),
                'urutan'         => 1,
                'status'         => 'Y'
            ],
            [
                'id_layout'      => 2,
                'kode'           => 'layanan',
                'html_section'   => 'layout html',
                'konten_dinamis' => json_encode([
                    'judul' => 'Layanan',
                    'deskripsi' => 'Kami memiliki keunggulan dalam pelayanan untuk memenuhi kebutuhan Anda dan keluarga.'
                ]),
                'urutan'         => 2,
                'status'         => 'Y'
            ],
            [
                'id_layout'      => 3,
                'kode'           => 'team',
                'html_section'   => 'layout html',
                'konten_dinamis' => json_encode([
                    'judul' => 'Team',
                    'deskripsi' => 'Berikut adalah daftar tim Ecomel'
                ]),
                'urutan'         => 3,
                'status'         => 'N'
            ],
            [
                'id_layout'      => 4,
                'kode'           => 'mitra',
                'html_section'   => 'layout html',
                'konten_dinamis' => json_encode([
                    'judul' => 'Mitra dan Partner Kami',
                    'deskripsi' => 'Kami bekerja sama dengan berbagai institusi terpercaya untuk mendukung layanan terbaik.'
                ]),
                'urutan'         => 6,
                'status'         => 'Y'
            ],
            [
                'id_layout'      => 5,
                'kode'           => 'berita',
                'html_section'   => 'layout html',
                'konten_dinamis' => json_encode([
                    'judul' => 'Berita / Event',
                    'deskripsi' => 'Kami menyediakan berita terbaru tentang Ecomel'
                ]),
                'urutan'         => 4,
                'status'         => 'Y'
            ],
            [
                'id_layout'      => 6,
                'kode'           => 'pengumuman',
                'html_section'   => "layout html\n",
                'konten_dinamis' => json_encode([
                    'judul' => 'Pengumuman',
                    'deskripsi' => null
                ]),
                'urutan'         => 5,
                'status'         => 'Y'
            ]
        ];

        $this->db->table('layout')->insertBatch($data);
    }
}
