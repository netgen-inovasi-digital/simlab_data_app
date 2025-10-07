<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PasswordResetsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_password_reset' => 1,
                'user_id'           => 4,
                'token'             => 'fSkVsYPo2879Lnn53rHyvjfylnDNvVQERdiouV4mH0WvBtRB7nWcMBLWALITdPQc',
                'expired_at'        => '2025-07-23 11:33:13',
                'used'              => 1,
                'created_at'        => null,
            ],
            [
                'id_password_reset' => 2,
                'user_id'           => 4,
                'token'             => 'm7f34cEXTofkkwXqkYQnDmRJSIWcMyR6Kr9L9VQdg9vO5LOdaoSlHw6YvIpyuVST',
                'expired_at'        => '2025-07-23 12:02:34',
                'used'              => 1,
                'created_at'        => null,
            ],
        ];

        // Insert ke tabel password_resets
        $this->db->table('password_resets')->insertBatch($data);
    }
}
