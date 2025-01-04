<?php

namespace Tests\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_route_is_disabled()
    {
        $response = $this->get('/register');
        $response->assertStatus(404);
    }

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->actingAs($admin)
            ->get('/manage/dashboard');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'User',
            'email' => 'user@test.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->actingAs($user)
            ->get('/manage/dashboard');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/manage/dashboard');
        $response->assertRedirect('/');
    }

    public function test_homepage_redirects_to_login()
    {
        $response = $this->get('/');
        $response->assertViewIs('auth.login');
    }

    public function test_home_route_returns_404()
    {
        $response = $this->get('/home');
        $response->assertStatus(404);
    }
}
