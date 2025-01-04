<?php

namespace Tests\Feature\Manage;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $subject;
    protected $students;
    protected $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with correct enum value
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Admin'
        ]);

        // Create a subject
        $this->subject = Subject::factory()->create([
            'name' => 'Test Subject',
            'description' => 'Test Description'
        ]);

        // Create students
        $this->students = Student::factory()
            ->count(3)
            ->create()
            ->each(function ($student) {
                // Attach students to subject using the correct pivot table
                $this->subject->students()->attach($student->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            });

        // Create attendance record
        $this->attendance = Attendance::factory()->create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->admin->id,
            'date' => now()->format('Y-m-d')
        ]);
    }

    /** @test */
    public function only_admin_can_access_attendance_management()
    {
        $nonAdmin = User::factory()->create([
            'role' => 'User'  // Using correct enum value
        ]);

        $response = $this->actingAs($nonAdmin)
            ->get('/manage/attendance');

        $response->assertStatus(403);

        $response = $this->actingAs($this->admin)
            ->get('/manage/attendance');

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Attendance.index');
    }

    /** @test */
    public function admin_can_store_new_attendance()
    {
        $attendanceData = [
            'subject_id' => $this->subject->id,
            'date' => now()->format('Y-m-d')
        ];

        $response = $this->actingAs($this->admin)
            ->post('/manage/attendance', $attendanceData);

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Attendance.take-attendance');

        $this->assertDatabaseHas('attendances', [
            'subject_id' => $this->subject->id,
            'user_id' => $this->admin->id,
            'date' => now()->format('Y-m-d')
        ]);
    }

    /** @test */
    public function admin_can_edit_attendance()
    {
        $response = $this->actingAs($this->admin)
            ->get("/manage/attendance/{$this->attendance->id}/edit");

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Attendance.edit')
            ->assertViewHas('attendance');
    }

    /** @test */
    public function admin_can_attach_students_attendance_status()
    {
        $statusData = [
            'status' => [
                $this->students[0]->id => 'on',  // present
                $this->students[1]->id => 'off', // absent
                $this->students[2]->id => null   // not set
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post("/manage/attendance/attach/{$this->attendance->id}", $statusData);

        $response->assertRedirect();

        // Check the database for correct status values
        $this->assertDatabaseHas('attendance_student', [
            'attendance_id' => $this->attendance->id,
            'student_id' => $this->students[0]->id,
            'status' => 1
        ]);

        $this->assertDatabaseHas('attendance_student', [
            'attendance_id' => $this->attendance->id,
            'student_id' => $this->students[1]->id,
            'status' => 0
        ]);
    }

    /** @test */
    public function admin_can_update_attendance_data()
    {
        // First attach initial attendance status
        $this->attendance->students()->attach($this->students[0]->id, [
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update the attendance
        $updateData = [
            'status' => [
                $this->students[0]->id => 'off' // Change from present to absent
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->put("/manage/attendance/attach/{$this->attendance->id}/update", $updateData);

        $response->assertRedirect();

        // Verify the update
        $this->assertDatabaseHas('attendance_student', [
            'attendance_id' => $this->attendance->id,
            'student_id' => $this->students[0]->id,
            'status' => 0
        ]);
    }

    /** @test */
    public function admin_can_delete_attendance()
    {
        $response = $this->actingAs($this->admin)
            ->delete("/manage/attendance/{$this->attendance->id}");

        $response->assertRedirect();

        $this->assertDatabaseMissing('attendances', [
            'id' => $this->attendance->id
        ]);
    }

    /** @test */
    public function attendance_is_deleted_when_no_status_provided()
    {
        $response = $this->actingAs($this->admin)
            ->post("/manage/attendance/attach/{$this->attendance->id}", [
                'status' => null
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('attendances', [
            'id' => $this->attendance->id
        ]);
    }

    /** @test */
    public function admin_can_view_filtered_attendance()
    {
        $date = now()->format('Y-m-d');

        $response = $this->actingAs($this->admin)
            ->get("/manage/attendance?subject_filter={$this->subject->id}&date_filter={$date}");

        $response->assertStatus(200)
            ->assertViewIs('Manage.pages.Attendance.index')
            ->assertViewHas(['attendances', 'subjects']);
    }
}
