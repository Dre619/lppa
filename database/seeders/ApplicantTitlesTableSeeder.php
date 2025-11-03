<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicantTitlesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('applicant_titles')->delete();
        
        \DB::table('applicant_titles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Mr',
                'created_at' => '2025-07-18 12:59:19',
                'updated_at' => '2025-07-18 12:59:19',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Brig. Gen.',
                'created_at' => '2025-07-18 13:02:21',
                'updated_at' => '2025-07-18 13:02:21',
            ),
        ));
        
        
    }
}