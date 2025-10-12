<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePersonel extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id_personel' => [
        'type'           => 'INT',
        'auto_increment' => true,
      ],
      'nama' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => true,
      ],
      'jabatan' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => true,
      ],
      'penempatan' => [
        'type' => "ENUM('Lab Terpadu','Mutu dan Administrasi','Lab Kimia','Lab Biologi','Lab Fisika')",
        'null' => true,
      ],
      'foto' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => true,
      ],
      'nip' => [
        'type'       => 'VARCHAR',
        'constraint' => 50,
        'null'       => true,
      ],
      'tempat_lahir' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
        'null'       => true,
      ],
      'tanggal_lahir' => [
        'type' => 'DATE',
        'null' => true,
      ],
      'jenis_kelamin' => [
        'type' => "ENUM('Laki-laki','Perempuan')",
        'null' => true,
      ],
      'kebangsaan' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
        'default'    => 'Indonesia',
      ],
      'alamat' => [
        'type' => 'TEXT',
        'null' => true,
      ],
      'no_handphone' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
        'null'       => true,
      ],
      'email' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
        'null'       => true,
      ],
      'doc_cv' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => true,
      ],
      'doc_coc' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => true,
      ],
      'doc_surat_tugas' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
        'null'       => true,
      ],
      'doc_lainnya' => [
        'type'       => 'TEXT',
        'null'       => true,
      ],
      'urutan' => [
        'type'    => 'INT',
        'default' => 0,
      ],
      'status' => [
        'type'    => "ENUM('Y','N')",
        'default' => 'Y',
      ],
    ]);
    $this->forge->addKey('id_personel', true);
    $this->forge->createTable('personel');
  }

  public function down()
  {
    $this->forge->dropTable('personel');
  }
}
