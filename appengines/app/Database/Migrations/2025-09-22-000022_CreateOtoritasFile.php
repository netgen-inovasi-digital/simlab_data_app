<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtoritasFile extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_otoritas_file' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'file_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'can_view' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'can_edit' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'can_delete' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ]
        ]);

        $this->forge->addKey('id_otoritas_file', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id_role', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('file_id', 'files', 'id_files', 'CASCADE', 'CASCADE');

        $this->forge->createTable('otoritas_file');
    }

    public function down()
    {
        $this->forge->dropTable('otoritas_file');
    }
}
