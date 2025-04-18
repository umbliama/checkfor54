<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class TestsRepairsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        $equipmentItems = DB::table('equipment')->select('category_id', 'size_id', 'series')->get();
        $locations = DB::table('equipment_locations')->pluck('id');

        if ($equipmentItems->isEmpty()) {
            $this->command->warn('No equipment records found. Please populate the equipment table first.');
            return;
        }

        if ($locations->isEmpty()) {
            $this->command->warn('No locations found. Please populate the equipment_locations table first.');
            return;
        }

        // Convert equipment collection to an array for easier access
        $equipmentArray = $equipmentItems->toArray();

        // Generate data for equipment_tests
        for ($i = 0; $i < 50; $i++) {
            $equipment = $faker->randomElement($equipmentArray);
            $locationId = $faker->randomElement($locations);

            DB::table('equipment_tests')->insert([
                'on_test_date' => $faker->date(),
                'test_date' => $faker->date(),
                'location_id' => $locationId,
                'expense' => $faker->numberBetween(100, 10000),
                'description' => $faker->sentence(),
                'category_id' => $equipment->category_id,
                'equipment_id' => 1,
                'size_id' => $equipment->size_id,
                'series' => $equipment->series,
                'mex_vverx' => $faker->numberBetween(100, 10000),
                'mex_vniz' => $faker->numberBetween(100, 10000),
                'usilie' => $faker->numberBetween(100, 10000),
                'delay' => $faker->numberBetween(100, 10000),
                'hyperlink' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generate data for equipment_repairs
        for ($i = 0; $i < 50; $i++) {
            $equipment = $faker->randomElement($equipmentArray);
            $locationId = $faker->randomElement($locations);

            DB::table('equipment_repairs')->insert([
                'on_repair_date' => $faker->date(),
                'repair_date' => $faker->date(),
                'location_id' => $locationId,
                'expense' => $faker->numberBetween(100, 10000),
                'description' => $faker->sentence(),
                'category_id' => $equipment->category_id,
                'equipment_id' => 1,
                'size_id' => $equipment->size_id,
                'series' => $equipment->series,
                'hyperlink' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

}
