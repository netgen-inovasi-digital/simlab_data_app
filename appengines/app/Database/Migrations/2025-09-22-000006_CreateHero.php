<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHero extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_hero' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'urutan' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Y', 'N'],
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_hero', true); // primary key
        $this->forge->createTable('hero');
    }

    public function down()
    {
        $this->forge->dropTable('hero');
    }
}
