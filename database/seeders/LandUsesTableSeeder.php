<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LandUsesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('land_uses')->delete();
        
        \DB::table('land_uses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Agricultural',
                'created_at' => '2025-07-18 14:24:57',
                'updated_at' => '2025-07-18 14:24:57',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Residential',
                'created_at' => '2025-07-18 14:25:12',
                'updated_at' => '2025-07-18 14:25:12',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Light Industrial',
                'created_at' => '2025-07-18 14:25:27',
                'updated_at' => '2025-07-18 14:25:27',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Industrial',
                'created_at' => '2025-07-18 14:25:44',
                'updated_at' => '2025-07-18 14:25:44',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Place of Worship',
                'created_at' => '2025-07-18 14:26:04',
                'updated_at' => '2025-07-18 14:26:04',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Institutional',
                'created_at' => '2025-07-18 14:26:17',
                'updated_at' => '2025-07-18 14:26:17',
            ),
        ));
        
        
    }
}