<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Justificatif;
use App\Models\Formation\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Justificatif>
 */
class JustificatifFactory extends Factory
{
    protected $model = Justificatif::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participant_id' => Participant::factory(),
            'formation_id' => function (array $attributes) {
                return Participant::find($attributes['participant_id'])->formation_id;
            },
            'diplome_id' => null,
            'licence_id' => null,
            'type' => $this->faker->randomElement(['identite', 'diplome', 'licence']),
            'nom_fichier' => $this->faker->unique()->word() . '.pdf',
            'chemin_fichier' => 'documents/' . $this->faker->unique()->uuid . '.pdf',
            'taille' => $this->faker->numberBetween(1000, 5000000),
            'mime_type' => 'application/pdf',
            'date_upload' => now(),
            'description' => $this->faker->optional()->sentence(),
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ];
    }
}
