<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create or update the default super admin
        $rawPassword = (string) config('app.admin_password');
        $password = match (true) {
            str_starts_with($rawPassword, '$2y$') => $rawPassword,
            str_starts_with($rawPassword, '$argon2i$') => $rawPassword,
            str_starts_with($rawPassword, '$argon2id$') => $rawPassword,
            default => Hash::make($rawPassword),
        };

        User::query()->updateOrCreate(
            ['email' => config('app.admin_email')],
            [
                'name' => config('app.admin_name', 'Super Admin'),
                'phone' => config('app.admin_phone'),
                'password' => $password,
                'role' => UserRole::SuperAdmin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // additional demo users
        \App\Models\User::factory()->count(8)->create();

        // create pools of niveaux/categories/sous-categories to avoid factory unique collisions
        $niveaux = \App\Models\Formation\Niveau::factory()->count(5)->make();
        foreach ($niveaux as $niveau) {
            // ensure unique niveau name
            $niveau->nom = uniqid('NIV-');
            $niveau->save();

            $categories = \App\Models\Formation\Categorie::factory()->count(3)->make(['niveau_id' => $niveau->id]);
            foreach ($categories as $categorie) {
                // ensure unique category code
                $categorie->code = uniqid('CAT-');
                $categorie->save();

                $sousList = \App\Models\Formation\SousCategorie::factory()->count(2)->make(['categorie_id' => $categorie->id]);
                foreach ($sousList as $sous) {
                    $sous->code = uniqid('SCAT-');
                    $sous->save();
                }
            }
        }

        // create formations by picking existing sous-categories
        for ($i = 0; $i < 8; $i++) {
            $sous = \App\Models\Formation\SousCategorie::inRandomOrder()->first();
            $formation = \App\Models\Formation\Formation::factory()->create([
                'niveau_id' => $sous->categorie->niveau_id,
                'categorie_id' => $sous->categorie_id,
                'sous_categorie_id' => $sous->id,
            ]);

            // classes for the formation
            $classes = \App\Models\Formation\Classe::factory()->count(rand(1, 3))->create([
                'formation_id' => $formation->id,
            ]);

            // participants and related records
            \App\Models\Formation\Participant::factory()
                ->count(rand(8, 18))
                ->create(['formation_id' => $formation->id])
                ->each(function (\App\Models\Formation\Participant $participant) use ($classes) {
                    // optionally assign to a class
                    if ($classes->count() && rand(0, 1)) {
                        $participant->classe_id = $classes->random()->id;
                        $participant->save();
                    }

                    // randomly create qualification / licence / diplome entries
                    if (rand(0, 100) < 70) {
                        \App\Models\Formation\Qualification::factory()->create([
                            'participant_id' => $participant->id,
                        ]);
                    }

                    if (rand(0, 100) < 60) {
                        \App\Models\Formation\Licence::factory()->create([
                            'participant_id' => $participant->id,
                        ]);
                    }

                    if (rand(0, 100) < 50) {
                        \App\Models\Formation\Diplome::factory()->create([
                            'participant_id' => $participant->id,
                        ]);
                    }
                });

            // add a few instructeurs and attach them via the pivot table
            $instructeurs = \App\Models\Formation\Instructeur::factory()->count(rand(1, 3))->make();
            foreach ($instructeurs as $instructeur) {
                // ensure unique numero_licence to avoid unique constraint collisions in demo data
                $instructeur->numero_licence = uniqid('LIC-');
                $instructeur->save();

                $formation->instructeurs()->attach($instructeur->id, [
                    'role' => 'instructeur',
                    'date_debut' => now()->toDateString(),
                    'date_fin' => null,
                ]);
            }

            // add a few examinateurs and attach them via the pivot table
            $examinateurs = \App\Models\Formation\Examinateur::factory()->count(rand(0, 2))->make();
            foreach ($examinateurs as $examinateur) {
                // ensure unique numero_licence
                $examinateur->numero_licence = uniqid('LIC-');
                $examinateur->save();

                $formation->examinateurs()->attach($examinateur->id, [
                    'date_examen' => now()->toDateString(),
                    'lieu' => null,
                ]);
            }
        }

        // create some standalone resources (justificatifs / situations)
        \App\Models\Formation\Justificatif::factory()->count(5)->create();
        \App\Models\Formation\SituationSCE::factory()->count(5)->create();

        $this->command->info('Database seeded with demo data.');
    }
}
