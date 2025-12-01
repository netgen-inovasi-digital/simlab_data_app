<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePersonel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_personel' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'jabatan' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'id_penempatan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Y', 'N'],
                'default' => 'Y',
            ],
        ]);
        $this->forge->addKey('id_personel', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('id_penempatan', 'penempatan_categories', 'id_penempatan', 'SET NULL', 'CASCADE');
        $this->forge->createTable('personel');
    }

    public function down()
    {
        $this->forge->dropTable('personel');
    }
}
