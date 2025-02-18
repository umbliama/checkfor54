<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            EquipmentSeeder::class,
            ContragentsTableSeeder::class,
            MoveSeeder::class,
            // TestsRepairsSeeder::class,
            SaleSeeder::class,   
            ServiceSeeder::class
        ]);
    }
}
