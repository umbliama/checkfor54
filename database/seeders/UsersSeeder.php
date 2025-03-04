<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        User::create([
            'name' => 'test',
            'lastname' => 'test',
            'email' => 'test@test.ru',
            'password' => bcrypt('123123123'),
            'isAdmin' => 1,
            'isApproved' => 1,
        ]);
        User::create([
            'name' => 'manager',
            'lastname' => 'manager',
            'email' => 'manager@manager.ru',
            'password' => bcrypt('123123123'),
            'isAdmin' => 0,
            'isApproved' => 1,
        ]);
        User::create([
            'name' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@admin.ru',
            'password' => bcrypt('123123123'),
            'isAdmin' => 1,
            'isApproved' => 1,
        ]);


    }
}
