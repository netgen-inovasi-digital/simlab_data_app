<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFolder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_folder' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default' => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id_folder', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('folder');
    }

    public function down()
    {
        $this->forge->dropTable('folder');
    }
}
