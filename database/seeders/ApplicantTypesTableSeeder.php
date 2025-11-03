<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicantTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('applicant_types')->delete();
        
        \DB::table('applicant_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Owner',
                'created_at' => '2025-07-18 23:13:11',
                'updated_at' => '2025-07-18 23:13:53',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Agent',
                'created_at' => '2025-07-18 23:13:51',
                'updated_at' => '2025-07-18 23:13:52',
            ),
        ));
        
        
    }
}