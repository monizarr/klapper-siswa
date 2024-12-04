<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class User extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_sekolah' => 1,
                'username' => 'sd1',
                'password' => password_hash('sd1', PASSWORD_DEFAULT),
                'role' => 'sekolah'
            ],
            [
                'id_sekolah' => 2,
                'username' => 'sd2',
                'password' => password_hash('sd2', PASSWORD_DEFAULT),
                'role' => 'sekolah'
            ],

        ];

        $this->db->table('user')->insertBatch($data);
    }
}
