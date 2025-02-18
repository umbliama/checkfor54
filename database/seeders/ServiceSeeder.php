<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contragents;
use App\Models\Equipment;
use App\Models\ServiceContragent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = ON;');

        for ($i = 0; $i < 20; $i++) {
            // Fetch or create a contragent
            $contragent = Contragents::inRandomOrder()->first() ?? Contragents::create([
                'agentTypeLegal' => 'individual',
                'country' => 'RU',
                'name' => 'Default Contragent',
                'inn' => '1234567890',
                'supplier' => true,
                'customer' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $asd = ServiceContragent::insert([
                'contragent_id' => $contragent->id,
            ]);

            // Insert Service   
            $serviceId = DB::table('services')->insertGetId([
                'contragent_id' => $contragent->id,
                'service_number' => Str::random(10),
                'service_date' => Carbon::now()->subDays(rand(1, 30)),
                'full_income' => rand(5000, 20000) / 100,
                'active' => rand(0, 1),
                'hyperlink' => 'http://example.com/service',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Fetch or create an equipment
            $equipment = Equipment::inRandomOrder()->first() ?? Equipment::create([
                'name' => 'Test Equipment',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Service Equipment
            $serviceEquipmentId = DB::table('service_equipment')->insertGetId([
                'service_id' => $serviceId,
                'equipment_id' => $equipment->id,
                'shipping_date' => Carbon::now()->subDays(rand(1, 10)),
                'period_start_date' => Carbon::now()->subDays(rand(10, 20)),
                'return_date' => Carbon::now()->addDays(rand(5, 15)),
                'period_end_date' => Carbon::now()->addDays(rand(10, 30)),
                'store' => 'Main Warehouse',
                'operating' => 'Operational',
                'commentary' => 'Service Equipment Record',
                'return_reason' => rand(0, 1) ? 'project' : 'rejected',
                'income' => rand(500, 5000) / 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Service Subequipment
            DB::table('service_subequipment')->insert([
                'service_id' => $serviceId,
                'service_equipment_id' => $serviceEquipmentId,
                'subequipment_id' => $equipment->id,
                'shipping_date' => Carbon::now()->subDays(rand(1, 10)),
                'period_start_date' => Carbon::now()->subDays(rand(10, 20)),
                'return_date' => Carbon::now()->addDays(rand(5, 15)),
                'period_end_date' => Carbon::now()->addDays(rand(10, 30)),
                'store' => 'Secondary Warehouse',
                'operating' => 'Standby',
                'commentary' => 'Service Subequipment Record',
                'return_reason' => rand(0, 1) ? 'project' : 'rejected',
                'income' => rand(200, 3000) / 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
