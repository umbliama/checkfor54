<?php

namespace Database\Seeders;

use App\Models\SaleContragent;
use Illuminate\Database\Seeder;
use App\Models\Contragents;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = ON;');
        
        $equipmentList = Equipment::whereIn('category_id', [1, 2, 3])
            ->whereIn('size_id', [1, 13, 19])
            ->get();

        for ($i = 0; $i < 20; $i++) {
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

            $sa123 = SaleContragent::insert([
                'contragent_id' => $contragent->id
            ]);
            $saleId = DB::table('sale')->insertGetId([
                'contragent_id' => $contragent->id,
                'sale_number' => Str::random(10),
                'sale_date' => Carbon::now()->subDays(5),
                'status' => 'full',
                'price' => rand(1000, 5000),
                'commentary' => 'Sample sale record',
                'shipping_date' => Carbon::now(),
                'hyperlink' => 'http://example.com/sale',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $equipment = Equipment::inRandomOrder()->first() ?? Equipment::create([
                'name' => 'Test Equipment',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $saleEquipmentId = DB::table('sale_equipment')->insertGetId([
                'sale' => $saleId,
                'equipment_id' => $equipment->id,
                'shipping_date' => Carbon::now()->addDays(2),
                'commentary' => 'Equipment commentary',
                'price' => rand(500, 1500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('sale_subequipment')->insert([
                'equipment_id' => $equipment->id,
                'sale_id' => $saleId,
                'shipping_date' => Carbon::now()->addDays(3),
                'commentary' => 'Subequipment commentary',
                'sale_equipment_id' => $saleEquipmentId,
                'price' => rand(200, 800),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('sale_extra')->insert([
                'shipping_date' => Carbon::now()->addDays(1),
                'type' => 'transfer',
                'commentary' => 'Extra sale commentary',
                'sale_id' => $saleId,
                'price' => rand(100, 500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}