<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNavbar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_navbar' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_navbar' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'default'    => '0',
            ],
            'kode_induk' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'default'    => '0',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '0',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => '0',
            ],
            'status' => [
                'type'       => "ENUM('Y','N')",
                'null'       => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_navbar', true);
        $this->forge->createTable('navbar');
    }

    public function down()
    {
        $this->forge->dropTable('navbar');
    }
}
