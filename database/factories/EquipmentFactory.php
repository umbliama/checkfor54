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
            'category_id' => \App\Models\EquipmentCategories::inRandomOrder()->first()->id ?? null,  // Assuming you have categories
            'size_id' => \App\Models\EquipmentSize::inRandomOrder()->first()->id ?? null,  // Assuming you have sizes
            'series' => $this->faker->bothify('##-###-???'),
            'manufactor_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['new', 'good', 'satisfactory', 'bad', 'off']),
            'notes' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 50000),
            'commentary' => $this->faker->paragraph(),
        ];
    }
}
