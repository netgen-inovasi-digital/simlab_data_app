<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenempatanCategories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penempatan' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_penempatan', true);
        $this->forge->createTable('penempatan_categories');
    }

    public function down()
    {
        $this->forge->dropTable('penempatan_categories');
    }
}
