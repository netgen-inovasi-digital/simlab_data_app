<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_layanan' => 22,
                'judul'      => 'Produk Ramah Lingkungan',
                'deskripsi'  => 'Semua produk di Ecomel telah dikurasi untuk mendukung gaya hidup berkelanjutan, bebas dari bahan berbahaya dan lebih aman untuk bumi.',
                'foto'       => '1753239440dbd259a32f.png',
                'urutan'     => 1,
                'status'     => 'Y',
                'link'       => '',
            ],
            [
                'id_layanan' => 23,
                'judul'      => 'Pilihan Produk Berkualitas',
                'deskripsi'  => 'Ecomel menghadirkan produk dari brand terpercaya yang mengutamakan kualitas, keamanan, dan etika produksi.',
                'foto'       => '175323955772941de000.png',
                'urutan'     => 2,
                'status'     => 'Y',
                'link'       => '',
            ],
            [
                'id_layanan' => 24,
                'judul'      => 'Pengiriman Cepat & Aman',
                'deskripsi'  => 'Didukung oleh sistem logistik terpercaya, Ecomel memastikan barang sampai tepat waktu dalam kondisi terbaik.',
                'foto'       => '175323964063bf5b939e.png',
                'urutan'     => 3,
                'status'     => 'Y',
                'link'       => '',
            ],
            [
                'id_layanan' => 25,
                'judul'      => 'Dukungan untuk UMKM Lokal',
                'deskripsi'  => 'Dengan berbelanja di Ecomel, kamu turut mendukung para pelaku UMKM lokal yang bergerak di bidang produk ramah lingkungan.',
                'foto'       => '1753239750a1ff7e9ba5.png',
                'urutan'     => 4,
                'status'     => 'Y',
                'link'       => '',
            ],
            [
                'id_layanan' => 26,
                'judul'      => 'Beragam Metode Pembayaran',
                'deskripsi'  => 'Nikmati transaksi mudah dan aman dengan berbagai metode pembayaran, termasuk e-wallet dan transfer bank.',
                'foto'       => '1753239818ddf5aa910f.png',
                'urutan'     => 5,
                'status'     => 'Y',
                'link'       => '',
            ],
        ];

        // Insert batch ke tabel layanan
        $this->db->table('layanan')->insertBatch($data);
    }
}
