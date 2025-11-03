<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PropertyTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('property_types')->delete();
        
        \DB::table('property_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Commercial',
                'created_at' => '2025-07-18 13:19:36',
                'updated_at' => '2025-07-18 13:19:36',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Mixed Use',
                'created_at' => '2025-07-18 13:20:44',
                'updated_at' => '2025-07-18 13:20:44',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Residential',
                'created_at' => '2025-07-18 13:21:04',
                'updated_at' => '2025-07-18 13:21:04',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Other',
                'created_at' => '2025-07-18 13:21:19',
                'updated_at' => '2025-07-18 13:21:19',
            ),
        ));
        
        
    }
}