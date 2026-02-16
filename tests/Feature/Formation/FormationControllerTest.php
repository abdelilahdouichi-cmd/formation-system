<?php

namespace Tests\Feature\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\Niveau;
use App\Models\Formation\Categorie;
use App\Models\Formation\SousCategorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_formations_index(): void
    {
        Formation::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.formations.index'));

        $response->assertStatus(200);
        $response->assertViewHas('formations');
    }

    public function test_formations_create_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.formations.create'));

        $response->assertStatus(200);
    }

    public function test_formations_store(): void
    {
        $niveau = Niveau::factory()->create();
        $categorie = Categorie::factory()->create(['niveau_id' => $niveau->id]);
        $sousCategorie = SousCategorie::factory()->create(['categorie_id' => $categorie->id]);

        $formationData = [
            'code' => 'FORM-001',
            'nom' => 'Formation Test',
            'description' => 'Description test',
            'niveau_id' => $niveau->id,
            'categorie_id' => $categorie->id,
            'sous_categorie_id' => $sousCategorie->id,
            'date_debut' => now()->toDateString(),
            'date_fin' => now()->addMonths(1)->toDateString(),
            'lieu' => 'Lieu test',
            'duree_heures' => 40,
            'nombre_participants' => 20,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.formations.store'), $formationData);

        $response->assertRedirect();
        $this->assertDatabaseHas('formations', [
            'code' => 'FORM-001',
            'nom' => 'Formation Test',
        ]);
    }

    public function test_formations_show(): void
    {
        $formation = Formation::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.formations.show', $formation));

        $response->assertStatus(200);
        $response->assertViewHas('formation');
    }

    public function test_formations_edit_page(): void
    {
        $formation = Formation::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.formations.edit', $formation));

        $response->assertStatus(200);
    }

    public function test_formations_update(): void
    {
        $formation = Formation::factory()->create();

        $updateData = [
            'nom' => 'Formation Updated',
            'description' => 'Updated description',
            'duree_heures' => 50,
        ];

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.formations.update', $formation), array_merge([
                'code' => $formation->code,
                'niveau_id' => $formation->niveau_id,
                'categorie_id' => $formation->categorie_id,
                'sous_categorie_id' => $formation->sous_categorie_id,
                'date_debut' => $formation->date_debut,
                'date_fin' => $formation->date_fin,
            ], $updateData));

        $response->assertRedirect();
        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'nom' => 'Formation Updated',
        ]);
    }

    public function test_formations_delete(): void
    {
        $formation = Formation::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.formations.destroy', $formation));

        $response->assertRedirect();
        $this->assertSoftDeleted('formations', ['id' => $formation->id]);
    }

    public function test_formations_execute_transitions_status(): void
    {
        $formation = Formation::factory()->create(['status' => 'planifiée']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.formations.execute', $formation));

        $response->assertRedirect();
        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'exécutée',
        ]);
    }

    public function test_formations_cancel(): void
    {
        $formation = Formation::factory()->create(['status' => 'planifiée']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.formations.cancel', $formation));

        $response->assertRedirect();
        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'annulée',
        ]);
    }

    public function test_formations_archive(): void
    {
        $formation = Formation::factory()->create(['status' => 'exécutée']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.formations.archive', $formation));

        $response->assertRedirect();
        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'archivée',
        ]);
    }

    public function test_non_admin_cannot_create_formation(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->get(route('admin.formations.create'));

        $response->assertStatus(403);
    }

    public function test_code_must_be_unique(): void
    {
        $niveau = Niveau::factory()->create();
        $categorie = Categorie::factory()->create(['niveau_id' => $niveau->id]);
        $sousCategorie = SousCategorie::factory()->create(['categorie_id' => $categorie->id]);

        Formation::factory()->create(['code' => 'FORM-001']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.formations.store'), [
                'code' => 'FORM-001',
                'nom' => 'Formation Test 2',
                'niveau_id' => $niveau->id,
                'categorie_id' => $categorie->id,
                'sous_categorie_id' => $sousCategorie->id,
                'date_debut' => now()->toDateString(),
                'date_fin' => now()->addMonths(1)->toDateString(),
            ]);

        $response->assertSessionHasErrors('code');
    }
}
