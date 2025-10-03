<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_pengumuman' => 2,
                'user_id'       => 1,
                'judul'         => 'Pengumuman Maintenance Sistem Ecomel',
                'deskripsi'     => "Halo, Sahabat Ecomel!\r\nKami akan melakukan maintenance sistem untuk peningkatan layanan pada:\r\n\r\n🗓️ Tanggal: Kamis, 18 Juli 2025\r\n⏰ Waktu: Pukul 23.00 – 03.00 WITA",
                'status'        => 'tampil',
                'tanggal'       => '2025-06-25',
            ],
            [
                'id_pengumuman' => 3,
                'user_id'       => 1,
                'judul'         => 'Pemberitahuan Keterlambatan Pengiriman',
                'deskripsi'     => 'Kami informasikan bahwa terjadi gangguan distribusi akibat cuaca ekstrem di beberapa wilayah Kalimantan dan Sulawesi. Hal ini dapat menyebabkan keterlambatan pengiriman 1–3 hari dari estimasi awal.',
                'status'        => 'tampil',
                'tanggal'       => '2025-06-25',
            ],
        ];

        // Insert batch data
        $this->db->table('pengumuman')->insertBatch($data);
    }
}
