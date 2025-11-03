<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicationClassificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('application_classifications')->delete();
        
        \DB::table('application_classifications')->insert(array (
            0 => 
            array (
                'id' => 1,
                'classification' => 'Change',
                'created_at' => '2025-07-18 12:09:50',
                'updated_at' => '2025-07-18 12:09:50',
            ),
            1 => 
            array (
                'id' => 3,
                'classification' => 'Development',
                'created_at' => '2025-07-18 12:16:04',
                'updated_at' => '2025-07-18 12:16:04',
            ),
            2 => 
            array (
                'id' => 4,
                'classification' => 'Subdivision',
                'created_at' => '2025-07-18 12:16:21',
                'updated_at' => '2025-07-18 12:16:21',
            ),
        ));
        
        
    }
}