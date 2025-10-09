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
    $this->call('FilesSeeder');
    $this->call('FolderLinksSeeder');

    // 3. Menu dan Otoritas
    $this->call('MenusSeeder');
    $this->call('OtoritasSeeder');
    $this->call('OtoritasFileSeeder');
    $this->call('OtoritasFolderSeeder');

    // 4. Data tanpa FK
    $this->call('KonfigurasiSeeder');

    $this->call('PersonelSeeder');
    $this->call('PagesSeeder');

    // 8. Password Reset
    $this->call('PasswordResetsSeeder');
    $this->call('DokumenSeeder');
  }
}
