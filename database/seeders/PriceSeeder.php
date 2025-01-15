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
        $equipmentList = Equipment::whereIn('category_id', [1, 2, 3])
            ->whereIn('size_id', [1, 13, 19])
            ->get();

        $contragent = Contragents::inRandomOrder()->first();

        if ($equipmentList->isEmpty() || !$contragent) {
            $this->command->error('No Equipment or Contragent records found matching the criteria. Please seed these tables first.');
            return;
        }

        foreach ($equipmentList as $equipment) {
            for ($i = 0; $i < 20; $i++) {
                $saleId = DB::table('sale')->insertGetId([
                    'equipment_id' => $equipment->id,
                    'contragent_id' => rand(1,5),
                    'sale_number' => 'SN-' . Str::random(5),
                    'sale_date' => now(),
                    'shipping_date' => now()->addDays(rand(1, 30)),
                    'commentary' => Str::random(10),
                    'status' => ['credit', 'full', 'pred'][rand(0, 2)],
                    'price' => rand(1000, 5000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                for ($j = 0; $j < 3; $j++) {
                    DB::table('sale_subequipment')->insert([
                        'equipment_id' => rand(1, 20),
                        'sale_id' => $saleId,
                        'shipping_date' => now()->addDays(rand(1, 30)),
                        'commentary' => Str::random(10),
                        'price' => rand(100, 1000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }


        
    }

}
