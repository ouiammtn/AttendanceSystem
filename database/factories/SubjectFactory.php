<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word() . ' ' . $this->faker->randomElement(['I', 'II', 'III', 'IV']),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the subject has students
     */
    public function withStudents($count = 1)
    {
        return $this->afterCreating(function (Subject $subject) use ($count) {
            $students = \App\Models\Student::factory()->count($count)->create();
            $students->each(function ($student) use ($subject) {
                $subject->students()->attach($student->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            });
        });
    }

    /**
     * Indicate that the subject has attendances
     */
    public function withAttendances($count = 1)
    {
        return $this->afterCreating(function (Subject $subject) use ($count) {
            \App\Models\Attendance::factory()->count($count)->create([
                'subject_id' => $subject->id,
                'user_id' => \App\Models\User::factory()->create(['role' => 'Admin'])->id
            ]);
        });
    }
}
