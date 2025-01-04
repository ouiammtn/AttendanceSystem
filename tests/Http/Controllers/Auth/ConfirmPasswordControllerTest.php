<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);
    }

    /** @test */
    public function show_password_confirmation_screen()
    {
        $response = $this->actingAs($this->user)
            ->get('/password/confirm');

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_view_password_confirmation_screen()
    {
        $response = $this->get('/password/confirm');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function password_can_be_confirmed()
    {
        $response = $this->actingAs($this->user)
            ->post('/password/confirm', [
                'password' => 'password123'
            ]);

        $response->assertStatus(302); // Redirect after successful confirmation
    }

    /** @test */
    public function password_is_not_confirmed_with_invalid_password()
    {
        $response = $this->actingAs($this->user)
            ->post('/password/confirm', [
                'password' => 'wrong-password'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
    }
}
