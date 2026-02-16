<?php

namespace Database\Factories\Formation;

use App\Enums\Licence\LicenceStatus;
use App\Models\Formation\Licence;
use App\Models\Formation\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Licence>
 */
class LicenceFactory extends Factory
{
    protected $model = Licence::class;

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
            'numero_licence' => $this->faker->unique()->bothify('LIC-#####'),
            'status' => LicenceStatus::ADelivrer,
            'date_delivrance' => $this->faker->optional()->date(),
            'date_expiration' => $this->faker->optional()->date(),
            'date_renouvellement' => $this->faker->optional()->date(),
            'organisme_delivrance' => $this->faker->optional()->company(),
            'observation' => $this->faker->optional()->sentence(),
        ];
    }
}
