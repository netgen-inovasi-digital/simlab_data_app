<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLandingViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_landing_views' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'viewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_landing_views', true);
        $this->forge->createTable('landing_views');
    }

    public function down()
    {
        $this->forge->dropTable('landing_views');
    }
}
