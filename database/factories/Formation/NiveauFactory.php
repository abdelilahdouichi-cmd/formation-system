<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Niveau>
 */
class NiveauFactory extends Factory
{
    protected $model = Niveau::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->words(2, true),
            'ordre' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
