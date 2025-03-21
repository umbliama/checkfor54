<?php

namespace Database\Seeders;

use App\Models\Contragents;
use App\Models\Equipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;


class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $equipmentItems = DB::table('equipment')->select('category_id', 'size_id')->get();


        $equipmentArray = $equipmentItems->toArray();

        for ($i = 0; $i < 50; $i++) {

            DB::table('equipment_prices')->insert([
                'notes' => $faker->sentence(),
                'category_id' => $faker->randomElement([1, 2, 3]),
                'size_id' => $faker->numberBetween(1, 20),
                'contragent_id' => $faker->numberBetween(1, 8),
                'hyperlink' => '',
                'created_at' => now(),
                'store_date' => now(),
                'archive' => $faker->randomElement([0, 1]),
                'updated_at' => now(),
                'store_price' => $faker->numberBetween(10, 100),
                'operation_price' => $faker->numberBetween(10, 100),
            ]);
        }



        
    }

}
