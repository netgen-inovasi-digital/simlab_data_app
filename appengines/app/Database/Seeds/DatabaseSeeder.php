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

    // 3. Menu
    $this->call('MenusSeeder');
    
    // 4. Data tanpa FK
    $this->call('KonfigurasiSeeder');
    $this->call('OtoritasSeeder');
  }
}
