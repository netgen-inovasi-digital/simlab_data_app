<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HeroSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_hero'    => 3,
                'judul'      => 'Selamat Datang di Ecomel',
                'deskripsi'  => 'Temukan kemudahan berbelanja online dengan pilihan produk terbaik dan harga bersahabat hanya di Ecomel.',
                'foto'       => '1752649616757b72ad46.jpg',
                'urutan'     => 2,
                'status'     => 'Y',
            ],
            [
                'id_hero'    => 4,
                'judul'      => 'Promo Spesial Setiap Hari!',
                'deskripsi'  => 'Nikmati potongan harga menarik untuk berbagai kebutuhan—dari fashion hingga kebutuhan rumah tangga.',
                'foto'       => '1752649638665a712824.jpg',
                'urutan'     => 1,
                'status'     => 'Y',
            ],
            [
                'id_hero'    => 5,
                'judul'      => 'Dukung Produk Lokal',
                'deskripsi'  => 'Belanja sambil berdampak! Temukan dan dukung UMKM lokal lewat produk-produk berkualitas pilihan.',
                'foto'       => '1752649663f5ccb58ac3.jpg',
                'urutan'     => 3,
                'status'     => 'Y',
            ],
            [
                'id_hero'    => 6,
                'judul'      => 'Kirim Cepat, Sampai Tepat',
                'deskripsi'  => 'Kami pastikan pesananmu dikirim dengan aman dan cepat ke seluruh Indonesia. Belanja tanpa khawatir.',
                'foto'       => '17526496951cafae734c.jpg',
                'urutan'     => 4,
                'status'     => 'Y',
            ],
        ];

        $this->db->table('hero')->insertBatch($data);
    }
}
