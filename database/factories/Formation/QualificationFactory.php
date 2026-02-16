<?php

namespace Database\Factories\Formation;

use App\Enums\Qualification\QualificationStatus;
use App\Models\Formation\Instructeur;
use App\Models\Formation\Participant;
use App\Models\Formation\Qualification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Qualification>
 */
class QualificationFactory extends Factory
{
    protected $model = Qualification::class;

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
            'status' => QualificationStatus::EnAttente,
            'score' => $this->faker->optional()->randomFloat(2, 0, 100),
            'date_evaluation' => $this->faker->optional()->date(),
            'observation' => $this->faker->optional()->sentence(),
            'evaluateur_id' => Instructeur::factory(),
        ];
    }
}
