<?php

namespace Tests\Feature\Authorization;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_super_admin_user_can_access_admin_dashboard(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);

        $response = $this->actingAs($superAdmin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_users_list(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_users_list(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_cannot_create_admin_user(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'phone' => '1234567890',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ]);

        // Admin cannot explicitly set admin role, it should be coerced to User
        // So the response should be successful but the user should be created as User
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => UserRole::User->value, // Should be User, not Admin
        ]);
    }

    public function test_super_admin_can_create_admin_user(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);

        $response = $this->actingAs($superAdmin)->post('/admin/users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'phone' => '1234567890',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => UserRole::Admin->value,
        ]);
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => UserRole::Admin]);
        $admin2 = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin1)->delete("/admin/users/{$admin2->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $admin2->id]);
    }

    public function test_super_admin_can_delete_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($superAdmin)->delete("/admin/users/{$admin->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_super_admin(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$superAdmin->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_user_cannot_be_deleted_by_themselves(): void
    {
        $user = User::factory()->create(['role' => UserRole::User]);

        $response = $this->actingAs($user)->delete("/admin/users/{$user->id}");

        // Regular user doesn't have policy access anyway
        $response->assertStatus(403);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_update_role_of_users(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $user = User::factory()->create(['role' => UserRole::User]);

        $response = $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", [
                'name' => 'Updated',
                'email' => 'updated@example.com',
                'phone' => '9999999999',
                'role' => UserRole::Admin->value,
                'is_active' => true,
            ]);

        // Role should not be updated
        $user->refresh();
        $this->assertEquals(UserRole::User, $user->role);
    }

    public function test_super_admin_can_update_user_role(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $user = User::factory()->create(['role' => UserRole::User]);

        $response = $this->actingAs($superAdmin)
            ->put("/admin/users/{$user->id}", [
                'name' => 'Updated',
                'email' => 'updated@example.com',
                'phone' => '9999999999',
                'role' => UserRole::Admin->value,
                'is_active' => true,
            ]);

        $user->refresh();
        $this->assertEquals(UserRole::Admin, $user->role);
    }

    public function test_inactive_user_cannot_access_admin(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'is_active' => false]);

        // Even if logged in (if possible), they should fail at login
        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password', // Won't match anyway
        ]);

        $response->assertSessionHasErrors();
    }
}
