<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategories extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id_categories' => [
        'type' => 'INT',
        'constraint' => 11,
        'auto_increment' => true
      ],
      'nama' => [
        'type' => 'VARCHAR',
        'constraint' => '50',
        'default' => '0',
      ],
      'slug' => [
        'type' => 'VARCHAR',
        'constraint' => '50',
        'default' => '0',
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => false,
      ]
    ]);

    $this->forge->addKey('id_categories', true);
    
    $this->forge->createTable('categories');
  }

  public function down()
  {
    $this->forge->dropTable('categories');
  }
}
