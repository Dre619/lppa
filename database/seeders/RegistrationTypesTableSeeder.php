<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegistrationTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('registration_types')->delete();
        
        \DB::table('registration_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Change of Use',
                'reg_key' => 'CU',
                'created_at' => '2025-07-18 12:23:54',
                'updated_at' => '2025-07-18 12:23:54',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Development',
                'reg_key' => 'D',
                'created_at' => '2025-07-18 12:24:29',
                'updated_at' => '2025-07-18 12:24:29',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Suddivision',
                'reg_key' => 'S',
                'created_at' => '2025-07-18 12:24:57',
                'updated_at' => '2025-07-18 12:24:57',
            ),
        ));
        
        
    }
}