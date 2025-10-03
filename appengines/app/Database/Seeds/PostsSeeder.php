<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PostsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_posts'     => 55,
                'categories_id'=> 35,
                'user_id'      => 1,
                'title'        => 'Peran Gizi Seimbang dalam Menjaga Daya Tahan Tubuh',
                'slug'         => 'peran-gizi-seimbang-dalam-menjaga-daya-tahan-tubuh',
                'konten'       => '<p>Tubuh memerlukan nutrisi lengkap untuk berfungsi optimal. Gizi seimbang mencakup karbohidrat, protein, lemak sehat, vitamin, dan mineral. Sayur dan buah memberikan serat serta antioksidan, sementara protein membantu membangun dan memperbaiki jaringan. Mengonsumsi makanan olahan secara berlebihan dapat menurunkan imunitas. Cobalah makan dengan porsi seimbang dan utamakan bahan makanan segar.</p>',
                'excerpt'      => 'Tubuh memerlukan nutrisi lengkap untuk berfungsi optima...',
                'thumbnail'    => '17508684608c4aef997b.jpg',
                'status'       => 'publish',
                'created_at'   => '2025-06-25 00:00:00',
                'updated_at'   => '2025-06-23 00:00:00',
                'published_at' => '2025-06-23 00:00:00',
                'views'        => 2,
            ],
            [
                'id_posts'     => 63,
                'categories_id'=> 35,
                'user_id'      => 1,
                'title'        => 'Pentingnya Tidur Cukup untuk Kesehatan Tubuh dan Mental',
                'slug'         => 'pentingnya-tidur-cukup-untuk-kesehatan-tubuh-dan-mental',
                'konten'       => '<p>Tidur bukan sekadar istirahat â€” ini adalah kebutuhan dasar tubuh untuk memperbaiki dan memulihkan fungsi fisik serta mental. Kurang tidur dapat menyebabkan penurunan daya konsentrasi, gangguan suasana hati, dan penurunan sistem imun. Orang dewasa disarankan tidur 7â€“9 jam per malam. Untuk meningkatkan kualitas tidur, hindari layar sebelum tidur, jaga jadwal tidur yang konsisten, dan ciptakan lingkungan tidur yang nyaman dan gelap.</p>',
                'excerpt'      => 'Tidur bukan sekadar istirahat â€” ini adalah kebutuhan ...',
                'thumbnail'    => '17508752556585a56e62.jpg',
                'status'       => 'publish',
                'created_at'   => '2025-06-25 00:00:00',
                'updated_at'   => '2025-06-22 00:00:00',
                'published_at' => '2025-06-22 00:00:00',
                'views'        => 2,
            ],
            [
                'id_posts'     => 70,
                'categories_id'=> 49,
                'user_id'      => 12,
                'title'        => 'Ecomel Resmi Diluncurkan: Platform Belanja Digital Baru untuk Generasi Cerdas dan Hemat',
                'slug'         => 'ecomel-resmi-diluncurkan-platform-belanja-digital-baru-untuk-generasi-cerdas-dan-hemat',
                'konten'       => '<p>y</p>',
                'excerpt'      => 'y',
                'thumbnail'    => '17556076449cf37f11bf.png',
                'status'       => 'publish',
                'created_at'   => '2025-08-19 15:35:46',
                'updated_at'   => '2025-08-19 20:47:24',
                'published_at' => '2025-08-19 00:00:00',
                'views'        => NULL,
            ],
        ];

        $this->db->table('posts')->insertBatch($data);
    }
}
