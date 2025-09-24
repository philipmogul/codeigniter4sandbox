<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username' => 'admin',
            'email'    => 'philipp1mogul@gmail.com',
            'password_hash'=> password_hash('12345', PASSWORD_BCRYPT),
        ];
        $this->db->table('users')->insert($data);
    }
}
