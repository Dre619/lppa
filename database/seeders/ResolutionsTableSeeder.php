<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ResolutionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('resolutions')->delete();
        
        \DB::table('resolutions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'resolution_type' => 'Approval',
                'description' => '',
                'created_at' => '2025-07-18 11:48:36',
                'updated_at' => '2025-07-18 11:48:36',
            ),
            1 => 
            array (
                'id' => 2,
                'resolution_type' => 'Approval with conditions',
                'description' => 'An application can be approved with set conditions',
                'created_at' => '2025-07-18 11:50:55',
                'updated_at' => '2025-07-18 11:50:55',
            ),
            2 => 
            array (
                'id' => 3,
                'resolution_type' => 'Approval with advice',
                'description' => 'Approval with advice',
                'created_at' => '2025-07-18 11:51:19',
                'updated_at' => '2025-07-18 11:51:19',
            ),
            3 => 
            array (
                'id' => 4,
                'resolution_type' => 'Deferral',
                'description' => '',
                'created_at' => '2025-07-18 11:51:36',
                'updated_at' => '2025-07-18 11:51:36',
            ),
            4 => 
            array (
                'id' => 5,
                'resolution_type' => 'Pending',
                'description' => '',
                'created_at' => '2025-07-18 11:51:51',
                'updated_at' => '2025-07-18 11:51:51',
            ),
            5 => 
            array (
                'id' => 6,
                'resolution_type' => 'RECOMMENDED',
                'description' => '',
                'created_at' => '2025-07-18 11:52:03',
                'updated_at' => '2025-07-19 17:45:46',
            ),
            6 => 
            array (
                'id' => 7,
                'resolution_type' => 'Referral',
                'description' => '',
                'created_at' => '2025-07-18 11:52:17',
                'updated_at' => '2025-07-18 11:52:17',
            ),
            7 => 
            array (
                'id' => 8,
                'resolution_type' => 'Refusal',
                'description' => '',
                'created_at' => '2025-07-18 11:52:31',
                'updated_at' => '2025-07-18 11:52:31',
            ),
            8 => 
            array (
                'id' => 9,
                'resolution_type' => 'REFERRAL to Technical Committee meeting',
                'description' => '',
                'created_at' => '2025-07-19 17:44:39',
                'updated_at' => '2025-07-19 17:44:39',
            ),
            9 => 
            array (
                'id' => 10,
                'resolution_type' => 'REFERAL to Board Meeting',
                'description' => '',
                'created_at' => '2025-07-19 17:44:56',
                'updated_at' => '2025-07-19 17:44:56',
            ),
            10 => 
            array (
                'id' => 11,
                'resolution_type' => 'PENDING FOR INSPECTION',
                'description' => '',
                'created_at' => '2025-07-19 17:45:10',
                'updated_at' => '2025-07-19 17:45:10',
            ),
        ));
        
        
    }
}