<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ChangeOfUseTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('change_of_use')->delete();
        
        \DB::table('change_of_use')->insert(array (
            0 => 
            array (
                'id' => 1,
                'stage_name' => 'Application',
                'description' => '',
                'created_at' => '2025-07-18 13:42:34',
                'updated_at' => '2025-07-18 13:42:34',
            ),
            1 => 
            array (
                'id' => 2,
                'stage_name' => 'Advertising	',
                'description' => '',
                'created_at' => '2025-07-18 13:43:46',
                'updated_at' => '2025-07-18 13:43:46',
            ),
            2 => 
            array (
                'id' => 3,
                'stage_name' => 'Ministry',
                'description' => '',
                'created_at' => '2025-07-18 13:44:06',
                'updated_at' => '2025-07-18 13:44:06',
            ),
            3 => 
            array (
                'id' => 4,
                'stage_name' => 'Recommendation',
                'description' => '',
                'created_at' => '2025-07-18 13:44:20',
                'updated_at' => '2025-07-18 13:44:20',
            ),
        ));
        
        
    }
}