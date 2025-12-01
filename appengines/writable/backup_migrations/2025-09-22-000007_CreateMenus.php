<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenus extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id_menu' => [
        'type'           => 'INT',
        'constraint'     => 11,
        'unsigned'       => true,
        'auto_increment' => true,
      ],
      'kode_menu' => [
        'type'       => 'VARCHAR',
        'constraint' => 5,
        'null'       => false,
      ],
      'kode_induk' => [
        'type'       => 'VARCHAR',
        'constraint' => 5,
        'null'       => false,
        'default'    => '0',
      ],
      'nama' => [
        'type'       => 'VARCHAR',
        'constraint' => 50,
        'null'       => false,
      ],
      'link' => [
        'type'       => 'VARCHAR',
        'constraint' => 50,
        'null'       => false,
      ],
      'icon' => [
        'type'       => 'VARCHAR',
        'constraint' => 50,
        'null'       => false,
      ],
      'sort_order' => [
        'type'       => 'INT',
        'constraint' => 11,
        'null'       => true,
        'default'    => null,
      ],
    ]);

    // Primary key
    $this->forge->addKey('id_menu', true);

    // Create table
    $this->forge->createTable('menus');
  }

  public function down()
  {
    $this->forge->dropTable('menus');
  }
}
