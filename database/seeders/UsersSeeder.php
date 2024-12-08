<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        foreach (range(1, 35) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'lastname' => $faker->name,
                'email' => $faker->email,
                'password' => $faker->password,
                'isAdmin' => 0,   
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
