<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Instructeur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Instructeur>
 */
class InstructeurFactory extends Factory
{
    protected $model = Instructeur::class;

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
            'specialite' => $this->faker->optional()->jobTitle(),
            'numero_licence' => $this->faker->unique()->bothify('LIC-####'),
            'date_debut' => $this->faker->optional()->date(),
            'date_fin' => $this->faker->optional()->date(),
            'is_active' => true,
        ];
    }
}
