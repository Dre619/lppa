<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegistrationAreasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('registration_areas')->delete();
        
        \DB::table('registration_areas')->insert(array (
            0 => 
            array (
                'id' => 4,
                'district_id' => 6,
                'name' => 'LUSAKA',
                'area_key' => 'LUS',
                'created_at' => '2025-07-19 17:09:27',
                'updated_at' => '2025-07-19 17:09:27',
            ),
            1 => 
            array (
                'id' => 5,
                'district_id' => 5,
                'name' => 'CHONGWE',
                'area_key' => 'CHON',
                'created_at' => '2025-07-19 17:10:34',
                'updated_at' => '2025-07-19 17:10:34',
            ),
            2 => 
            array (
                'id' => 6,
                'district_id' => 4,
                'name' => 'CHILANGA',
                'area_key' => 'CHIL',
                'created_at' => '2025-07-19 17:10:53',
                'updated_at' => '2025-07-19 17:10:53',
            ),
            3 => 
            array (
                'id' => 7,
                'district_id' => 3,
                'name' => 'KAFUE',
                'area_key' => 'KAF',
                'created_at' => '2025-07-19 17:11:22',
                'updated_at' => '2025-07-19 17:11:22',
            ),
            4 => 
            array (
                'id' => 8,
                'district_id' => 7,
                'name' => 'CHIRUNDU',
                'area_key' => 'CHIR',
                'created_at' => '2025-07-19 17:14:24',
                'updated_at' => '2025-07-19 17:14:24',
            ),
            5 => 
            array (
                'id' => 9,
                'district_id' => 7,
                'name' => 'CHIRUNDU',
                'area_key' => 'CHIRU',
                'created_at' => '2025-07-19 17:14:56',
                'updated_at' => '2025-07-19 17:14:56',
            ),
            6 => 
            array (
                'id' => 10,
                'district_id' => 9,
                'name' => 'LUANGWA',
                'area_key' => 'LUA',
                'created_at' => '2025-07-19 17:15:42',
                'updated_at' => '2025-07-19 17:15:42',
            ),
            7 => 
            array (
                'id' => 11,
                'district_id' => 9,
                'name' => 'LUANGWA',
                'area_key' => 'LUAN',
                'created_at' => '2025-07-19 17:16:24',
                'updated_at' => '2025-07-19 17:16:24',
            ),
            8 => 
            array (
                'id' => 12,
                'district_id' => 10,
                'name' => 'MAHOPO',
                'area_key' => 'MON',
                'created_at' => '2025-07-19 17:17:03',
                'updated_at' => '2025-07-19 17:17:03',
            ),
            9 => 
            array (
                'id' => 13,
                'district_id' => 5,
            'name' => 'CHONGWE (NGWE)',
                'area_key' => 'NGWE',
                'created_at' => '2025-07-19 17:17:55',
                'updated_at' => '2025-07-19 17:17:55',
            ),
            10 => 
            array (
                'id' => 14,
                'district_id' => 5,
            'name' => 'CHONGWE (PET)',
                'area_key' => 'PET',
                'created_at' => '2025-07-19 17:19:10',
                'updated_at' => '2025-07-19 17:19:10',
            ),
            11 => 
            array (
                'id' => 15,
                'district_id' => 11,
                'name' => 'RUFUNSA',
                'area_key' => 'RUF',
                'created_at' => '2025-07-19 17:19:39',
                'updated_at' => '2025-07-19 17:19:39',
            ),
            12 => 
            array (
                'id' => 16,
                'district_id' => 12,
                'name' => 'SHIBUYUNJI',
                'area_key' => 'SHIBU',
                'created_at' => '2025-07-19 17:20:11',
                'updated_at' => '2025-07-19 17:20:11',
            ),
        ));
        
        
    }
}