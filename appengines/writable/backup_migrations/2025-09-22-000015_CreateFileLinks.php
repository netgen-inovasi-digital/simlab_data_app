<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFileLinks extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'parent_folder' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
            'child_file' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);

        // Foreign Key
        $this->forge->addForeignKey('parent_folder', 'folder', 'id_folder', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('child_file', 'files', 'id_files', 'CASCADE', 'CASCADE');

        $this->forge->createTable('file_links');
    }

    public function down()
    {
        $this->forge->dropTable('file_links');
    }
}
