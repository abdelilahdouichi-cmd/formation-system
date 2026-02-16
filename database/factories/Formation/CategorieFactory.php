<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Categorie;
use App\Models\Formation\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Categorie>
 */
class CategorieFactory extends Factory
{
    protected $model = Categorie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'niveau_id' => Niveau::factory(),
            'nom' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->bothify('CAT-###'),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
