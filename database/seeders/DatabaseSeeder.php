<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SubAreasTableSeeder::class);
        $this->call(ResolutionsTableSeeder::class);
        $this->call(RegistrationTypesTableSeeder::class);
        $this->call(RegistrationOrganizationsTableSeeder::class);
        $this->call(PropertyTypesTableSeeder::class);
        $this->call(LandUsesTableSeeder::class);
        $this->call(DistrictsTableSeeder::class);
        $this->call(AliasesTableSeeder::class);
        $this->call(DevelopmentAreasTableSeeder::class);
        $this->call(ChangeOfUseTableSeeder::class);
        $this->call(ApplicationClassificationsTableSeeder::class);
        $this->call(ApplicantTypesTableSeeder::class);
        $this->call(ApplicantTitlesTableSeeder::class);
        $this->call(RegistrationAreasTableSeeder::class);
    }
}
