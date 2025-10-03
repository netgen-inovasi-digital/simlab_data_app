<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLayanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_layanan' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'urutan' => [
                'type'     => 'INT',
                'null'     => true,
            ],
            'status' => [
                'type'       => "ENUM('Y','N')",
                'default'    => 'Y',
                'null'       => false,
            ],
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_layanan', true);
        $this->forge->createTable('layanan');
    }

    public function down()
    {
        $this->forge->dropTable('layanan');
    }
}
