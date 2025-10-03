<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengumuman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengumuman' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => "ENUM('tampil','tersembunyi')",
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_pengumuman', true);
        $this->forge->createTable('pengumuman');
    }

    public function down()
    {
        $this->forge->dropTable('pengumuman');
    }
}
