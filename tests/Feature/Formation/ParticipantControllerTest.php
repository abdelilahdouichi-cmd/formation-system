<?php

namespace Tests\Feature\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Formation $formation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->formation = Formation::factory()->create();
    }

    public function test_participants_index(): void
    {
        Participant::factory()->count(3)->create(['formation_id' => $this->formation->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.participants.index', $this->formation));

        $response->assertStatus(200);
        $response->assertViewHas('participants');
    }

    public function test_participants_create_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.participants.create', $this->formation));

        $response->assertStatus(200);
    }

    public function test_participants_store(): void
    {
        $participantData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'numero_identite' => 'A123456',
            'date_debut' => now()->toDateString(),
            'date_fin' => now()->addMonths(1)->toDateString(),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.participants.store', $this->formation), $participantData);

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'email' => 'jean.dupont@example.com',
            'formation_id' => $this->formation->id,
        ]);
    }

    public function test_participants_show(): void
    {
        $participant = Participant::factory()->create(['formation_id' => $this->formation->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.participants.show', ['formation' => $this->formation, 'participant' => $participant]));

        $response->assertStatus(200);
        $response->assertViewHas('participant');
    }

    public function test_participants_edit_page(): void
    {
        $participant = Participant::factory()->create(['formation_id' => $this->formation->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.participants.edit', ['formation' => $this->formation, 'participant' => $participant]));

        $response->assertStatus(200);
    }

    public function test_participants_update(): void
    {
        $participant = Participant::factory()->create(['formation_id' => $this->formation->id]);

        $updateData = [
            'prenom' => 'Jacques',
            'nom' => 'Martin',
            'email' => $participant->email,
            'numero_identite' => $participant->numero_identite,
            'date_debut' => $participant->date_debut,
            'date_fin' => $participant->date_fin->addDays(10),
        ];

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.participants.update', ['formation' => $this->formation, 'participant' => $participant]), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'prenom' => 'Jacques',
        ]);
    }

    public function test_participants_delete(): void
    {
        $participant = Participant::factory()->create(['formation_id' => $this->formation->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.participants.destroy', ['formation' => $this->formation, 'participant' => $participant]));

        $response->assertRedirect();
        $this->assertSoftDeleted('participants', ['id' => $participant->id]);
    }

    public function test_participants_deactivate(): void
    {
        $participant = Participant::factory()->create(['formation_id' => $this->formation->id, 'is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.participants.deactivate', ['formation' => $this->formation, 'participant' => $participant]));

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'is_active' => false,
        ]);
    }

    public function test_participants_activate(): void
    {
        $participant = Participant::factory()->create(['formation_id' => $this->formation->id, 'is_active' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.participants.activate', ['formation' => $this->formation, 'participant' => $participant]));

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'id' => $participant->id,
            'is_active' => true,
        ]);
    }

    public function test_email_must_be_unique(): void
    {
        Participant::factory()->create(['formation_id' => $this->formation->id, 'email' => 'test@example.com']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.participants.store', $this->formation), [
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'email' => 'test@example.com',
                'numero_identite' => 'A123456',
                'date_debut' => now()->toDateString(),
                'date_fin' => now()->addMonths(1)->toDateString(),
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_numero_identite_must_be_unique(): void
    {
        Participant::factory()->create(['formation_id' => $this->formation->id, 'numero_identite' => 'A123456']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.participants.store', $this->formation), [
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'email' => 'jean@example.com',
                'numero_identite' => 'A123456',
                'date_debut' => now()->toDateString(),
                'date_fin' => now()->addMonths(1)->toDateString(),
            ]);

        $response->assertSessionHasErrors('numero_identite');
    }
}
