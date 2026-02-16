<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Participant>
 */
class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 10) . ' days');

        return [
            'formation_id' => Formation::factory(),
            'classe_id' => null,
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->optional()->phoneNumber(),
            'entreprise' => $this->faker->optional()->company(),
            'poste' => $this->faker->optional()->jobTitle(),
            'numero_identite' => $this->faker->unique()->bothify('ID###??'),
            'date_inscription' => $startDate,
            'date_debut' => $startDate,
            'date_fin' => $endDate,
            'is_active' => true,
        ];
    }
}
