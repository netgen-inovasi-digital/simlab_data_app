<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompleteNetxTemplateDatabase extends Migration
{
    public function up()
    {
        // 1. Categories table
        $this->forge->addField([
            'id_categories' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id_categories', true);
        $this->forge->createTable('categories', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 2. Hero table
        $this->forge->addField([
            'id_hero' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'deskripsi' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_hero', true);
        $this->forge->createTable('hero', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 3. Konfigurasi table
        $this->forge->addField([
            'id_konfigurasi' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_profil' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => false,
                'default' => '',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
                'default' => '',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => '',
            ],
            'kota' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => '',
            ],
            'provinsi' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => '',
            ],
            'logo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
                'default' => '',
            ],
            'peta' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_konfigurasi', true);
        $this->forge->createTable('konfigurasi', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 4. Landing Views table
        $this->forge->addField([
            'id_landing_views' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'viewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_landing_views', true);
        $this->forge->createTable('landing_views', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 5. Layanan table
        $this->forge->addField([
            'id_layanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'deskripsi' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => true,
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_layanan', true);
        $this->forge->createTable('layanan', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci', 'ROW_FORMAT' => 'DYNAMIC']);

        // 6. Mitra table
        $this->forge->addField([
            'id_mitra' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_mitra', true);
        $this->forge->createTable('mitra', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci', 'ROW_FORMAT' => 'DYNAMIC']);

        // 7. Motifs table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'deskripsi' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('motifs', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4']);

        // 8. Navbar table
        $this->forge->addField([
            'id_navbar' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_navbar' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => false,
                'default' => '0',
            ],
            'kode_induk' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => false,
                'default' => '0',
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => '0',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_navbar', true);
        $this->forge->createTable('navbar', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci', 'ROW_FORMAT' => 'DYNAMIC']);

        // 9. Page Views table
        $this->forge->addField([
            'id_page_views' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'page_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'viewed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id_page_views', true);
        $this->forge->createTable('page_views', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 10. Password Resets table
        $this->forge->addField([
            'id_password_reset' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'expired_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'used' => [
                'type' => 'TINYINT',
                'constraint' => 4,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_password_reset', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('password_resets', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 11. Post Views table
        $this->forge->addField([
            'id_post_views' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'post_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'viewed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id_post_views', true);
        $this->forge->createTable('post_views', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 12. Roles table
        $this->forge->addField([
            'id_role' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_role' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'grup' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'author'],
                'null' => true,
            ],
            'status_role' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id_role', true);
        $this->forge->createTable('roles', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 13. Sosmed table
        $this->forge->addField([
            'id_sosmed' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_sosmed', true);
        $this->forge->createTable('sosmed', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 14. Team table
        $this->forge->addField([
            'id_team' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'spesialis' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => true,
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_team', true);
        $this->forge->createTable('team', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 15. Visitor table
        $this->forge->addField([
            'id_visitor' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ipAddress' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'userAgent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'visitPage' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'visitDate' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'visitTime' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_visitor', true);
        $this->forge->createTable('visitor', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 16. Users table
        $this->forge->addField([
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => '0',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => '0',
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'status_user' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'alamat' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => '0',
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => '0',
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => '0',
            ],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addKey('role_id');
        $this->forge->createTable('users', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 17. Menus table
        $this->forge->addField([
            'id_menu' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_menu' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => false,
                'default' => '0',
            ],
            'kode_induk' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => false,
                'default' => '0',
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => '0',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_menu', true);
        $this->forge->createTable('menus', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 18. Otoritas table
        $this->forge->addField([
            'id_otoritas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'kode_menu' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
            ],
            'status_otoritas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_otoritas', true);
        $this->forge->addKey('role_id');
        $this->forge->createTable('otoritas', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 19. Pages table
        $this->forge->addField([
            'id_pages' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'konten' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'publish'],
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'views' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_pages', true);
        $this->forge->addKey('user_id', false, false, 'author_id');
        $this->forge->createTable('pages', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci', 'ROW_FORMAT' => 'DYNAMIC']);

        // 21. Pengumuman table
        $this->forge->addField([
            'id_pengumuman' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => '80',
                'null' => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['tampil', 'tersembunyi'],
                'null' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_pengumuman', true);
        $this->forge->addKey('user_id', false, false, 'author_id');
        $this->forge->createTable('pengumuman', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 22. Posts table
        $this->forge->addField([
            'id_posts' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'categories_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'konten' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'thumbnail' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'publish'],
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'views' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_posts', true);
        $this->forge->addKey('categories_id');
        $this->forge->addKey('user_id', false, false, 'author_id');
        $this->forge->createTable('posts', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // 24. Layout table
        $this->forge->addField([
            'id_layout' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'html_section' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'konten_dinamis' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'urutan' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id_layout', true);
        $this->forge->createTable('layout', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);

        // Add Foreign Key Constraints after all tables are created (COMMENTED OUT FOR NOW)
        // $this->db->query('ALTER TABLE layout ADD CONSTRAINT fk_layout_page FOREIGN KEY (id_page) REFERENCES page_builder (id_page) ON DELETE CASCADE');
        // $this->db->query('ALTER TABLE layout ADD CONSTRAINT fk_layout_template FOREIGN KEY (id_template) REFERENCES section_templates (id_template) ON DELETE SET NULL');
    }

    public function down()
    {
        // Drop all foreign keys first (COMMENTED OUT FOR NOW)
        // $this->db->query('ALTER TABLE layout DROP FOREIGN KEY IF EXISTS fk_layout_page');
        // $this->db->query('ALTER TABLE layout DROP FOREIGN KEY IF EXISTS fk_layout_template');

        // Drop tables in reverse order
        $this->forge->dropTable('layout', true);
        $this->forge->dropTable('posts', true);
        $this->forge->dropTable('pengumuman', true);
        $this->forge->dropTable('pages', true);
        $this->forge->dropTable('otoritas', true);
        $this->forge->dropTable('menus', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('visitor', true);
        $this->forge->dropTable('team', true);
        $this->forge->dropTable('sosmed', true);
        $this->forge->dropTable('roles', true);
        $this->forge->dropTable('post_views', true);
        $this->forge->dropTable('password_resets', true);
        $this->forge->dropTable('page_views', true);
        $this->forge->dropTable('navbar', true);
        $this->forge->dropTable('motifs', true);
        $this->forge->dropTable('mitra', true);
        $this->forge->dropTable('layanan', true);
        $this->forge->dropTable('landing_views', true);
        $this->forge->dropTable('konfigurasi', true);
        $this->forge->dropTable('hero', true);
        $this->forge->dropTable('categories', true);
    }
}
