<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/verify-email');
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_email_verification_prompt_is_shown_to_unverified_users(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    public function test_verified_user_is_redirected_from_verification_prompt(): void
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertRedirect('/dashboard');
    }

    public function test_verification_email_can_be_resent(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertSessionHasNoErrors();
    }

    public function test_valid_verification_hash_is_accepted(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'test@example.com']);

        $hash = sha1($user->getEmailForVerification());

        $response = $this->actingAs($user)->get(
            route('verification.verify', [
                'id' => $user->id,
                'hash' => $hash,
            ])
        );

        $response->assertRedirect('/dashboard');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_invalid_verification_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(
            route('verification.verify', [
                'id' => $user->id,
                'hash' => 'invalid-hash',
            ])
        );

        $response->assertStatus(403);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_can_only_be_done_once(): void
    {
        $user = User::factory()->unverified()->create();
        $user->markEmailAsVerified();

        $hash = sha1($user->getEmailForVerification());

        $response = $this->actingAs($user)->get(
            route('verification.verify', [
                'id' => $user->id,
                'hash' => $hash,
            ])
        );

        $response->assertRedirect('/dashboard');
    }
}
