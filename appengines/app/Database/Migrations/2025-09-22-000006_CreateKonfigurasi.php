<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKonfigurasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_konfigurasi' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_profil' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'provinsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'peta' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rajaongkir_api_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'rajaongkir_origin_subdistrict_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'rajaongkir_origin_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'rajaongkir_couriers' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Comma separated courier codes (jne,sicepat,jnt,etc)',
            ],
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_konfigurasi', true);
        $this->forge->createTable('konfigurasi');
    }

    public function down()
    {
        $this->forge->dropTable('konfigurasi');
    }
}
