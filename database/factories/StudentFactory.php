<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('##########'), // 10 digits
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the student belongs to a subject
     */
    public function withSubject($subject = null)
    {
        return $this->afterCreating(function (Student $student) use ($subject) {
            $subject = $subject ?? \App\Models\Subject::factory()->create();
            $student->subjects()->attach($subject->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });
    }
}
