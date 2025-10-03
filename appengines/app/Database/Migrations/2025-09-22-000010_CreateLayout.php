<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLayout extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_layout' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'html_section' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'konten_dinamis' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'urutan' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'null'       => false,
            ],
            'status' => [
                'type'       => "ENUM('Y','N')",
                'default'    => 'Y',
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id_layout', true);
        $this->forge->createTable('layout');
    }

    public function down()
    {
        $this->forge->dropTable('layout');
    }
}
