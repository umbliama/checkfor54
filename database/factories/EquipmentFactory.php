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
            'length' => $this->faker->optional()->numberBetween(20, 100),
            'operating' => $this->faker->optional()->numberBetween(20, 100),
           'manufactor_date' => $this->faker->optional()->date(),
           'status' => $this->faker->optional()->randomElement(['new', 'good','satisfactory', 'bad', 'off']),
            'notes' => $this->faker->optional()->sentence(),
            'price' => $this->faker->optional()->numberBetween(4000, 50000),
            'commentary' => $this->faker->optional()->paragraph(),
            'zahodnost' => $this->faker->optional()->word,
           'stator_rotor' => $this->faker->optional()->word,
            'dlina_ds' => $this->faker->optional()->word,
            'narabotka_ds' => $this->faker->optional()->word,
           'rezbi' => $this->faker->optional()->word,
            'length_rezba' => $this->faker->optional()->word,
            'diameter' => $this->faker->optional()->word,
            'hyperlink' => $this->faker->optional()->url,
        ];
    }
}
