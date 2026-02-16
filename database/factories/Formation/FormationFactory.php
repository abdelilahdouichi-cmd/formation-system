<?php

namespace Database\Factories\Formation;

use App\Enums\Formation\FormationStatus;
use App\Models\Formation\Categorie;
use App\Models\Formation\Formation;
use App\Models\Formation\Niveau;
use App\Models\Formation\SousCategorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation\Formation>
 */
class FormationFactory extends Factory
{
    protected $model = Formation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $niveau = Niveau::factory();
        $categorie = Categorie::factory()->for($niveau);
        $sousCategorie = SousCategorie::factory()->for($categorie);
        $startDate = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $minParticipants = $this->faker->numberBetween(5, 10);

        return [
            'niveau_id' => $niveau,
            'categorie_id' => $categorie,
            'sous_categorie_id' => $sousCategorie,
            'nom' => $this->faker->words(3, true),
            'code' => $this->faker->unique()->bothify('FORM-####'),
            'description' => $this->faker->optional()->sentence(),
            'objectif' => $this->faker->optional()->sentence(),
            'duree_heures' => $this->faker->numberBetween(8, 80),
            'prix' => $this->faker->randomFloat(2, 100, 1500),
            'date_debut_prevue' => $startDate,
            'date_fin_prevue' => (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 10) . ' days'),
            'status' => FormationStatus::Planifiee,
            'lieu' => $this->faker->optional()->city(),
            'nombreParticipantsMin' => $minParticipants,
            'nombreParticipantsMax' => $this->faker->numberBetween($minParticipants, $minParticipants + 20),
            'is_active' => true,
        ];
    }
}
