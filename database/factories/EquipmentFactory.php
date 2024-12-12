<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Equipment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Equipment::class;

    public function definition(): array
    {


        return [
            'manufactor' => $this->faker->company,
            'category_id' => \App\Models\EquipmentCategories::inRandomOrder()->first()->id?? null,
           'size_id' => \App\Models\EquipmentSize::inRandomOrder()->first()->id?? null,
            'location_id' => \App\Models\EquipmentLocation::inRandomOrder()->first()->id?? null,
           'series' => $this->faker->bothify('ДР-####'),
            'length' => $this->faker->numberBetween(20, 100),
            'operating' => $this->faker->numberBetween(20, 100),
           'manufactor_date' => $this->faker->date(),
           'status' => $this->faker->randomElement(['new', 'good','satisfactory', 'bad', 'off']),
            'notes' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(4000, 50000),
            'commentary' => $this->faker->paragraph(),
            'zahodnost' => $this->faker->numberBetween(20, 100),
           'stator_rotor' => $this->faker->numberBetween(20, 100),
            'dlina_ds' => $this->faker->numberBetween(20, 100),
            'narabotka_ds' => $this->faker->numberBetween(20, 100),
           'rezbi' => $this->faker->numberBetween(20, 100),
            'length_rezba' => $this->faker->numberBetween(20, 100),
            'diameter' => $this->faker->numberBetween(20, 100),
            'hyperlink' => $this->faker->url,
        ];
    }
}
