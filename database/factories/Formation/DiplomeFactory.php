<?php

namespace Database\Factories\Formation;

use App\Enums\Diplome\DiplomeStatus;
use App\Models\Formation\Diplome;
use App\Models\Formation\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Diplome>
 */
class DiplomeFactory extends Factory
{
    protected $model = Diplome::class;

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
            'numero_diplome' => $this->faker->unique()->bothify('DIP-#####'),
            'status' => DiplomeStatus::ADelivrer,
            'date_delivrance' => $this->faker->optional()->date(),
            'date_expiration' => $this->faker->optional()->date(),
            'lieu_delivrance' => $this->faker->optional()->city(),
            'signature_directeur' => $this->faker->boolean(20),
            'observation' => $this->faker->optional()->sentence(),
        ];
    }
}
