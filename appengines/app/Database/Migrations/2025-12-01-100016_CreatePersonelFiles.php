<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePersonelFiles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_personel' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_files' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_personel', 'id_files']);
        $this->forge->addForeignKey('id_personel', 'personel', 'id_personel', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_files', 'files', 'id_files', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('personel_files');
    }

    public function down()
    {
        $this->forge->dropTable('personel_files');
    }
}
