<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $programs = ['life_coaching', 'career_coaching', 'relationship_coaching', 'wellness_coaching', null];
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

        return [
            'user_id' => User::factory(),
            'program' => $this->faker->randomElement($programs),
            'appointment_date' => $this->faker->dateTimeBetween('-30 days', '+30 days'),
            'appointment_time' => $this->faker->randomElement($times),
            'message' => $this->faker->optional(0.7)->sentence(),
            'status' => $this->faker->randomElement($statuses),
        ];
    }

    /**
     * Indicate that the appointment is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'appointment_date' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'status' => $this->faker->randomElement(['pending', 'confirmed']),
        ]);
    }

    /**
     * Indicate that the appointment is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'appointment_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the appointment is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the appointment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the appointment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
