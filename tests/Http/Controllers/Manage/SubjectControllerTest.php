<?php

namespace Tests\Feature\Manage;

use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
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
    public function admin_can_view_subjects_list()
    {
        $subjects = Subject::factory()->count(3)->create();
        $users = User::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get('/manage/subject');

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Subject.index')
            ->assertViewHas(['subjects', 'users'])
            ->assertSee($subjects[0]->name);
    }

    /** @test */
    public function admin_can_view_subject_details()
    {
        $subject = Subject::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/manage/subject/{$subject->id}");

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Subject.show')
            ->assertViewHas('subject')
            ->assertSee($subject->name);
    }

    /** @test */
    public function admin_can_create_new_subject()
    {
        $subjectData = [
            'name' => 'Mathematics 101',
            'description' => 'Basic mathematics course'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/manage/subject', $subjectData);

        $response->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseHas('subjects', $subjectData);
    }

    /** @test */
    public function admin_cannot_create_subject_with_invalid_data()
    {
        $invalidData = [
            'name' => '', // Empty name should fail validation
            'description' => null
        ];

        $response = $this->actingAs($this->admin)
            ->post('/manage/subject', $invalidData);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function admin_can_update_existing_subject()
    {
        $subject = Subject::factory()->create();

        $updatedData = [
            'name' => 'Updated Course Name',
            'description' => 'Updated course description'
        ];

        $response = $this->actingAs($this->admin)
            ->put("/manage/subject/{$subject->id}", $updatedData);

        $response->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Updated Course Name',
            'description' => 'Updated course description'
        ]);
    }

    /** @test */
    public function admin_can_delete_subject()
    {
        $subject = Subject::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/manage/subject/{$subject->id}");

        $response->assertStatus(302)
            ->assertRedirect();

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id
        ]);
    }

    /** @test */
    public function admin_can_view_assign_students_page()
    {
        $subject = Subject::factory()->create();
        $existingStudents = Student::factory()->count(2)->create();
        $subject->students()->attach($existingStudents->pluck('id'));

        // Create some unassigned students
        $unassignedStudents = Student::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get("/manage/subject/{$subject->id}/assign");

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Subject.assign-student')
            ->assertViewHas(['students', 'subject']);

        // Verify only unassigned students are shown
        $responseStudents = $response->viewData('students');
        $this->assertEquals($unassignedStudents->count(), $responseStudents->count());
    }

    /** @test */
    public function admin_can_assign_students_to_subject()
    {
        $subject = Subject::factory()->create();
        $students = Student::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->post("/manage/subject/{$subject->id}/attach", [
                'students' => $students->pluck('id')->toArray()
            ]);

        $response->assertStatus(302)
            ->assertRedirect(route('subject.index'));

        // Verify students were attached
        foreach ($students as $student) {
            $this->assertDatabaseHas('subject_student', [
                'subject_id' => $subject->id,
                'student_id' => $student->id
            ]);
        }
    }

    /** @test */
    public function admin_can_remove_student_from_subject()
    {
        $subject = Subject::factory()->create();
        $student = Student::factory()->create();

        // First attach the student
        $subject->students()->attach($student->id);

        $response = $this->actingAs($this->admin)
            ->delete("/manage/subject/{$subject->id}/detach/{$student->id}");

        $response->assertStatus(302)
            ->assertRedirect();

        // Verify student was detached
        $this->assertDatabaseMissing('subject_student', [
            'subject_id' => $subject->id,
            'student_id' => $student->id
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_subject_management()
    {
        $nonAdmin = User::factory()->create(['role' => 'User']);

        $response = $this->actingAs($nonAdmin)
            ->get('/manage/subject');

        $response->assertStatus(403);
    }

    /** @test */
    public function subject_delete_fails_gracefully_when_having_attendances()
    {
        $subject = Subject::factory()
            ->withAttendances(1)
            ->create();

        $response = $this->actingAs($this->admin)
            ->delete("/manage/subject/{$subject->id}");

        $response->assertStatus(302)
            ->assertRedirect();

        // Subject should still exist due to relationship constraint
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id
        ]);
    }
}
