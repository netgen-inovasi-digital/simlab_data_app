<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtoritasFolder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_otoritas_folder' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'folder_id' => [
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

        $this->forge->addKey('id_otoritas_folder', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id_role', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('folder_id', 'folder', 'id_folder', 'CASCADE', 'CASCADE');

        $this->forge->createTable('otoritas_folder');
    }

    public function down()
    {
        $this->forge->dropTable('otoritas_folder');
    }
}
