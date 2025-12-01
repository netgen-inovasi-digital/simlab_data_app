<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtoritas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_otoritas' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
            ],
            'kode_menu' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => true,
            ],
            'status_otoritas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_otoritas', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id_role', 'CASCADE', 'CASCADE');
        $this->forge->createTable('otoritas');
    }

    public function down()
    {
        $this->forge->dropTable('otoritas');
    }
}
