<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokumen extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id_dokumen' => [
        'type'           => 'INT',
        'constraint'     => 11,
        'auto_increment' => true,
        'null'       => false,
      ],
      'id_personel' => [
        'type'       => 'INT',
        'constraint' => 11,
        'null'       => false,
      ],
      'nama_asli_file' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => false,
      ],
      'nama_file_tersimpan' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => false,
      ],
      'tipe_dokumen' => [
        'type' => 'ENUM',
        'constraint' => ['cv', 'coc', 'surat_tugas', 'lainnya', 'foto'],
      ],
      'path_file' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => false,
      ],
      'created_at' => [
        'type' => 'TIMESTAMP',
        'null' => true,
      ],
    ]);

    $this->forge->addKey('id_dokumen', true);

    // Foreign Key
    $this->forge->addForeignKey('id_personel', 'personel', 'id_personel', 'CASCADE', 'CASCADE');

    $this->forge->createTable('dokumen');
  }

  public function down()
  {
    $this->forge->dropTable('dokumen');
  }
}
