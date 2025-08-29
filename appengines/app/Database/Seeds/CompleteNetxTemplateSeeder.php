<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompleteNetxTemplateSeeder extends Seeder
{
    public function run()
    {
        // 1. Insert roles data
        $this->db->table('roles')->insertBatch([
            ['id_role' => 1, 'nama_role' => 'Admin', 'grup' => 'admin', 'status_role' => 1],
            ['id_role' => 2, 'nama_role' => 'User', 'grup' => 'author', 'status_role' => 1],
            ['id_role' => 8, 'nama_role' => 'Super Admin', 'grup' => null, 'status_role' => 1],
        ]);

        // 2. Insert users data (exact from SQL)
        $this->db->table('users')->insertBatch([
            [
                'id_user' => 1,
                'role_id' => 1,
                'nama' => 'admin',
                'email' => 'admin@gmail.com',
                'username' => 'admin',
                'password' => '$2y$10$xyGL25XKYGT5.ZRrLDuqm.WqYWAAXkf2v9gQ4dBDGBKY9kP3Z/WUe',
                'last_login' => '2025-08-26 22:05:00',
                'status_user' => 1,
                'alamat' => null,
                'telepon' => null,
                'foto' => null
            ],
            [
                'id_user' => 11,
                'role_id' => 2,
                'nama' => 'user',
                'email' => 'user@gmail.com',
                'username' => 'user',
                'password' => '$2y$10$fY4ye1b5LxrPCETE9z2Hju9DNJuVnqZXRZ3mxgDXkwddIKXgnHeWe',
                'last_login' => '2025-08-11 22:28:03',
                'status_user' => 1,
                'alamat' => '0',
                'telepon' => '0',
                'foto' => '0'
            ],
            [
                'id_user' => 12,
                'role_id' => 8,
                'nama' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'username' => 'superadmin',
                'password' => '$2y$10$7qmnMG5YlxgW6RPaSZTmVupVuJfXo1bckyyVZdYFBss9V/zyQYjwK',
                'last_login' => '2025-08-26 22:14:25',
                'status_user' => 1,
                'alamat' => '0',
                'telepon' => '0',
                'foto' => '0'
            ],
        ]);

        // 3. Insert categories data (exact from SQL)
        $this->db->table('categories')->insertBatch([
            ['id_categories' => 46, 'nama' => 'Umum', 'slug' => 'umum', 'created_at' => '2025-07-16 07:13:19'],
            ['id_categories' => 49, 'nama' => 'dokumen A', 'slug' => 'dokumen-a', 'created_at' => '2025-08-11 22:00:40'],
        ]);

        // 4. Insert hero data (exact from SQL)
        $this->db->table('hero')->insertBatch([
            [
                'id_hero' => 3,
                'judul' => 'Selamat Datang di Ecomel',
                'deskripsi' => 'Temukan kemudahan berbelanja online dengan pilihan produk terbaik dan harga bersahabat hanya di Ecomel.',
                'foto' => '1752649616757b72ad46.jpg',
                'urutan' => 2,
                'status' => 'Y'
            ],
            [
                'id_hero' => 4,
                'judul' => 'Promo Spesial Setiap Hari!',
                'deskripsi' => 'Nikmati potongan harga menarik untuk berbagai kebutuhan—dari fashion hingga kebutuhan rumah tangga.',
                'foto' => '1752649638665a712824.jpg',
                'urutan' => 1,
                'status' => 'Y'
            ],
            [
                'id_hero' => 5,
                'judul' => 'Dukung Produk Lokal',
                'deskripsi' => 'Belanja sambil berdampak! Temukan dan dukung UMKM lokal lewat produk-produk berkualitas pilihan.',
                'foto' => '1752649663f5ccb58ac3.jpg',
                'urutan' => 3,
                'status' => 'Y'
            ],
            [
                'id_hero' => 6,
                'judul' => 'Kirim Cepat, Sampai Tepat',
                'deskripsi' => 'Kami pastikan pesananmu dikirim dengan aman dan cepat ke seluruh Indonesia. Belanja tanpa khawatir.',
                'foto' => '17526496951cafae734c.jpg',
                'urutan' => 4,
                'status' => 'Y'
            ],
        ]);

        // 5. Insert konfigurasi data (exact from SQL)
        $this->db->table('konfigurasi')->insert([
            'id_konfigurasi' => 1,
            'nama_profil' => 'Netx Template',
            'deskripsi' => 'NetX Template adalah sebuah starter template engine berbasis CodeIgniter 4 (CI4) yang dirancang untuk memudahkan pengembangan website dengan struktur yang rapi, modular, dan siap pakai.',
            'alamat' => 'Kota Banjarbaru, Kalimantan Selatan',
            'telepon' => '083159236448',
            'email' => 'netgen.id@gmail.com',
            'kota' => 'Banjarbaru',
            'provinsi' => 'Kalimantan Selatan',
            'logo' => '1754489500f9b1f5b62d.jpg',
            'peta' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.668147624301!2d114.8010200744995!3d-3.4307116417292853!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de683004aea87fd%3A0x908679b896616ec2!2sKlinik%20dan%20Apotek%20Medikidz!5e0!3m2!1sen!2sid!4v1751200717274!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'link' => 'profil'
        ]);

        // 6. Insert layanan data (exact from SQL)
        $this->db->table('layanan')->insertBatch([
            [
                'id_layanan' => 22,
                'judul' => 'Produk Ramah Lingkungan',
                'deskripsi' => 'Semua produk di Ecomel telah dikurasi untuk mendukung gaya hidup berkelanjutan, bebas dari bahan berbahaya dan lebih aman untuk bumi.',
                'foto' => '1753239440dbd259a32f.png',
                'urutan' => 1,
                'status' => 'Y',
                'link' => ''
            ],
            [
                'id_layanan' => 23,
                'judul' => 'Pilihan Produk Berkualitas',
                'deskripsi' => 'Ecomel menghadirkan produk dari brand terpercaya yang mengutamakan kualitas, keamanan, dan etika produksi.',
                'foto' => '175323955772941de000.png',
                'urutan' => 2,
                'status' => 'Y',
                'link' => ''
            ],
            [
                'id_layanan' => 24,
                'judul' => 'Pengiriman Cepat & Aman',
                'deskripsi' => 'Didukung oleh sistem logistik terpercaya, Ecomel memastikan barang sampai tepat waktu dalam kondisi terbaik.',
                'foto' => '175323964063bf5b939e.png',
                'urutan' => 3,
                'status' => 'Y',
                'link' => ''
            ],
            [
                'id_layanan' => 25,
                'judul' => 'Dukungan untuk UMKM Lokal',
                'deskripsi' => 'Dengan berbelanja di Ecomel, kamu turut mendukung para pelaku UMKM lokal yang bergerak di bidang produk ramah lingkungan.',
                'foto' => '1753239750a1ff7e9ba5.png',
                'urutan' => 4,
                'status' => 'Y',
                'link' => ''
            ],
            [
                'id_layanan' => 26,
                'judul' => 'Beragam Metode Pembayaran',
                'deskripsi' => 'Nikmati transaksi mudah dan aman dengan berbagai metode pembayaran, termasuk e-wallet dan transfer bank.',
                'foto' => '1753239818ddf5aa910f.png',
                'urutan' => 5,
                'status' => 'Y',
                'link' => ''
            ],
        ]);

        // 7. Insert menus data (exact from SQL)
        $this->db->table('menus')->insertBatch([
            ['id_menu' => 6, 'kode_menu' => '1', 'kode_induk' => '0', 'nama' => 'Dashboard', 'link' => 'dashboard/load', 'icon' => 'bi-house', 'sort_order' => 1],
            ['id_menu' => 9, 'kode_menu' => '3', 'kode_induk' => '0', 'nama' => 'Pengumuman', 'link' => 'pengumuman', 'icon' => 'bi-megaphone', 'sort_order' => 3],
            ['id_menu' => 11, 'kode_menu' => '6', 'kode_induk' => '0', 'nama' => 'Tampilan', 'link' => '#', 'icon' => 'bi-display', 'sort_order' => 6],
            ['id_menu' => 12, 'kode_menu' => '6.1', 'kode_induk' => '6', 'nama' => 'Menu', 'link' => 'navbar', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 7],
            ['id_menu' => 13, 'kode_menu' => '6.4', 'kode_induk' => '6', 'nama' => 'Slider', 'link' => 'hero', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 10],
            ['id_menu' => 14, 'kode_menu' => '6.2', 'kode_induk' => '6', 'nama' => 'Layanan', 'link' => 'layanan', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 8],
            ['id_menu' => 15, 'kode_menu' => '6.5', 'kode_induk' => '6', 'nama' => 'Informasi', 'link' => 'konfigurasi', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 11],
            ['id_menu' => 16, 'kode_menu' => '6.6', 'kode_induk' => '6', 'nama' => 'Sosial', 'link' => 'sosmed', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 12],
            ['id_menu' => 31, 'kode_menu' => '6.7', 'kode_induk' => '6', 'nama' => 'Mitra', 'link' => 'mitra', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 13],
            ['id_menu' => 17, 'kode_menu' => '7', 'kode_induk' => '0', 'nama' => 'Urutan Tampilan', 'link' => 'layout', 'icon' => 'bi-layout-text-window', 'sort_order' => 14],
            ['id_menu' => 18, 'kode_menu' => '9', 'kode_induk' => '0', 'nama' => 'Pengaturan', 'link' => '#', 'icon' => 'bi-gear', 'sort_order' => 18],
            ['id_menu' => 19, 'kode_menu' => '9.1', 'kode_induk' => '9', 'nama' => 'Pengguna', 'link' => 'user', 'icon' => 'bi-person', 'sort_order' => 19],
            ['id_menu' => 20, 'kode_menu' => '9.3', 'kode_induk' => '9', 'nama' => 'Role', 'link' => 'role', 'icon' => 'bi-shield-lock', 'sort_order' => 20],
            ['id_menu' => 21, 'kode_menu' => '9.2', 'kode_induk' => '9', 'nama' => 'Otoritas', 'link' => 'otoritas', 'icon' => 'bi-shield-check', 'sort_order' => 21],
            ['id_menu' => 23, 'kode_menu' => '9.4', 'kode_induk' => '9', 'nama' => 'Menu ', 'link' => 'menu', 'icon' => 'bi-people', 'sort_order' => 22],
            ['id_menu' => 24, 'kode_menu' => '2', 'kode_induk' => '0', 'nama' => 'Berita', 'link' => 'posts', 'icon' => 'bi-newspaper', 'sort_order' => 2],
            ['id_menu' => 28, 'kode_menu' => '6.3', 'kode_induk' => '6', 'nama' => 'Team', 'link' => 'team', 'icon' => 'bi-three-dots-vertical', 'sort_order' => 9],
            ['id_menu' => 29, 'kode_menu' => '8', 'kode_induk' => '0', 'nama' => 'Data Master', 'link' => '#', 'icon' => 'bi bi-box', 'sort_order' => 16],
            ['id_menu' => 30, 'kode_menu' => '8.1', 'kode_induk' => '8', 'nama' => 'Motif', 'link' => 'motif', 'icon' => 'bi bi-receipt', 'sort_order' => 17],
        ]);

        // 8. Insert mitra data (exact from SQL)
        $this->db->table('mitra')->insertBatch([
            ['id_mitra' => 14, 'nama' => 'BIMA', 'foto' => '175293305338af992638.png', 'urutan' => 3, 'status' => 'Y'],
            ['id_mitra' => 16, 'nama' => 'Tut Wuri Handayani', 'foto' => '17529330137b4677912f.png', 'urutan' => 1, 'status' => 'Y'],
            ['id_mitra' => 19, 'nama' => 'LPPM', 'foto' => '1752933028429a38c250.png', 'urutan' => 2, 'status' => 'Y'],
            ['id_mitra' => 20, 'nama' => 'DIKTISAINTEK BERDAMPAK', 'foto' => '1752933097403cb1c616.png', 'urutan' => 4, 'status' => 'Y'],
            ['id_mitra' => 23, 'nama' => 'Tut Wuri Handayani', 'foto' => '175293315576c3452791.png', 'urutan' => 5, 'status' => 'Y'],
            ['id_mitra' => 24, 'nama' => 'LPPM', 'foto' => '1752933169766c556b5d.png', 'urutan' => 6, 'status' => 'Y'],
            ['id_mitra' => 25, 'nama' => 'BIMA', 'foto' => '1752933187684626179c.png', 'urutan' => 7, 'status' => 'Y'],
            ['id_mitra' => 26, 'nama' => 'DIKTISAINTEK BERDAMPAK', 'foto' => '17529332156624c13061.png', 'urutan' => 8, 'status' => 'Y'],
            ['id_mitra' => 27, 'nama' => 'Tut Wuri Handayani', 'foto' => '1752933992112ee8c1dc.png', 'urutan' => 9, 'status' => 'Y'],
            ['id_mitra' => 28, 'nama' => 'LPPM', 'foto' => '1752934005f52689f1b2.png', 'urutan' => 10, 'status' => 'Y'],
        ]);

        // 9. Insert motifs data (exact from SQL)
        $this->db->table('motifs')->insert([
            'id' => 1,
            'name' => 'Dragon',
            'deskripsi' => 'Dragon adalah naga',
            'foto' => '1754487655f4c01333af.jpg'
        ]);

        // 10. Insert navbar data (exact from SQL)
        $this->db->table('navbar')->insertBatch([
            ['id_navbar' => 11, 'kode_navbar' => '1', 'kode_induk' => '0', 'nama' => 'News', 'url' => 'berita', 'status' => 'N', 'sort_order' => 1],
            ['id_navbar' => 31, 'kode_navbar' => '2', 'kode_induk' => '0', 'nama' => 'Youtube', 'url' => 'https://youtube.com/', 'status' => 'N', 'sort_order' => 2],
            ['id_navbar' => 33, 'kode_navbar' => '4', 'kode_induk' => '0', 'nama' => 'Layanan', 'url' => '#services', 'status' => 'Y', 'sort_order' => 4],
            ['id_navbar' => 34, 'kode_navbar' => '5', 'kode_induk' => '0', 'nama' => 'Team', 'url' => '#team', 'status' => 'N', 'sort_order' => 5],
            ['id_navbar' => 35, 'kode_navbar' => '6', 'kode_induk' => '0', 'nama' => 'Pengumuman', 'url' => '#notice', 'status' => 'Y', 'sort_order' => 6],
            ['id_navbar' => 36, 'kode_navbar' => '8', 'kode_induk' => '0', 'nama' => 'Mitra', 'url' => '#partner', 'status' => 'Y', 'sort_order' => 8],
            ['id_navbar' => 38, 'kode_navbar' => '7', 'kode_induk' => '0', 'nama' => 'Berita', 'url' => '#news', 'status' => 'Y', 'sort_order' => 7],
            ['id_navbar' => 40, 'kode_navbar' => '3', 'kode_induk' => '0', 'nama' => 'Profil', 'url' => 'hal/profil', 'status' => 'Y', 'sort_order' => 3],
        ]);

// 11. Insert otoritas data (exact from SQL - first 42 records)
$this->db->table('otoritas')->insertBatch([
    ['id_otoritas' => 1, 'role_id' => 1, 'kode_menu' => '3', 'status_otoritas' => 1],
    ['id_otoritas' => 2, 'role_id' => 1, 'kode_menu' => '1', 'status_otoritas' => 1],
    ['id_otoritas' => 3, 'role_id' => 1, 'kode_menu' => '4', 'status_otoritas' => 0],
    ['id_otoritas' => 4, 'role_id' => 1, 'kode_menu' => '5', 'status_otoritas' => 0],
    ['id_otoritas' => 5, 'role_id' => 1, 'kode_menu' => '6', 'status_otoritas' => 1],
    ['id_otoritas' => 6, 'role_id' => 1, 'kode_menu' => '7', 'status_otoritas' => 1],
    ['id_otoritas' => 7, 'role_id' => 1, 'kode_menu' => '2', 'status_otoritas' => 1],
    ['id_otoritas' => 8, 'role_id' => 1, 'kode_menu' => '6.1', 'status_otoritas' => 1],
    ['id_otoritas' => 9, 'role_id' => 1, 'kode_menu' => '6.2', 'status_otoritas' => 1],
    ['id_otoritas' => 10, 'role_id' => 1, 'kode_menu' => '6.3', 'status_otoritas' => 1],
    ['id_otoritas' => 11, 'role_id' => 1, 'kode_menu' => '6.4', 'status_otoritas' => 1],
    ['id_otoritas' => 12, 'role_id' => 1, 'kode_menu' => '6.5', 'status_otoritas' => 1],
    ['id_otoritas' => 24, 'role_id' => 1, 'kode_menu' => '8', 'status_otoritas' => 1],
    ['id_otoritas' => 25, 'role_id' => 1, 'kode_menu' => '9', 'status_otoritas' => 1],
    ['id_otoritas' => 26, 'role_id' => 1, 'kode_menu' => '9.2', 'status_otoritas' => 0],
    ['id_otoritas' => 36, 'role_id' => 8, 'kode_menu' => '1', 'status_otoritas' => 1],
    ['id_otoritas' => 37, 'role_id' => 8, 'kode_menu' => '4', 'status_otoritas' => 0],
    ['id_otoritas' => 38, 'role_id' => 8, 'kode_menu' => '5', 'status_otoritas' => 0],
    ['id_otoritas' => 39, 'role_id' => 8, 'kode_menu' => '6', 'status_otoritas' => 1],
    ['id_otoritas' => 40, 'role_id' => 8, 'kode_menu' => '6.1', 'status_otoritas' => 1],
    ['id_otoritas' => 41, 'role_id' => 8, 'kode_menu' => '6.2', 'status_otoritas' => 1],
    ['id_otoritas' => 42, 'role_id' => 8, 'kode_menu' => '6.3', 'status_otoritas' => 1],
    ['id_otoritas' => 43, 'role_id' => 8, 'kode_menu' => '6.4', 'status_otoritas' => 1],
    ['id_otoritas' => 44, 'role_id' => 8, 'kode_menu' => '6.5', 'status_otoritas' => 1],
    ['id_otoritas' => 45, 'role_id' => 8, 'kode_menu' => '7', 'status_otoritas' => 1],
    ['id_otoritas' => 46, 'role_id' => 8, 'kode_menu' => '8', 'status_otoritas' => 1],
    ['id_otoritas' => 47, 'role_id' => 8, 'kode_menu' => '8.1', 'status_otoritas' => 1],
    ['id_otoritas' => 51, 'role_id' => 8, 'kode_menu' => '3', 'status_otoritas' => 1],
    ['id_otoritas' => 52, 'role_id' => 8, 'kode_menu' => '9', 'status_otoritas' => 1],
    ['id_otoritas' => 54, 'role_id' => 8, 'kode_menu' => '2', 'status_otoritas' => 1],
    ['id_otoritas' => 86, 'role_id' => 2, 'kode_menu' => '2', 'status_otoritas' => 1],
    ['id_otoritas' => 87, 'role_id' => 2, 'kode_menu' => '7', 'status_otoritas' => 0],
    ['id_otoritas' => 88, 'role_id' => 2, 'kode_menu' => '7.1', 'status_otoritas' => 0],
    ['id_otoritas' => 89, 'role_id' => 1, 'kode_menu' => '6.6', 'status_otoritas' => 1],
    ['id_otoritas' => 90, 'role_id' => 1, 'kode_menu' => '9.1', 'status_otoritas' => 1],
    ['id_otoritas' => 91, 'role_id' => 1, 'kode_menu' => '9.3', 'status_otoritas' => 0],
    ['id_otoritas' => 92, 'role_id' => 1, 'kode_menu' => '9.4', 'status_otoritas' => 0],
    ['id_otoritas' => 93, 'role_id' => 1, 'kode_menu' => '8.1', 'status_otoritas' => 1],
    ['id_otoritas' => 94, 'role_id' => 8, 'kode_menu' => '6.6', 'status_otoritas' => 1],
    ['id_otoritas' => 95, 'role_id' => 8, 'kode_menu' => '9.1', 'status_otoritas' => 1],
    ['id_otoritas' => 96, 'role_id' => 8, 'kode_menu' => '9.3', 'status_otoritas' => 1],
    ['id_otoritas' => 97, 'role_id' => 8, 'kode_menu' => '9.2', 'status_otoritas' => 1],
    ['id_otoritas' => 98, 'role_id' => 8, 'kode_menu' => '9.4', 'status_otoritas' => 1],
    ['id_otoritas' => 99, 'role_id' => 8, 'kode_menu' => '6.7', 'status_otoritas' => 1],
    ['id_otoritas' => 100, 'role_id' => 1, 'kode_menu' => '6.7', 'status_otoritas' => 1],
    ['id_otoritas' => 101, 'role_id' => 2, 'kode_menu' => '3', 'status_otoritas' => 1],
]);

        // 12. Insert landing_views data (exact from SQL)
        $this->db->table('landing_views')->insert([
            'id_landing_views' => 1,
            'viewed_at' => '2025-08-22 14:16:54',
        ]);

        // 13. Insert pengumuman data (exact from SQL)
        $this->db->table('pengumuman')->insertBatch([
            [
                'id_pengumuman' => 2,
                'user_id' => 1,
                'judul' => 'Pengumuman Maintenance Sistem Ecomel',
                'deskripsi' => 'Halo, Sahabat Ecomel!' . "\r\n" . 'Kami akan melakukan maintenance sistem untuk peningkatan layanan pada:' . "\r\n\r\n" . '🗓️ Tanggal: Kamis, 18 Juli 2025' . "\r\n" . '⏰ Waktu: Pukul 23.00 – 03.00 WITA',
                'status' => 'tampil',
                'tanggal' => '2025-06-25'
            ],
            [
                'id_pengumuman' => 3,
                'user_id' => 1,
                'judul' => 'Pemberitahuan Keterlambatan Pengiriman',
                'deskripsi' => 'Kami informasikan bahwa terjadi gangguan distribusi akibat cuaca ekstrem di beberapa wilayah Kalimantan dan Sulawesi. Hal ini dapat menyebabkan keterlambatan pengiriman 1–3 hari dari estimasi awal.',
                'status' => 'tampil',
                'tanggal' => '2025-06-25'
            ],
            [
                'id_pengumuman' => 4,
                'user_id' => 1,
                'judul' => 'Promo Khusus Member Baru: Dapatkan Voucher Belanja!',
                'deskripsi' => 'Kabar baik untuk kamu yang baru bergabung!' . "\r\n" . 'Dapatkan Voucher Belanja Rp25.000 tanpa minimum belanja, khusus untuk pengguna baru yang mendaftar akun Ecomel mulai 15–31 Juli 2025.',
                'status' => 'tampil',
                'tanggal' => '2025-08-01'
            ],
        ]);

        // 14. Insert posts data (exact from SQL)
        $this->db->table('posts')->insertBatch([
            [
                'id_posts' => 54,
                'categories_id' => 46,
                'user_id' => 1,
                'title' => 'Ecomel Resmi Diluncurkan: Platform Belanja Digital Baru untuk Generasi Cerdas dan Hemat',
                'slug' => 'manfaat-berjalan-kaki-30-menit-setiap-hari',
                'konten' => '<p><strong>Banjarbaru, 15 Juli 2025</strong> – Sebuah platform e-commerce terbaru bernama <strong>Ecomel</strong> resmi diluncurkan dan siap menjadi solusi belanja digital yang cepat, mudah, dan ramah pengguna. Dengan mengusung slogan <em>"Belanja Mudah, Hidup Cerah"</em>, Ecomel menawarkan pengalaman berbelanja yang efisien dengan harga terjangkau dan dukungan pada produk-produk lokal unggulan.</p><p>Peluncuran Ecomel dilangsungkan secara daring melalui siaran langsung di kanal media sosial resmi dan disambut antusias oleh para pengguna awal, pelaku UMKM, serta komunitas digital di Kalimantan Selatan. Dalam sambutannya, CEO Ecomel, Muhammad Nazar Gunawan menyampaikan:</p><blockquote>"Kami membangun Ecomel dengan semangat untuk menghadirkan e-commerce yang bukan hanya praktis, tapi juga memberdayakan. Kami percaya bahwa teknologi bisa menjadi jembatan antara kualitas, aksesibilitas, dan pemberdayaan lokal."</blockquote><p>Ecomel hadir dengan fitur-fitur unggulan seperti:</p><ol><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Navigasi super ringan &amp; mobile friendly</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Promo harian dan sistem cashback</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Kategori khusus produk lokal dan UMKM</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Pembayaran digital aman dan pengiriman cepat</li></ol><p><br></p>',
                'excerpt' => 'Banjarbaru, 15 Juli 2025 – Sebuah platform e-commerce...',
                'thumbnail' => '1755143835d1c4284c77.jpg',
                'status' => 'publish',
                'created_at' => '2025-06-25 16:11:29',
                'updated_at' => '2025-08-14 11:57:15',
                'published_at' => '2025-06-25 00:00:00',
                'views' => 6
            ],
            [
                'id_posts' => 63,
                'categories_id' => 49,
                'user_id' => 1,
                'title' => 'Pentingnya Tidur Cukup untuk Kesehatan Tubuh dan Mental',
                'slug' => 'pentingnya-tidur-cukup-untuk-kesehatan-tubuh-dan-mental',
                'konten' => '<p>Tidur bukan sekadar istirahat â€" ini adalah kebutuhan dasar tubuh untuk memperbaiki dan memulihkan fungsi fisik serta mental. Kurang tidur dapat menyebabkan penurunan daya konsentrasi, gangguan suasana hati, dan penurunan sistem imun. Orang dewasa disarankan tidur 7â€"9 jam per malam. Untuk meningkatkan kualitas tidur, hindari layar sebelum tidur, jaga jadwal tidur yang konsisten, dan ciptakan lingkungan tidur yang nyaman dan gelap.</p>',
                'excerpt' => 'Tidur bukan sekadar istirahat â€" ini adalah kebutuhan ...',
                'thumbnail' => '17508752556585a56e62.jpg',
                'status' => 'publish',
                'created_at' => '2025-06-25 00:00:00',
                'updated_at' => '2025-06-22 00:00:00',
                'published_at' => '2025-06-22 00:00:00',
                'views' => 2
            ],
        ]);

        // 16. Insert sosmed data (exact from SQL)
        $this->db->table('sosmed')->insertBatch([
            ['id_sosmed' => 1, 'nama' => 'Facebook', 'link' => 'https://www.facebook.com/klinik', 'icon' => 'bi-facebook', 'status' => 'Y', 'urutan' => 0],
            ['id_sosmed' => 2, 'nama' => 'Instagram', 'link' => 'https://www.instagram.com/klinik', 'icon' => 'bi-instagram', 'status' => 'Y', 'urutan' => 0],
        ]);

        // 17. Insert team data (exact from SQL)
        $this->db->table('team')->insert([
            'id_team' => 17,
            'nama' => 'dr. Iskandar, M.Kes., Sp.A',
            'spesialis' => 'Dokter Cinta',
            'foto' => '175325369259f09b9399.png',
            'urutan' => 1,
            'status' => 'Y',
            'link' => ''
        ]);

        // 18. Insert layout data (exact from SQL)
        $this->db->table('layout')->insertBatch([
            [
                'id_layout' => 1,
                'kode' => 'hero',
                'html_section' => 'layout html' . "\r\n",
                'konten_dinamis' => '{"judul": "Slider", "deskripsi": null}',
                'urutan' => 1,
                'status' => 'Y',
                'created_at' => '2025-08-22 14:16:54',
                'updated_at' => '2025-08-22 16:22:13'
            ],
            [
                'id_layout' => 2,
                'kode' => 'layanan',
                'html_section' => 'layout html',
                'konten_dinamis' => '{"judul":"Layanan","deskripsi":"Kami memiliki keunggulan dalam pelayanan untuk memenuhi kebutuhan Anda dan keluarga."}',
                'urutan' => 3,
                'status' => 'Y',
                'created_at' => '2025-08-22 14:16:54',
                'updated_at' => '2025-08-22 14:16:54'
            ],
            [
                'id_layout' => 3,
                'kode' => 'team',
                'html_section' => 'layout html',
                'konten_dinamis' => '{"judul":"Team","deskripsi":"Berikut adalah daftar tim Ecomel"}',
                'urutan' => 4,
                'status' => 'N',
                'created_at' => '2025-08-22 14:16:54',
                'updated_at' => '2025-08-22 14:16:54'
            ],
            [
                'id_layout' => 4,
                'kode' => 'mitra',
                'html_section' => 'layout html',
                'konten_dinamis' => '{"judul":"Mitra dan Partner Kami","deskripsi":"Kami bekerja sama dengan berbagai institusi terpercaya untuk mendukung layanan terbaik."}',
                'urutan' => 7,
                'status' => 'Y',
                'created_at' => '2025-08-22 14:16:54',
                'updated_at' => '2025-08-22 14:16:54'
            ],
            [
                'id_layout' => 5,
                'kode' => 'berita',
                'html_section' => 'layout html',
                'konten_dinamis' => '{"judul":"Berita \/ Event","deskripsi":"Kami menyediakan berita terbaru tentang Ecomel"}',
                'urutan' => 5,
                'status' => 'Y',
                'created_at' => '2025-08-22 14:16:54',
                'updated_at' => '2025-08-22 14:16:54'
            ],
            [
                'id_layout' => 6,
                'kode' => 'pengumuman',
                'html_section' => 'layout html',
                'konten_dinamis' => '{"judul": "Pengumuman", "deskripsi": null}',
                'urutan' => 6,
                'status' => 'Y',
                'created_at' => '2025-08-22 14:16:54',
                'updated_at' => '2025-08-22 14:16:54'
            ],
        ]);
    }
}
