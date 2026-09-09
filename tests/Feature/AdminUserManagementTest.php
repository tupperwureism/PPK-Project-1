<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_user_list(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_users_cannot_access_admin_user_list(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_user_list_and_search(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['name' => 'Budi Sudarsono', 'email' => 'budi@example.com']);
        $otherUser = User::factory()->create(['name' => 'Siti Nurhaliza', 'email' => 'siti@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'Budi']));

        $response->assertStatus(200);
        $response->assertSee('Budi Sudarsono');
        $response->assertDontSee('Siti Nurhaliza');
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'mhs@jara.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'user',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Mahasiswa Baru',
            'email' => 'mhs@jara.test',
            'role' => 'user',
        ]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $victim = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $victim));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $victim->id]);
    }
}
