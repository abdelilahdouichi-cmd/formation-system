<?php

namespace Tests\Unit\Formation;

use App\Models\Formation\Classe;
use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use App\Models\User;
use App\Services\Formation\ClasseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClasseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ClasseService $classeService;
    protected Formation $formation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);

        $this->classeService = new ClasseService();
        $this->formation = Formation::factory()->create();
    }

    public function test_create_classe(): void
    {
        $data = [
            'formation_id' => $this->formation->id,
            'code' => 'CLASS-001',
            'nom' => 'Classe A',
            'capacite_maximal' => 30,
            'date_debut' => now(),
            'date_fin' => now()->addMonths(1),
        ];

        $classe = $this->classeService->createClasse($data);

        $this->assertDatabaseHas('classes', [
            'id' => $classe->id,
            'code' => 'CLASS-001',
        ]);
    }

    public function test_update_classe(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id]);

        $updateData = [
            'nom' => 'Classe Updated',
            'capacite_maximal' => 40,
        ];

        $updated = $this->classeService->updateClasse($classe, $updateData);

        $this->assertDatabaseHas('classes', [
            'id' => $classe->id,
            'nom' => 'Classe Updated',
            'capacite_maximal' => 40,
        ]);
    }

    public function test_delete_classe_without_participants(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id]);

        $this->classeService->deleteClasse($classe);

        $this->assertSoftDeleted('classes', ['id' => $classe->id]);
    }

    public function test_cannot_delete_classe_with_participants(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id]);
        Participant::factory()->create(['classe_id' => $classe->id, 'formation_id' => $this->formation->id]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Impossible de supprimer une classe contenant des participants.');

        $this->classeService->deleteClasse($classe);
    }

    public function test_get_enrolled_count(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id]);
        Participant::factory()->count(5)->create(['classe_id' => $classe->id, 'formation_id' => $this->formation->id, 'is_active' => true]);
        Participant::factory()->count(2)->create(['classe_id' => $classe->id, 'formation_id' => $this->formation->id, 'is_active' => false]);

        $count = $this->classeService->getEnrolledCount($classe);

        $this->assertEquals(5, $count);
    }

    public function test_can_enroll_participant_when_capacity_available(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id, 'capacite_maximal' => 30]);
        Participant::factory()->count(25)->create(['classe_id' => $classe->id, 'formation_id' => $this->formation->id, 'is_active' => true]);

        $canEnroll = $this->classeService->canEnrollParticipant($classe);

        $this->assertTrue($canEnroll);
    }

    public function test_cannot_enroll_participant_when_at_capacity(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id, 'capacite_maximal' => 30]);
        Participant::factory()->count(30)->create(['classe_id' => $classe->id, 'formation_id' => $this->formation->id, 'is_active' => true]);

        $canEnroll = $this->classeService->canEnrollParticipant($classe);

        $this->assertFalse($canEnroll);
    }

    public function test_get_remaining_capacity(): void
    {
        $classe = Classe::factory()->create(['formation_id' => $this->formation->id, 'capacite_maximal' => 30]);
        Participant::factory()->count(12)->create(['classe_id' => $classe->id, 'formation_id' => $this->formation->id, 'is_active' => true]);

        $remaining = $this->classeService->getRemainingCapacity($classe);

        $this->assertEquals(18, $remaining);
    }
}
