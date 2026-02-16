<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Categorie;
use App\Models\Formation\SousCategorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\SousCategorie>
 */
class SousCategorieFactory extends Factory
{
    protected $model = SousCategorie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categorie_id' => Categorie::factory(),
            'nom' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->bothify('SCAT-###'),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
