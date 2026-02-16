<?php

namespace Tests\Unit\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\Licence;
use App\Models\Formation\Participant;
use App\Models\User;
use App\Services\Formation\LicenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected LicenceService $licenceService;
    protected Formation $formation;
    protected Participant $participant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);

        $this->licenceService = new LicenceService();
        $this->formation = Formation::factory()->create();
        $this->participant = Participant::factory()->create(['formation_id' => $this->formation->id]);
    }

    public function test_create_licence_creates_with_pending_status(): void
    {
        $data = [
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'numero_licence' => 'LIC-001',
            'date_emission' => now(),
            'date_expiration' => now()->addYear(),
        ];

        $licence = $this->licenceService->createLicence($data);

        $this->assertDatabaseHas('licences', [
            'id' => $licence->id,
            'numero_licence' => 'LIC-001',
            'status' => 'à livrer',
        ]);
    }

    public function test_deliver_licence_changes_status_to_delivered(): void
    {
        $licence = Licence::factory()->create([
            'formation_id' => $this->formation->id,
            'participant_id' => $this->participant->id,
            'status' => 'à livrer',
        ]);

        $delivered = $this->licenceService->deliverLicence($licence);

        $this->assertDatabaseHas('licences', [
            'id' => $licence->id,
            'status' => 'livrée',
        ]);
        $this->assertNotNull($delivered->date_livraison);
    }

    public function test_renew_licence_changes_status_to_renewed(): void
    {
        $licence = Licence::factory()->create([
            'status' => 'livrée',
        ]);

        $newExpirationDate = now()->addYear();
        $renewed = $this->licenceService->renewLicence($licence, [
            'date_expiration' => $newExpirationDate,
        ]);

        $this->assertDatabaseHas('licences', [
            'id' => $licence->id,
            'status' => 'renouvelée',
        ]);
    }

    public function test_suspend_licence_changes_status_to_suspended(): void
    {
        $licence = Licence::factory()->create([
            'status' => 'livrée',
        ]);

        $suspended = $this->licenceService->suspendLicence($licence, 'Raison de suspension');

        $this->assertDatabaseHas('licences', [
            'id' => $licence->id,
            'status' => 'suspendue',
        ]);
    }

    public function test_cannot_suspend_already_suspended_licence(): void
    {
        $licence = Licence::factory()->create([
            'status' => 'suspendue',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cette licence est déjà suspendue.');

        $this->licenceService->suspendLicence($licence);
    }

    public function test_check_expired_licences(): void
    {
        $expiredLicence = Licence::factory()->create([
            'status' => 'livrée',
            'date_expiration' => now()->subDay(),
        ]);

        $activeLicence = Licence::factory()->create([
            'status' => 'livrée',
            'date_expiration' => now()->addYear(),
        ]);

        $this->licenceService->checkExpiredLicences();

        $this->assertDatabaseHas('licences', [
            'id' => $expiredLicence->id,
            'status' => 'expirée',
        ]);

        $this->assertDatabaseHas('licences', [
            'id' => $activeLicence->id,
            'status' => 'livrée',
        ]);
    }

    public function test_get_licensed_rate(): void
    {
        // setUp() already created 1 participant — create 9 more to total 10
        Participant::factory()->count(9)->create(['formation_id' => $this->formation->id]);

        // Create 6 licensed participants
        $participants = $this->formation->participants;
        foreach ($participants->take(6) as $participant) {
            Licence::factory()->create([
                'formation_id' => $this->formation->id,
                'participant_id' => $participant->id,
                'status' => 'livrée',
            ]);
        }

        $rate = $this->licenceService->getLicencedRate($this->formation->id);

        $this->assertEquals(60, round($rate, 0));
    }
}
