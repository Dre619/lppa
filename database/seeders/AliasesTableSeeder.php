<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AliasesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('aliases')->delete();
        
        \DB::table('aliases')->insert(array (
            0 => 
            array (
                'id' => 3,
                'district_id' => 5,
                'alias' => 'CHONGWE',
                'created_at' => '2025-07-19 16:59:05',
                'updated_at' => '2025-07-19 16:59:05',
            ),
            1 => 
            array (
                'id' => 4,
                'district_id' => 4,
                'alias' => 'CHILANGA',
                'created_at' => '2025-07-19 16:59:22',
                'updated_at' => '2025-07-19 16:59:22',
            ),
            2 => 
            array (
                'id' => 5,
                'district_id' => 3,
                'alias' => 'KAFUE',
                'created_at' => '2025-07-19 16:59:30',
                'updated_at' => '2025-07-19 16:59:30',
            ),
            3 => 
            array (
                'id' => 6,
                'district_id' => 6,
                'alias' => 'LUSAKA',
                'created_at' => '2025-07-19 16:59:59',
                'updated_at' => '2025-07-19 16:59:59',
            ),
            4 => 
            array (
                'id' => 7,
                'district_id' => 6,
                'alias' => 'LSK',
                'created_at' => '2025-07-19 16:59:59',
                'updated_at' => '2025-07-19 16:59:59',
            ),
            5 => 
            array (
                'id' => 8,
                'district_id' => 6,
                'alias' => 'LUS',
                'created_at' => '2025-07-19 16:59:59',
                'updated_at' => '2025-07-19 16:59:59',
            ),
            6 => 
            array (
                'id' => 9,
                'district_id' => 7,
                'alias' => 'CHIRUNDU',
                'created_at' => '2025-07-19 17:01:38',
                'updated_at' => '2025-07-19 17:01:38',
            ),
            7 => 
            array (
                'id' => 10,
                'district_id' => 8,
                'alias' => 'KAFUE',
                'created_at' => '2025-07-19 17:03:09',
                'updated_at' => '2025-07-19 17:03:09',
            ),
            8 => 
            array (
                'id' => 11,
                'district_id' => 8,
                'alias' => 'CHILANGA',
                'created_at' => '2025-07-19 17:03:09',
                'updated_at' => '2025-07-19 17:03:09',
            ),
            9 => 
            array (
                'id' => 12,
                'district_id' => 8,
                'alias' => 'KAFUE DISTRICT',
                'created_at' => '2025-07-19 17:03:09',
                'updated_at' => '2025-07-19 17:03:09',
            ),
            10 => 
            array (
                'id' => 13,
                'district_id' => 8,
                'alias' => 'CHILANGA DISTRICT',
                'created_at' => '2025-07-19 17:03:09',
                'updated_at' => '2025-07-19 17:03:09',
            ),
            11 => 
            array (
                'id' => 14,
                'district_id' => 9,
                'alias' => 'LUANGWA',
                'created_at' => '2025-07-19 17:03:52',
                'updated_at' => '2025-07-19 17:03:52',
            ),
            12 => 
            array (
                'id' => 15,
                'district_id' => 10,
                'alias' => 'MAHOPO',
                'created_at' => '2025-07-19 17:04:32',
                'updated_at' => '2025-07-19 17:04:32',
            ),
            13 => 
            array (
                'id' => 16,
                'district_id' => 11,
                'alias' => 'RUFUNSA',
                'created_at' => '2025-07-19 17:05:05',
                'updated_at' => '2025-07-19 17:05:05',
            ),
            14 => 
            array (
                'id' => 17,
                'district_id' => 12,
                'alias' => 'SHIBUYUNJI',
                'created_at' => '2025-07-19 17:05:38',
                'updated_at' => '2025-07-19 17:05:38',
            ),
        ));
        
        
    }
}