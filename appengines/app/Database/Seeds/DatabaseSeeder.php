<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Role dan User
        $this->call('RolesSeeder');
        $this->call('UsersSeeder');

        // 2. Category dan Folder
        $this->call('CategoriesSeeder');
        $this->call('FolderSeeder');
        $this->call('FolderLinksSeeder');

        // 3. Menu dan Otoritas
        $this->call('MenusSeeder');
        $this->call('OtoritasSeeder');

        // 4. Data tanpa FK
        $this->call('HeroSeeder');
        $this->call('KonfigurasiSeeder');
        $this->call('LandingViewsSeeder');
        $this->call('LayananSeeder');
        $this->call('LayoutSeeder');
        $this->call('MitraSeeder');
        $this->call('MotifsSeeder');
        $this->call('NavbarSeeder');

        // 5. Pages dan relasinya
        $this->call('PagesSeeder');
        $this->call('PageViewsSeeder');

        // 6. Pengumuman dan Personel
        $this->call('PengumumanSeeder');
        $this->call('PersonelSeeder');

        // 7. Posts dan Files
        $this->call('PostsSeeder');
        $this->call('FilesSeeder');

        // 8. Password Reset
        $this->call('PasswordResetsSeeder');
    }
}
