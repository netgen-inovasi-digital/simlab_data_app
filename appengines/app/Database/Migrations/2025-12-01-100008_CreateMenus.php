<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenus extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_menu' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_menu' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
            ],
            'kode_induk' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'default' => '0',
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_menu', true);
        $this->forge->createTable('menus');
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}
