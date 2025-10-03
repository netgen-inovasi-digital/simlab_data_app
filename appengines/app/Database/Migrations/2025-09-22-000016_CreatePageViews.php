<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePageViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_page_views' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'page_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'viewed_at' => [
                'type' => 'DATETIME',
                'null' => true, // biar gak error
            ],
        ]);

        $this->forge->addKey('id_page_views', true);
        $this->forge->createTable('page_views');
    }

    public function down()
    {
        $this->forge->dropTable('page_views');
    }
}
