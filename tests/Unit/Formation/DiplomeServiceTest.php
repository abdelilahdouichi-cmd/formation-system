<?php

namespace Tests\Unit\Formation;

use App\Models\Formation\Diplome;
use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use App\Models\User;
use App\Services\Formation\DiplomeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiplomeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DiplomeService $diplomeService;
    protected Formation $formation;
    protected Participant $participant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);

        $this->diplomeService = new DiplomeService();
        $this->formation = Formation::factory()->create();
        $this->participant = Participant::factory()->create(['formation_id' => $this->formation->id]);
    }

    public function test_create_diplome_creates_with_pending_status(): void
    {
        $data = [
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'numero_diplome' => 'DIPL-001',
            'date_emission' => now(),
        ];

        $diplome = $this->diplomeService->createDiplome($data);

        $this->assertDatabaseHas('diplomes', [
            'id' => $diplome->id,
            'numero_diplome' => 'DIPL-001',
            'status' => 'à livrer',
        ]);
    }

    public function test_deliver_diplome_changes_status_to_delivered(): void
    {
        $diplome = Diplome::factory()->create([
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'status' => 'à livrer',
        ]);

        $delivered = $this->diplomeService->deliverDiplome($diplome);

        $this->assertDatabaseHas('diplomes', [
            'id' => $diplome->id,
            'status' => 'livrée',
        ]);
        $this->assertNotNull($delivered->date_livraison);
    }

    public function test_reject_diplome_changes_status_to_rejected(): void
    {
        $diplome = Diplome::factory()->create([
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'status' => 'à livrer',
        ]);

        $rejected = $this->diplomeService->rejectDiplome($diplome, 'Documents manquants');

        $this->assertDatabaseHas('diplomes', [
            'id' => $diplome->id,
            'status' => 'refusée',
        ]);
    }

    public function test_cannot_deliver_already_delivered_diplome(): void
    {
        $diplome = Diplome::factory()->create([
            'status' => 'livrée',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ce diplôme a déjà été livré.');

        $this->diplomeService->deliverDiplome($diplome);
    }

    public function test_cannot_deliver_rejected_diplome(): void
    {
        $diplome = Diplome::factory()->create([
            'status' => 'refusée',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ce diplôme a été refusé et ne peut pas être livré.');

        $this->diplomeService->deliverDiplome($diplome);
    }

    public function test_cancel_diplome_changes_status_to_cancelled(): void
    {
        $diplome = Diplome::factory()->create([
            'status' => 'à livrer',
        ]);

        $cancelled = $this->diplomeService->cancelDiplome($diplome);

        $this->assertDatabaseHas('diplomes', [
            'id' => $diplome->id,
            'status' => 'annulée',
        ]);
    }

    public function test_get_diplomation_rate(): void
    {
        // setUp() already created 1 participant — create 9 more to total 10
        Participant::factory()->count(9)->create(['formation_id' => $this->formation->id]);

        // Create 8 delivered diplomes
        $participants = $this->formation->participants;
        foreach ($participants->take(8) as $participant) {
            Diplome::factory()->create([
                'formation_id' => $this->formation->id,
                'participant_id' => $participant->id,
                'status' => 'livrée',
            ]);
        }

        $rate = $this->diplomeService->getDiplomationRate($this->formation->id);

        $this->assertEquals(80, round($rate, 0));
    }
}
