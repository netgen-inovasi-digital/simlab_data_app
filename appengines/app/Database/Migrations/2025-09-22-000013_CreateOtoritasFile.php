<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtoritasFile extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id_otoritas' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'id_role' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => false
      ],
      'id_file' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => false
      ],
      'can_view' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 0
      ],
      'can_crud' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 0
      ],
    ]);

    $this->forge->addKey('id_otoritas', true);
    $this->forge->addForeignKey('id_role', 'roles', 'id_role', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('id_file', 'files', 'id_files', 'CASCADE', 'CASCADE');

    $this->forge->createTable('otoritas_file');
  }

  public function down()
  {
    $this->forge->dropTable('otoritas_file');
  }
}
