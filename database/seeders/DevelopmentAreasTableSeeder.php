<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentAreasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('development_areas')->delete();
        
        \DB::table('development_areas')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'CHILANGA',
                'created_at' => '2025-07-19 17:21:55',
                'updated_at' => '2025-07-19 17:21:55',
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'CHIAWA',
                'created_at' => '2025-07-19 17:24:18',
                'updated_at' => '2025-07-19 17:24:18',
            ),
            2 => 
            array (
                'id' => 5,
                'name' => 'CHADIZA',
                'created_at' => '2025-07-19 17:24:34',
                'updated_at' => '2025-07-19 17:24:34',
            ),
            3 => 
            array (
                'id' => 6,
                'name' => 'CHILAMGA',
                'created_at' => '2025-07-19 17:24:46',
                'updated_at' => '2025-07-19 17:24:46',
            ),
            4 => 
            array (
                'id' => 7,
                'name' => 'CHIPATA',
                'created_at' => '2025-07-19 17:25:27',
                'updated_at' => '2025-07-19 17:25:27',
            ),
            5 => 
            array (
                'id' => 8,
                'name' => 'CHINGOLA',
                'created_at' => '2025-07-19 17:25:37',
                'updated_at' => '2025-07-19 17:25:37',
            ),
            6 => 
            array (
                'id' => 9,
                'name' => 'CHILILABOMBWE',
                'created_at' => '2025-07-19 17:25:52',
                'updated_at' => '2025-07-19 17:25:52',
            ),
            7 => 
            array (
                'id' => 10,
                'name' => 'CHONGWE',
                'created_at' => '2025-07-19 17:26:27',
                'updated_at' => '2025-07-19 17:26:27',
            ),
            8 => 
            array (
                'id' => 11,
                'name' => 'CHIRUNDU',
                'created_at' => '2025-07-19 17:26:54',
                'updated_at' => '2025-07-19 17:26:54',
            ),
            9 => 
            array (
                'id' => 12,
                'name' => 'CHOMA',
                'created_at' => '2025-07-19 17:27:15',
                'updated_at' => '2025-07-19 17:27:15',
            ),
            10 => 
            array (
                'id' => 13,
                'name' => 'CHLANGA',
                'created_at' => '2025-07-19 17:27:24',
                'updated_at' => '2025-07-19 17:27:24',
            ),
            11 => 
            array (
                'id' => 14,
                'name' => 'KAFUE',
                'created_at' => '2025-07-19 17:27:58',
                'updated_at' => '2025-07-19 17:27:58',
            ),
            12 => 
            array (
                'id' => 15,
                'name' => 'KASAMA',
                'created_at' => '2025-07-19 17:28:20',
                'updated_at' => '2025-07-19 17:28:20',
            ),
            13 => 
            array (
                'id' => 16,
                'name' => 'KABWE',
                'created_at' => '2025-07-19 17:28:30',
                'updated_at' => '2025-07-19 17:28:30',
            ),
            14 => 
            array (
                'id' => 17,
                'name' => 'KAOMA',
                'created_at' => '2025-07-19 17:29:01',
                'updated_at' => '2025-07-19 17:29:01',
            ),
            15 => 
            array (
                'id' => 18,
                'name' => 'KITWE',
                'created_at' => '2025-07-19 17:29:40',
                'updated_at' => '2025-07-19 17:29:40',
            ),
            16 => 
            array (
                'id' => 19,
                'name' => 'LIBALA',
                'created_at' => '2025-07-19 17:29:53',
                'updated_at' => '2025-07-19 17:29:53',
            ),
            17 => 
            array (
                'id' => 20,
                'name' => 'KATETE',
                'created_at' => '2025-07-19 17:30:04',
                'updated_at' => '2025-07-19 17:30:04',
            ),
            18 => 
            array (
                'id' => 21,
                'name' => 'KAWAMBWA',
                'created_at' => '2025-07-19 17:30:15',
                'updated_at' => '2025-07-19 17:30:15',
            ),
            19 => 
            array (
                'id' => 22,
                'name' => 'LILONGWE',
                'created_at' => '2025-07-19 17:30:24',
                'updated_at' => '2025-07-19 17:30:24',
            ),
            20 => 
            array (
                'id' => 23,
                'name' => 'LUSAKA',
                'created_at' => '2025-07-19 17:31:02',
                'updated_at' => '2025-07-19 17:31:02',
            ),
            21 => 
            array (
                'id' => 24,
                'name' => 'LUSAKA EAST',
                'created_at' => '2025-07-19 17:31:31',
                'updated_at' => '2025-07-19 17:31:31',
            ),
            22 => 
            array (
                'id' => 25,
                'name' => 'LUANGWA',
                'created_at' => '2025-07-19 17:31:48',
                'updated_at' => '2025-07-19 17:31:48',
            ),
            23 => 
            array (
                'id' => 26,
                'name' => 'LUSAKA NORTH',
                'created_at' => '2025-07-19 17:32:35',
                'updated_at' => '2025-07-19 17:32:35',
            ),
            24 => 
            array (
                'id' => 27,
                'name' => 'LUSAKA WEST',
                'created_at' => '2025-07-19 17:32:43',
                'updated_at' => '2025-07-19 17:32:43',
            ),
            25 => 
            array (
                'id' => 28,
                'name' => 'LUSAKA SOUTH',
                'created_at' => '2025-07-19 17:32:55',
                'updated_at' => '2025-07-19 17:32:55',
            ),
            26 => 
            array (
                'id' => 29,
                'name' => 'MAKENI',
                'created_at' => '2025-07-19 17:33:50',
                'updated_at' => '2025-07-19 17:33:50',
            ),
            27 => 
            array (
                'id' => 30,
                'name' => 'SHIBUYUNJI',
                'created_at' => '2025-07-19 17:33:59',
                'updated_at' => '2025-07-19 17:33:59',
            ),
            28 => 
            array (
                'id' => 31,
                'name' => 'RUFUNSA',
                'created_at' => '2025-07-19 17:34:08',
                'updated_at' => '2025-07-19 17:34:08',
            ),
        ));
        
        
    }
}