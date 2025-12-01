<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtoritasFolder extends Migration
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
        'null' => true
      ],
      'id_folder' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => true
      ],
      'can_view' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'null' => true,
        'default' => 0
      ],
      'can_crud' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'null' => true,
        'default' => 0
      ],
    ]);

    $this->forge->addKey('id_otoritas', true);
    $this->forge->addForeignKey('id_role', 'roles', 'id_role', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('id_folder', 'folder', 'id_folder', 'CASCADE', 'CASCADE');

    $this->forge->createTable('otoritas_folder');
  }

  public function down()
  {
    $this->forge->dropTable('otoritas_folder');
  }
}
