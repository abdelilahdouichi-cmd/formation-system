<?php

namespace Tests\Unit\Formation;

use App\Models\Formation\Formation;
use App\Models\User;
use App\Services\Formation\FormationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected FormationService $formationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);

        $this->formationService = new FormationService();
    }

    public function test_create_formation_creates_with_planning_status(): void
    {
        $data = Formation::factory()->raw([
            'status' => 'planifiée',
        ]);

        $formation = $this->formationService->createFormation($data);

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'planifiée',
        ]);
    }

    public function test_program_formation_transitions_status(): void
    {
        $formation = Formation::factory()->create(['status' => 'planifiée']);

        $programmed = $this->formationService->programFormation($formation);

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'à programmer',
        ]);
    }

    public function test_cannot_program_non_planned_formation(): void
    {
        $formation = Formation::factory()->create(['status' => 'exécutée']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seules les formations planifiées peuvent être programmées.');

        $this->formationService->programFormation($formation);
    }

    public function test_execute_formation_transitions_status(): void
    {
        $formation = Formation::factory()->create(['status' => 'à programmer']);

        $executed = $this->formationService->executeFormation($formation, [
            'date_debut_reel' => now(),
        ]);

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'exécutée',
        ]);
    }

    public function test_cannot_execute_non_programmed_formation(): void
    {
        $formation = Formation::factory()->create(['status' => 'planifiée']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seules les formations à programmer peuvent être exécutées.');

        $this->formationService->executeFormation($formation, []);
    }

    public function test_cancel_formation_transitions_status(): void
    {
        $formation = Formation::factory()->create(['status' => 'planifiée']);

        $cancelled = $this->formationService->cancelFormation($formation);

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'annulée',
        ]);
    }

    public function test_cannot_cancel_executed_formation(): void
    {
        $formation = Formation::factory()->create(['status' => 'exécutée']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Impossible d\'annuler une formation déjà exécutée ou archivée.');

        $this->formationService->cancelFormation($formation);
    }

    public function test_archive_formation_transitions_status(): void
    {
        $formation = Formation::factory()->create(['status' => 'exécutée']);

        $archived = $this->formationService->archiveFormation($formation);

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'status' => 'archivée',
        ]);
    }

    public function test_cannot_archive_non_executed_formation(): void
    {
        $formation = Formation::factory()->create(['status' => 'planifiée']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seules les formations exécutées peuvent être archivées.');

        $this->formationService->archiveFormation($formation);
    }

    public function test_update_formation(): void
    {
        $formation = Formation::factory()->create();

        $updateData = [
            'nom' => 'Formation Updated',
            'description' => 'Updated description',
        ];

        $updated = $this->formationService->updateFormation($formation, $updateData);

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'nom' => 'Formation Updated',
        ]);
    }

    public function test_delete_formation(): void
    {
        $formation = Formation::factory()->create();

        $this->formationService->deleteFormation($formation);

        $this->assertSoftDeleted('formations', ['id' => $formation->id]);
    }
}
