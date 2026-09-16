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

    /**
     * Security: Login kebal terhadap SQL Injection payload klasik (Auth Bypass).
     */
    public function test_login_is_resilient_against_classic_sql_injection_payloads(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@jara.app',
            'password' => 'secret123',
        ]);

        $payloads = [
            "' OR '1'='1",
            "' OR 1=1 --",
            "admin' --",
            "admin' #",
            "' OR ''='",
            "admin'/*",
        ];

        foreach ($payloads as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'anypassword',
            ]);

            $this->assertGuest();
            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
        }
    }

    /**
     * Security: Login kebal terhadap SQL Injection berbentuk sintaks email valid.
     */
    public function test_login_is_resilient_against_email_formatted_sql_injection(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@jara.app',
            'password' => 'secret123',
        ]);

        $emailPayloads = [
            "'or'1'='1'@jara.app",
            "admin'--@jara.app",
            "admin'/*@jara.app",
        ];

        foreach ($emailPayloads as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'wrongpass',
            ]);

            $this->assertGuest();
            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
        }
    }

    /**
     * Security: Login kebal terhadap SQL Injection pada kolom password.
     */
    public function test_login_is_resilient_against_password_field_sql_injection(): void
    {
        $user = User::factory()->create([
            'email' => 'user@jara.app',
            'password' => 'realpassword',
        ]);

        $passwordPayloads = [
            "' OR '1'='1",
            "' OR 1=1 --",
            "'; DROP TABLE users; --",
        ];

        foreach ($passwordPayloads as $payload) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => $payload,
            ]);

            $this->assertGuest();
            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
        }

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Security: Login kebal terhadap SQL Injection bertingkat (Stacked Queries / Drop Table).
     */
    public function test_login_is_resilient_against_destructive_stacked_queries(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@jara.app',
        ]);

        $response = $this->post('/login', [
            'email' => "admin@jara.app'; DROP TABLE users; --",
            'password' => 'dummy',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
