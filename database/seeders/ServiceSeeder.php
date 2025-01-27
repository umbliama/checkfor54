<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use App\Models\Contragents;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
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
                $serviceId = DB::table('services')->insertGetId([
                    'contragent_id' => rand(1, 5),
                    'service_number' => Str::uuid(),
                    'service_date' => now(),
                    'full_income' => $faker->randomFloat(2, 1000, 5000),
                    'active' => $faker->boolean(50),
                    'hyperlink' => $faker->url(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $serviceEquipmentId = DB::table('service_equipment')->insertGetId([
                    'service_id' => $serviceId,
                    'equipment_id' => $equipment->id,
                    'shipping_date' => now()->addDays(2),
                    'period_start_date' => now()->addDays(3),
                    'return_date' => now()->addDays(10),
                    'period_end_date' => now()->addDays(20),
                    'store' => 'Warehouse 20',
                    'operating' => 'Operational',
                    'commentary' => 'Routine maintenance',
                    'return_reason' => null,
                    'income' => 1500.50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                for ($j = 0; $j < 3; $j++) {
                    DB::table('service_subequipment')->insert([
                        'subequipment_id' => $equipment->id,
                        'service_equipment_id' => $serviceEquipmentId,
                        'service_id' => $serviceId,
                        'shipping_date' => now()->addDays(2 + $j),
                        'period_start_date' => now()->addDays(3 + $j),
                        'commentary' => "Subequipment item $j commentary",
                        'return_date' => now()->addDays(12 + $j),
                        'period_end_date' => now()->addDays(22 + $j),
                        'store' => 'Warehouse 20',
                        'operating' => 'Operational',
                        'income' => rand(100, 500),
                        'return_reason' => $j % 2 === 0 ? 'project' : 'rejected',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
