<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Andrew Chikuluma',
                'email' => 'chikulumaa@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$uQ5oqLpOescQGpQ84CNi0.RIP0kVOm6mZfD1dr2i/tdB9muhxlGZq',
                'remember_token' => NULL,
                'created_at' => '2025-07-18 09:44:35',
                'updated_at' => '2025-07-18 09:51:12',
                'role_id' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Secretary ',
                'email' => 'wes1doc@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$nCzW2ZPXn0p7GTowhjBwYutLTBCk6duaxsqmCnK5mXp.h5lJ3h3i2',
                'remember_token' => NULL,
                'created_at' => '2025-07-20 03:26:38',
                'updated_at' => '2025-07-20 03:26:38',
                'role_id' => 3,
            ),
        ));
        
        
    }
}