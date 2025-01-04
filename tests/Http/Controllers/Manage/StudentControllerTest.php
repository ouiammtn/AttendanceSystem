<?php

namespace Tests\Feature\Manage;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Admin'
        ]);
    }

    /** @test */
    public function admin_can_view_students_list()
    {
        $students = Student::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get('/manage/student');

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Students.index')
            ->assertViewHas('students')
            ->assertSee($students[0]->name);
    }

    /** @test */
    public function admin_can_view_student_details()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/manage/student/{$student->id}");

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Students.show')
            ->assertViewHas('student')
            ->assertSee($student->name);
    }

    /** @test */
    public function admin_can_create_new_student()
    {
        $studentData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/manage/student', $studentData);

        $response->assertStatus(302) // Redirect response
        ->assertRedirect();

        $this->assertDatabaseHas('students', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ]);
    }

    /** @test */
    public function admin_cannot_create_student_with_invalid_data()
    {
        $invalidData = [
            'name' => '', // Empty name should fail validation
            'email' => 'not-an-email',
            'phone' => '123' // Too short
        ];

        $response = $this->actingAs($this->admin)
            ->post('/manage/student', $invalidData);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function admin_can_update_existing_student()
    {
        $student = Student::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210'
        ];

        $response = $this->actingAs($this->admin)
            ->put("/manage/student/{$student->id}", $updatedData);

        $response->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210'
        ]);
    }

    /** @test */
    public function admin_cannot_update_student_with_invalid_data()
    {
        $student = Student::factory()->create();

        $invalidData = [
            'name' => '', // Empty name should fail validation
            'email' => 'not-an-email',
            'phone' => '123' // Too short
        ];

        $response = $this->actingAs($this->admin)
            ->put("/manage/student/{$student->id}", $invalidData);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function admin_can_delete_student()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/manage/student/{$student->id}");

        $response->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseMissing('students', [
            'id' => $student->id
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_student_management()
    {
        $nonAdmin = User::factory()->create([
            'role' => 'User'
        ]);

        $response = $this->actingAs($nonAdmin)
            ->get('/manage/student');

        $response->assertStatus(403);
    }

    /** @test */
    public function student_delete_fails_gracefully_when_having_relationships()
    {
        $student = Student::factory()
            ->withSubject() // This creates a subject and attaches it to the student
            ->create();

        $response = $this->actingAs($this->admin)
            ->delete("/manage/student/{$student->id}");

        $response->assertStatus(302)
            ->assertRedirect();

        // Student should still exist due to relationship constraint
        $this->assertDatabaseHas('students', [
            'id' => $student->id
        ]);
    }

    /** @test */
    public function can_view_student_attendance_history()
    {
        $student = Student::factory()->create();

        // Create attendance records for the student
        $attendance = \App\Models\Attendance::factory()
            ->create()
            ->students()
            ->attach($student->id, [
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

        $response = $this->actingAs($this->admin)
            ->get("/manage/student/{$student->id}");

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Students.show')
            ->assertViewHas('student');

        // Verify the attendance relationship is loaded
        $this->assertTrue($response->original->getData()['student']->relationLoaded('attendances'));
    }
}
