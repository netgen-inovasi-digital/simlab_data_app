<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFolder extends Migration
{
  public function up()
  {
    // 1. Buat struktur dasar tabel
    $this->forge->addField([
      'id_folder' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'nama' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => false,
      ],
      'slug' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
        'null' => true,
      ],
      'status' => [
        'type' => 'ENUM',
        'constraint' => ['aktif', 'nonaktif'],
        'default' => 'aktif',
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'sort_order' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => true,
      ],
      'flag' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 0,
      ]
    ]);

    $this->forge->addKey('id_folder', true);
    $this->forge->addUniqueKey('slug');
    
    $this->forge->createTable('folder');

    // 2. Update kolom created_at dan updated_at dengan CURRENT_TIMESTAMP
    // karena $this->forge tidak mendukung default CURRENT_TIMESTAMP
    $this->db->query("
            ALTER TABLE folder 
            MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");
  }

  public function down()
  {
    // Drop tabel folder jika rollback migration
    $this->forge->dropTable('folder');
  }
}
