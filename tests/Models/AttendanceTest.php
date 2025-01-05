<?php

namespace Tests\Unit\Models;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Attendance $attendance;
    private User $teacher;
    private Subject $subject;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->teacher = User::factory()->create();
        $this->subject = Subject::factory()->create();
        $this->student = Student::factory()->create();

        // Create attendance record
        $this->attendance = Attendance::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'date' => Carbon::now(),
        ]);

        // Attach student with status (1 for present, 0 for absent)
        $this->attendance->students()->attach($this->student->id, ['status' => 1]);
    }

    /** @test */
    public function it_can_create_attendance()
    {
        $data = [
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'date' => Carbon::now(),
        ];

        $attendance = Attendance::create($data);

        $this->assertInstanceOf(Attendance::class, $attendance);
        $this->assertEquals($data['subject_id'], $attendance->subject_id);
        $this->assertEquals($data['user_id'], $attendance->user_id);
        $this->assertEquals($data['date']->format('Y-m-d'), $attendance->date->format('Y-m-d'));
    }

    /** @test */
    public function it_belongs_to_many_students()
    {
        $this->assertInstanceOf(BelongsToMany::class, $this->attendance->students());
        $this->assertTrue($this->attendance->students->contains($this->student));
        $this->assertEquals(1, $this->attendance->students->first()->pivot->status);
    }

    /** @test */
    public function it_belongs_to_a_subject()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->attendance->subject());
        $this->assertTrue($this->attendance->subject()->exists());
        $this->assertEquals($this->subject->id, $this->attendance->subject->id);
    }

    /** @test */
    public function it_belongs_to_a_teacher()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->attendance->teacher());
        $this->assertTrue($this->attendance->teacher()->exists());
        $this->assertEquals($this->teacher->id, $this->attendance->teacher->id);
    }

    /** @test */
    public function it_can_scope_by_subject()
    {
        // Create another attendance record with different subject
        $otherSubject = Subject::factory()->create();
        Attendance::create([
            'subject_id' => $otherSubject->id,
            'user_id' => $this->teacher->id,
            'date' => Carbon::now(),
        ]);

        $filteredAttendance = Attendance::whereSubject($this->subject->id)->get();

        $this->assertEquals(1, $filteredAttendance->count());
        $this->assertEquals($this->subject->id, $filteredAttendance->first()->subject_id);
    }

    /** @test */
    public function it_can_scope_by_date()
    {
        // Create another attendance record with different date
        $yesterday = Carbon::yesterday();
        Attendance::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'date' => $yesterday,
        ]);

        $filteredAttendance = Attendance::whereDateIs($yesterday->format('Y-m-d'))->get();

        $this->assertEquals(1, $filteredAttendance->count());
        $this->assertEquals($yesterday->format('Y-m-d'), $filteredAttendance->first()->date->format('Y-m-d'));
    }

    /** @test */
    public function it_handles_null_in_where_subject_scope()
    {
        $result = Attendance::whereSubject(null)->get();

        $this->assertNotNull($result);
        $this->assertTrue($result->contains($this->attendance));
    }

    /** @test */
    public function it_handles_null_in_where_date_is_scope()
    {
        $result = Attendance::whereDateIs(null)->get();

        $this->assertNotNull($result);
        $this->assertTrue($result->contains($this->attendance));
    }
}
