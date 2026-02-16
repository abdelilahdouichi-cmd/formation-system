<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::SuperAdmin,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(403);
    }
}
