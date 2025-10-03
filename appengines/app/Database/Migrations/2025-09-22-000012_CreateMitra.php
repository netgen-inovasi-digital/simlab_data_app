<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMitra extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mitra' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status' => [
                'type'    => "ENUM('Y','N')",
                'null'    => true,
                'default' => 'Y',
            ],
        ]);

        $this->forge->addKey('id_mitra', true);
        $this->forge->createTable('mitra');
    }

    public function down()
    {
        $this->forge->dropTable('mitra');
    }
}
