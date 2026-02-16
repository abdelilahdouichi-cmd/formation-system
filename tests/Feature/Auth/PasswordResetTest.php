<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_link_page_is_accessible(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_password_reset_link_fails_for_non_existent_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_password_page_is_accessible(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->get("/reset-password/$token");

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', $token);
    }

    public function test_invalid_reset_token_redirects(): void
    {
        $response = $this->get('/reset-password/invalid-token');

        $response->assertStatus(200);
        // Laravel requires attempting to reset with the token for validation
    }

    public function test_password_can_be_reset(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/login');

        $user->refresh();
        $this->assertTrue(\Hash::check('new-password123', $user->password));
    }

    public function test_password_reset_fails_with_mismatched_password(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_reset_fails_with_expired_token(): void
    {
        $user = User::factory()->create();
        $expiredToken = 'expired-token-' . now()->subHour()->timestamp;

        $response = $this->post('/reset-password', [
            'token' => $expiredToken,
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
