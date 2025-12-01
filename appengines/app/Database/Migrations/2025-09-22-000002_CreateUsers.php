<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => '0'
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'status_user' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'alamat' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ]
        ]);

        $this->forge->addKey('id_user', true);

        // Foreign key ke roles
        $this->forge->addForeignKey('role_id', 'roles', 'id_role', 'CASCADE', 'CASCADE');

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
