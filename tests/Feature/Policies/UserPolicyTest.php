<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $admin;
    private User $user;
    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
        $this->user = User::factory()->create(['role' => UserRole::User]);
        $this->targetUser = User::factory()->create(['role' => UserRole::User]);
    }

    // ViewAny tests
    public function test_super_admin_can_view_any_user(): void
    {
        $this->assertTrue($this->superAdmin->can('viewAny', User::class));
    }

    public function test_admin_can_view_any_user(): void
    {
        $this->assertTrue($this->admin->can('viewAny', User::class));
    }

    public function test_regular_user_cannot_view_any_user(): void
    {
        $this->assertFalse($this->user->can('viewAny', User::class));
    }

    public function test_guest_cannot_view_any_user(): void
    {
        $guest = new User();
        $this->assertFalse($guest->can('viewAny', User::class));
    }

    // Create tests
    public function test_super_admin_can_create_user(): void
    {
        $this->assertTrue($this->superAdmin->can('create', User::class));
    }

    public function test_admin_can_create_user(): void
    {
        $this->assertTrue($this->admin->can('create', User::class));
    }

    public function test_regular_user_cannot_create_user(): void
    {
        $this->assertFalse($this->user->can('create', User::class));
    }

    // Update tests
    public function test_super_admin_can_update_any_user(): void
    {
        $this->assertTrue($this->superAdmin->can('update', $this->targetUser));
    }

    public function test_admin_can_update_regular_user(): void
    {
        $this->assertTrue($this->admin->can('update', $this->targetUser));
    }

    public function test_admin_cannot_update_another_admin(): void
    {
        $this->assertFalse($this->admin->can('update', $this->admin));
    }

    public function test_admin_cannot_update_super_admin(): void
    {
        $this->assertFalse($this->admin->can('update', $this->superAdmin));
    }

    public function test_super_admin_can_update_admin(): void
    {
        $this->assertTrue($this->superAdmin->can('update', $this->admin));
    }

    public function test_regular_user_cannot_update_other_users(): void
    {
        $this->assertFalse($this->user->can('update', $this->targetUser));
    }

    // Delete tests
    public function test_super_admin_can_delete_user(): void
    {
        $this->assertTrue($this->superAdmin->can('delete', $this->targetUser));
    }

    public function test_admin_can_delete_regular_user(): void
    {
        $this->assertTrue($this->admin->can('delete', $this->targetUser));
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin2 = User::factory()->create(['role' => UserRole::Admin]);
        $this->assertFalse($this->admin->can('delete', $admin2));
    }

    public function test_admin_cannot_delete_super_admin(): void
    {
        $this->assertFalse($this->admin->can('delete', $this->superAdmin));
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $this->assertFalse($this->superAdmin->can('delete', $this->superAdmin));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $this->assertFalse($this->admin->can('delete', $this->admin));
    }

    public function test_user_cannot_delete_other_users(): void
    {
        $this->assertFalse($this->user->can('delete', $this->targetUser));
    }

    // UpdateRole tests
    public function test_only_super_admin_can_update_role(): void
    {
        $this->assertTrue($this->superAdmin->can('updateRole', $this->targetUser));
        $this->assertFalse($this->admin->can('updateRole', $this->targetUser));
        $this->assertFalse($this->user->can('updateRole', $this->targetUser));
    }

    public function test_super_admin_cannot_change_own_role(): void
    {
        $this->assertFalse($this->superAdmin->can('updateRole', $this->superAdmin));
    }

    public function test_super_admin_can_change_admin_role(): void
    {
        $this->assertTrue($this->superAdmin->can('updateRole', $this->admin));
    }

    // UpdateStatus tests
    public function test_super_admin_can_update_status(): void
    {
        $this->assertTrue($this->superAdmin->can('updateStatus', $this->targetUser));
    }

    public function test_admin_can_update_regular_user_status(): void
    {
        $this->assertTrue($this->admin->can('updateStatus', $this->targetUser));
    }

    public function test_admin_cannot_update_own_status(): void
    {
        $this->assertFalse($this->admin->can('updateStatus', $this->admin));
    }

    public function test_regular_user_cannot_update_status(): void
    {
        $this->assertFalse($this->user->can('updateStatus', $this->targetUser));
    }

    // UpdatePassword tests
    public function test_super_admin_can_update_own_password(): void
    {
        $this->assertTrue($this->superAdmin->can('updatePassword', $this->superAdmin));
    }

    public function test_super_admin_can_update_other_passwords(): void
    {
        $this->assertTrue($this->superAdmin->can('updatePassword', $this->targetUser));
    }

    public function test_admin_cannot_update_own_password(): void
    {
        // Admin cannot update their own password; they need SuperAdmin to do it
        $this->assertFalse($this->admin->can('updatePassword', $this->admin));
    }

    public function test_admin_can_update_regular_user_password(): void
    {
        $this->assertTrue($this->admin->can('updatePassword', $this->targetUser));
    }

    public function test_regular_user_cannot_update_password(): void
    {
        $this->assertFalse($this->user->can('updatePassword', $this->targetUser));
    }

    // Role hierarchy tests
    public function test_privilege_escalation_protection(): void
    {
        // Admin should not be able to update another admin to super admin
        $this->assertFalse($this->admin->can('updateRole', $this->admin));

        // Regular user should not be able to become admin
        $this->assertFalse($this->user->can('updateRole', $this->user));
    }

    public function test_access_control_is_checked(): void
    {
        // SuperAdmin has access to admin panel
        $this->assertTrue($this->superAdmin->canAccessAdmin());
        $this->assertTrue($this->admin->canAccessAdmin());
        $this->assertFalse($this->user->canAccessAdmin());
    }
}
