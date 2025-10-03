<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PageViewsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_page_views' => 1, 'page_id' => 60, 'viewed_at' => '2025-06-30 03:23:28'],
            ['id_page_views' => 2, 'page_id' => 60, 'viewed_at' => '2025-06-30 14:18:32'],
            ['id_page_views' => 3, 'page_id' => 60, 'viewed_at' => '2025-07-04 06:13:29'],
            ['id_page_views' => 4, 'page_id' => 60, 'viewed_at' => '2025-07-12 12:29:55'],
            ['id_page_views' => 5, 'page_id' => 60, 'viewed_at' => '2025-07-16 07:49:23'],
            ['id_page_views' => 6, 'page_id' => 60, 'viewed_at' => '2025-07-16 09:49:08'],
            ['id_page_views' => 7, 'page_id' => 60, 'viewed_at' => '2025-07-18 07:52:40'],
            ['id_page_views' => 8, 'page_id' => 60, 'viewed_at' => '2025-07-18 07:57:11'],
            ['id_page_views' => 9, 'page_id' => 60, 'viewed_at' => '2025-07-18 07:58:07'],
            ['id_page_views' => 10, 'page_id' => 60, 'viewed_at' => '2025-07-18 08:31:49'],
            ['id_page_views' => 11, 'page_id' => 60, 'viewed_at' => '2025-07-19 13:42:04'],
            ['id_page_views' => 12, 'page_id' => 60, 'viewed_at' => '2025-07-21 18:13:33'],
            ['id_page_views' => 13, 'page_id' => 60, 'viewed_at' => '2025-07-23 11:38:13'],
            ['id_page_views' => 14, 'page_id' => 60, 'viewed_at' => '2025-07-27 02:11:17'],
            ['id_page_views' => 15, 'page_id' => 60, 'viewed_at' => '2025-07-30 07:36:49'],
            ['id_page_views' => 16, 'page_id' => 60, 'viewed_at' => '2025-07-30 07:43:23'],
            ['id_page_views' => 17, 'page_id' => 60, 'viewed_at' => '2025-07-30 07:56:16'],
            ['id_page_views' => 18, 'page_id' => 60, 'viewed_at' => '2025-07-30 11:24:49'],
            ['id_page_views' => 19, 'page_id' => 60, 'viewed_at' => '2025-07-30 15:18:53'],
            ['id_page_views' => 20, 'page_id' => 60, 'viewed_at' => '2025-07-31 21:09:04'],
            ['id_page_views' => 21, 'page_id' => 60, 'viewed_at' => '2025-08-06 22:13:05'],
        ];

        // Insert batch data
        $this->db->table('page_views')->insertBatch($data);
    }
}
