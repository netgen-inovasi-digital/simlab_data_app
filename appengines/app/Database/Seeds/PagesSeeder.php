<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PagesSeeder extends Seeder
{
  public function run()
  {
    $data = [
      'id_pages'    => 60,
      'user_id'     => 1,
      'title'       => 'Profil',
      'slug'        => 'profil',
      'konten'      => '<p><strong>Ecomel</strong> adalah platform e-commerce yang hadir untuk menghadirkan pengalaman belanja digital yang mudah, aman, dan memberdayakan. Dibangun dengan semangat lokal dan inovasi teknologi, Ecomel menghubungkan pelanggan dengan berbagai produk berkualitas dari seluruh Indonesia, sekaligus menjadi rumah digital bagi pelaku UMKM untuk tumbuh bersama.</p><h3>💡 Visi</h3><p><strong>Menjadi platform e-commerce terpercaya yang menghubungkan masyarakat Indonesia dengan produk berkualitas melalui teknologi yang sederhana dan inklusif.</strong></p><h3>🎯 Misi</h3><ol><li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>Memberikan pengalaman belanja online yang praktis, cepat, dan menyenangkan.</li><li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>Mendukung pertumbuhan UMKM dan produk lokal melalui teknologi digital.</li><li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>Menyediakan sistem pembayaran dan pengiriman yang aman, transparan, dan efisien.</li><li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>Menjadi mitra strategis bagi pengguna, mitra usaha, dan komunitas digital.</li></ol><h3>🌱 Nilai-Nilai Kami</h3><ol><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Integritas</strong> – Kami menjaga kepercayaan pelanggan dan mitra dengan transparansi dan tanggung jawab.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Inovasi</strong> – Kami terus berkembang dan berinovasi untuk menciptakan solusi belanja yang lebih baik.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Kebermanfaatan</strong> – Kami percaya bahwa teknologi harus memberi dampak positif bagi masyarakat.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Kebersamaan</strong> – Kami tumbuh bersama pelanggan dan pelaku usaha dalam semangat kolaborasi.</li></ol><h3>🔍 Apa yang Membuat Ecomel Berbeda?</h3><ol><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Fokus pada Produk Lokal:</strong> Kami memprioritaskan brand dan usaha lokal untuk menjangkau pasar lebih luas.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>UI/UX Sederhana &amp; Ringan:</strong> Desain aplikasi kami dibuat untuk semua kalangan, bahkan yang baru pertama kali belanja online.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Layanan Pelanggan Responsif:</strong> Tim kami siap membantu melalui berbagai kanal dengan cepat dan ramah.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><strong>Promo dan Program Loyalitas:</strong> Kami menghadirkan promo menarik setiap hari dan sistem poin belanja yang menguntungkan.</li></ol><h3>📍 Lokasi Kantor</h3><p>Jl. Bhayangkara, Kel. Sungai Besar, Banjarbaru Selatan,</p><p> Kota Banjarbaru, Kalimantan Selatan 70714</p><p> 📧 Email: info@ecomel.id</p><p> 📞 Telepon: 08xx-xxxx-xxxx</p>',
      'status'      => 'publish',
      'created_at'  => '2025-06-29 00:00:00',
      'updated_at'  => '2025-06-29 00:00:00',
      'published_at' => '2025-06-29 00:00:00',
      'views'       => 21,
    ];

    $this->db->table('pages')->insert($data);
  }
}
