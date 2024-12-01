<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ContragentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        foreach (range(1, 35) as $index) {
            DB::table('contragents')->insert([
                'agentTypeLegal' => $faker->randomElement(['OOO', 'OAO', 'ZAO', 'PAO', 'individual']),
                'country' => $faker->country,
                'name' => $faker->company,
                'fullname' => $faker->companySuffix,
                'inn' => $faker->numerify('##########'),
                'kpp' => $faker->numerify('#########'),
                'ogrn' => $faker->numerify('#############'),
                'reason' => $faker->sentence,
                'notes' => $faker->text(50),
                'commentary' => $faker->text(100),
                'group' => $faker->word,
                'bankname' => $faker->company,
                'bank_bik' => $faker->numerify('########'),
                'bank_kpp' => $faker->numerify('#########'),
                'bank_inn' => $faker->numerify('##########'),
                'bank_rs' => $faker->numerify('##################'),
                'bank_ca' => $faker->numerify('##################'),
                'bank_commnetary' => $faker->sentence,
                'supplier' => $faker->boolean,
                'customer' => $faker->boolean,
                'address' => $faker->address,
                'site' => $faker->url,
                'phone' => $faker->phoneNumber,
                'email' => $faker->email,
                'contact_person' => $faker->name,
                'contact_person_phone' => $faker->phoneNumber,
                'contact_person_email' => $faker->email,
                'contact_person_notes' => $faker->sentence,
                'contact_person_commentary' => $faker->sentence,
                'status' => $faker->boolean,
                'avatar' => $faker->imageUrl(100, 100, 'business', true, 'avatar'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

}
