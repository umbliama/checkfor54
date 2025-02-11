<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MoveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipmentMoves = [];

        for ($i = 1; $i <= 15; $i++) {
            $equipmentMoves[] = [
                'send_date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
                'departure_date' => Carbon::now()->addDays(rand(31, 60))->format('Y-m-d'),
                'from' => 2, 
                'to' => 1, 
                'reason' => 'Reason for move ' . $i,
                'expense' => rand(100, 1000),
                'category_id' => rand(1, 5), 
                'size_id' => rand(1, 5), 
                'series' => 'Series ' . $i,
                'equipment_id' => rand(1, 10), 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('equipment_moves')->insert($equipmentMoves);
    }

}
