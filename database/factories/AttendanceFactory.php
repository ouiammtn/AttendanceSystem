<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject_id' => Subject::factory(),
            'user_id' => User::factory()->create(['role' => 'Admin'])->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the attendance has students with status
     */
    public function withStudents($count = null)
    {
        return $this->afterCreating(function (Attendance $attendance) use ($count) {
            $subject = $attendance->subject;

            // If no students exist for the subject, create them
            if ($subject->students()->count() === 0) {
                \App\Models\Student::factory()
                    ->count($count ?? 3)
                    ->create()
                    ->each(function ($student) use ($subject) {
                        $subject->students()->attach($student->id, [
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    });
            }

            // Attach all subject's students to attendance with random status
            $subject->students->each(function ($student) use ($attendance) {
                $attendance->students()->attach($student->id, [
                    'status' => $this->faker->randomElement([0, 1, null]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            });
        });
    }

    /**
     * Set specific date for attendance
     */
    public function forDate($date)
    {
        return $this->state(function () use ($date) {
            return [
                'date' => Carbon::parse($date)->format('Y-m-d')
            ];
        });
    }

    /**
     * Set specific subject for attendance
     */
    public function forSubject(Subject $subject)
    {
        return $this->state(function () use ($subject) {
            return [
                'subject_id' => $subject->id
            ];
        });
    }
}
