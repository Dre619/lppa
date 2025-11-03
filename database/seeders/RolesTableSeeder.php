<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Administrator',
                'role_slug' => 'administrator',
                'description' => NULL,
                'is_active' => 1,
                'is_default' => 0,
                'created_at' => '2025-07-18 11:43:41',
                'updated_at' => '2025-07-18 11:43:42',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Operator',
                'role_slug' => 'operator',
                'description' => '',
                'is_active' => 1,
                'is_default' => 0,
                'created_at' => '2025-07-18 09:55:08',
                'updated_at' => '2025-07-18 09:55:08',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Secretary ',
                'role_slug' => 'secretary',
                'description' => '',
                'is_active' => 1,
                'is_default' => 0,
                'created_at' => '2025-07-18 10:07:39',
                'updated_at' => '2025-07-18 10:07:39',
            ),
        ));
        
        
    }
}