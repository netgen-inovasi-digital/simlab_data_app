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
                'null' => false
            ],
            'kode_menu' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'status_otoritas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1 = aktif, 0 = nonaktif'
            ]
        ]);

        // Primary Key
        $this->forge->addKey('id_otoritas', true);

        // Foreign Key ke roles
        $this->forge->addForeignKey('role_id', 'roles', 'id_role', 'CASCADE', 'CASCADE');

        // Foreign Key ke menus
        $this->forge->addForeignKey('kode_menu', 'menus', 'kode_menu', 'CASCADE', 'CASCADE');

        // Buat tabel
        $this->forge->createTable('otoritas');
    }

    public function down()
    {
        $this->forge->dropTable('otoritas');
    }
}
