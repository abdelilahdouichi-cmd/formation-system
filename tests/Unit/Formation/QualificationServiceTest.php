<?php

namespace Tests\Unit\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use App\Models\Formation\Qualification;
use App\Models\User;
use App\Services\Formation\ParticipantService;
use App\Services\Formation\QualificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected QualificationService $qualificationService;
    protected Formation $formation;
    protected Participant $participant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);

        $this->qualificationService = new QualificationService();
        $this->formation = Formation::factory()->create();
        $this->participant = Participant::factory()->create(['formation_id' => $this->formation->id]);
    }

    public function test_record_qualification_creates_qualification_with_pending_status(): void
    {
        $data = [
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'status' => 'en attente',
            'score' => 75,
            'date_evaluation' => now(),
        ];

        $qualification = $this->qualificationService->recordQualification($data);

        $this->assertDatabaseHas('qualifications', [
            'id' => $qualification->id,
            'participant_id' => $this->participant->id,
            'status' => 'en attente',
        ]);
    }

    public function test_approve_qualification_changes_status_to_qualified(): void
    {
        $qualification = Qualification::factory()->create([
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'status' => 'en attente',
        ]);

        $approved = $this->qualificationService->approveQualification($qualification);

        $this->assertDatabaseHas('qualifications', [
            'id' => $qualification->id,
            'status' => 'qualifié',
        ]);
    }

    public function test_reject_qualification_changes_status_to_rejected(): void
    {
        $qualification = Qualification::factory()->create([
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'status' => 'en attente',
        ]);

        $rejected = $this->qualificationService->rejectQualification($qualification, 'Score insuffisant');

        $this->assertDatabaseHas('qualifications', [
            'id' => $qualification->id,
            'status' => 'refusé',
        ]);
    }

    public function test_cannot_approve_already_qualified_qualification(): void
    {
        $qualification = Qualification::factory()->create([
            'status' => 'qualifié',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cette qualification est déjà approuvée.');

        $this->qualificationService->approveQualification($qualification);
    }

    public function test_get_qualification_rate(): void
    {
        // setUp() already created 1 participant — create 9 more to total 10
        Participant::factory()->count(9)->create(['formation_id' => $this->formation->id]);

        // Create 7 qualified participants
        $participants = $this->formation->participants;
        foreach ($participants->take(7) as $participant) {
            Qualification::factory()->create([
                'formation_id' => $this->formation->id,
                'participant_id' => $participant->id,
                'status' => 'qualifié',
            ]);
        }

        $rate = $this->qualificationService->getQualificationRate($this->formation->id);

        $this->assertEquals(70, round($rate, 0));
    }

    public function test_delete_qualification(): void
    {
        $qualification = Qualification::factory()->create();

        $this->qualificationService->deleteQualification($qualification);

        $this->assertSoftDeleted('qualifications', ['id' => $qualification->id]);
    }
}
