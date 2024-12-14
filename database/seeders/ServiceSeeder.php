<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use App\Models\Contragents;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                    'equipment_id' => $equipment->id,
                    'contragent_id' => $contragent->id,
                    'service_number' => Str::uuid(),
                    'service_date' => now(),
                    'shipping_date' => now()->addDays(2),
                    'period_start_date' => now()->addDays(3),
                    'return_date' => now()->addDays(10),
                    'period_end_date' => now()->addDays(20),
                    'store' => '20',
                    'operating' => '20',
                    'commentary' => 'Routine maintenance',
                    'return_reason' => null,
                    'active' => false,
                    'income' => 1500.50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                for ($j = 0; $j < 3; $j++) {
                    DB::table('service_subequipment')->insert([
                        'subequipment_id' => $equipment->id,
                        'service_id' => $serviceId,
                        'shipping_date' => now()->addDays(2 + $j),
                        'period_start_date' => now()->addDays(3 + $j),
                        'commentary' => "Subequipment item $j commentary",
                        'return_date' => now()->addDays(12 + $j),
                        'period_end_date' => now()->addDays(22 + $j),
                        'store' => '20',
                        'operating' => '20',
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
