<?php

namespace Database\Factories\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\SituationSCE;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\SituationSCE>
 */
class SituationSceFactory extends Factory
{
    protected $model = SituationSCE::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inscrits = $this->faker->numberBetween(10, 40);
        $presents = $this->faker->numberBetween(0, $inscrits);
        $qualifies = $this->faker->numberBetween(0, $presents);

        return [
            'formation_id' => Formation::factory(),
            'sce_name' => $this->faker->company(),
            'nombre_inscrits' => $inscrits,
            'nombre_presents' => $presents,
            'nombre_absents' => $inscrits - $presents,
            'nombre_qualifies' => $qualifies,
            'nombre_non_qualifies' => $presents - $qualifies,
            'nombre_diplomes' => $this->faker->numberBetween(0, $qualifies),
            'nombre_licences' => $this->faker->numberBetween(0, $qualifies),
            'date_rapport' => $this->faker->optional()->date(),
            'observation' => $this->faker->optional()->sentence(),
            'statut_rapport' => $this->faker->randomElement(['en cours', 'finalisé']),
        ];
    }
}
