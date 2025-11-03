<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegistrationOrganizationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('registration_organizations')->delete();
        
        \DB::table('registration_organizations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'LKPPA',
                'created_at' => '2025-07-18 15:00:41',
                'updated_at' => '2025-07-18 15:00:41',
            ),
        ));
        
        
    }
}