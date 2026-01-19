<?php

namespace Database\Factories;

use App\Models\ClientAccount;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['virtual', 'physical']);
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $endsAt = (clone $startsAt)->modify('+1 hour');

        return [
            'client_account_id' => ClientAccount::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'type' => $type,
            'meeting_link' => $type === 'virtual' ? Event::generateMeetingLink() : null,
            'location' => $type === 'physical' ? fake()->address() : null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    /**
     * Set the event as virtual.
     */
    public function virtual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'virtual',
            'meeting_link' => Event::generateMeetingLink(),
            'location' => null,
        ]);
    }

    /**
     * Set the event as physical.
     */
    public function physical(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'physical',
            'meeting_link' => null,
            'location' => fake()->address(),
        ]);
    }

    /**
     * Set the event as upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    /**
     * Set the event as past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
