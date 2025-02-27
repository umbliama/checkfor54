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
            'category_id' => $this->faker->randomElement([1, 2, 3,4,5,6,7]),
            'size_id' => function (array $attributes) {
                if ($attributes['category_id'] == 1) {
                    return $this->faker->numberBetween(1,1);
                } elseif ($attributes['category_id'] == 2) {
                    return $this->faker->numberBetween(13, 18);
                } elseif ($attributes['category_id'] == 3) {
                    return $this->faker->numberBetween(19, 30);
                } elseif ($attributes['category_id'] == 4) {
                    return $this->faker->numberBetween(31, 42);
                } elseif ($attributes['category_id'] == 5) {
                    return $this->faker->numberBetween(43, 54);
                } elseif ($attributes['category_id'] == 6) {
                    return $this->faker->numberBetween(55,  66);
                } elseif ($attributes['category_id'] == 7) {
                    return $this->faker->numberBetween(67, 78);
                }
            },
            'location_id' =>$this->faker->numberBetween(1,2),
            'series' => $this->faker->bothify('ДР-####'),
            'length' => $this->faker->numberBetween(20, 100),
            'operating' => $this->faker->numberBetween(20, 100),
            'manufactor_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['new', 'good', 'satisfactory', 'bad', 'off']),
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
            'hyperlink' => '',
        ];

    }
}
