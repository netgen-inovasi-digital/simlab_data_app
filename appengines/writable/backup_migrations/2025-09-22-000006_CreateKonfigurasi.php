<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKonfigurasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_konfigurasi' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('id_konfigurasi', true);
        $this->forge->createTable('konfigurasi');
    }

    public function down()
    {
        $this->forge->dropTable('konfigurasi');
    }
}
