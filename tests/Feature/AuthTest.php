<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Aplikasi');
    }

    public function test_regular_user_is_redirected_to_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_is_redirected_to_admin_user_management_after_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'adminpass123',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'adminpass123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => 'valid-password',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
