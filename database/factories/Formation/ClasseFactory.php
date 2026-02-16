<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Classe;
use App\Models\Formation\Formation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Classe>
 */
class ClasseFactory extends Factory
{
    protected $model = Classe::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 week', '+1 week');

        return [
            'formation_id' => Formation::factory(),
            'nom' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->bothify('CLS-####'),
            'capacite' => $this->faker->numberBetween(15, 40),
            'date_debut' => $startDate,
            'date_fin' => (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 10) . ' days'),
            'lieu' => $this->faker->optional()->city(),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
