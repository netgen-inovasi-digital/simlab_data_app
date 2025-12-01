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
                'auto_increment' => true
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0
            ],
            'kode_menu' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true
            ],
            'status_otoritas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'default' => 1
            ]
        ]);

        // Primary Key
        $this->forge->addKey('id_otoritas', true);

        // Foreign Key ke roles
        $this->forge->addForeignKey('role_id', 'roles', 'id_role', 'CASCADE', 'CASCADE');

        // Buat tabel
        $this->forge->createTable('otoritas');
    }

    public function down()
    {
        $this->forge->dropTable('otoritas');
    }
}
