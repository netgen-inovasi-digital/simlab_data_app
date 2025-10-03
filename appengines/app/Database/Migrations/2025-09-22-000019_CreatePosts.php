<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_posts' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'categories_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'konten' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'thumbnail' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type' => "ENUM('draft','publish')",
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'views' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_posts', true);
        $this->forge->createTable('posts');
    }

    public function down()
    {
        $this->forge->dropTable('posts');
    }
}
