<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Examinateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Examinateur>
 */
class ExaminateurFactory extends Factory
{
    protected $model = Examinateur::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->optional()->phoneNumber(),
            'numero_licence' => $this->faker->unique()->bothify('LIC-####'),
            'organisme' => $this->faker->optional()->company(),
            'date_debut' => $this->faker->optional()->date(),
            'date_fin' => $this->faker->optional()->date(),
            'is_active' => true,
        ];
    }
}
