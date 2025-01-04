<?php

namespace Tests\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_redirects_to_manage_dashboard()
    {
        $adminUser = User::factory()->create([
            'role' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/manage/dashboard');
    }

    public function test_regular_user_redirects_to_login()
    {
        $regularUser = User::factory()->create([
            'role' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/manage/dashboard');
        $response->assertRedirect('/');
    }

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_invalid_login_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'admin@ensam-casa.ma',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors();
        $response->assertRedirect('/');
    }
}
