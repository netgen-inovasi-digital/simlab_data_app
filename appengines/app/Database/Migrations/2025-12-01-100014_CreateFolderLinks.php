<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFolderLinks extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'child_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'folder', 'id_folder', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('child_id', 'folder', 'id_folder', 'CASCADE', 'CASCADE');
        $this->forge->createTable('folder_links');
    }

    public function down()
    {
        $this->forge->dropTable('folder_links');
    }
}
