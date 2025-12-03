<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFiles extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id_files' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'categories_id' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => false
      ],
      'user_id' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => false
      ],
      'nomor_dokumen' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true
      ],
      'title' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true
      ],
      'slug' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true
      ],
      'berkas' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true
      ],
      'revisi' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => true
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true
      ]
    ]);

    $this->forge->addKey('id_files', true);

    $this->forge->addForeignKey('user_id', 'users', 'id_user', 'CASCADE', 'CASCADE');
    $this->forge->addKey('slug');


    $this->forge->createTable('files');
  }

  public function down()
  {
    $this->forge->dropTable('files');
  }
}
