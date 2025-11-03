<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DistrictsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('districts')->delete();
        
        \DB::table('districts')->insert(array (
            0 => 
            array (
                'id' => 3,
                'name' => 'KAFUE DISTRICT',
                'created_at' => '2025-07-18 10:46:52',
                'updated_at' => '2025-07-19 15:55:03',
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'CHILANGA DISTRICT',
                'created_at' => '2025-07-19 15:55:39',
                'updated_at' => '2025-07-19 15:55:39',
            ),
            2 => 
            array (
                'id' => 5,
                'name' => 'CHONGWE DISTRICT',
                'created_at' => '2025-07-19 15:57:32',
                'updated_at' => '2025-07-19 16:58:45',
            ),
            3 => 
            array (
                'id' => 6,
                'name' => 'LUSAKA DISTRICT',
                'created_at' => '2025-07-19 16:59:59',
                'updated_at' => '2025-07-19 16:59:59',
            ),
            4 => 
            array (
                'id' => 7,
                'name' => 'CHIRUNDU DISTRICT',
                'created_at' => '2025-07-19 17:01:38',
                'updated_at' => '2025-07-19 17:01:38',
            ),
            5 => 
            array (
                'id' => 8,
                'name' => 'KAFUE/CHILANGA DISTRICT',
                'created_at' => '2025-07-19 17:03:09',
                'updated_at' => '2025-07-19 17:03:09',
            ),
            6 => 
            array (
                'id' => 9,
                'name' => 'LUANGWA DISTRICT',
                'created_at' => '2025-07-19 17:03:52',
                'updated_at' => '2025-07-19 17:03:52',
            ),
            7 => 
            array (
                'id' => 10,
                'name' => 'MAHOPO DISTRICT',
                'created_at' => '2025-07-19 17:04:32',
                'updated_at' => '2025-07-19 17:04:32',
            ),
            8 => 
            array (
                'id' => 11,
                'name' => 'RUFUNSA DISTRICT',
                'created_at' => '2025-07-19 17:05:05',
                'updated_at' => '2025-07-19 17:05:05',
            ),
            9 => 
            array (
                'id' => 12,
                'name' => 'SHIBUYUNJI DISTRICT',
                'created_at' => '2025-07-19 17:05:38',
                'updated_at' => '2025-07-19 17:05:38',
            ),
        ));
        
        
    }
}