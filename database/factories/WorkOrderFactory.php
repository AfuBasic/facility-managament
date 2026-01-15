<?php

namespace Database\Factories;

use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientAccount = ClientAccount::factory()->create();

        return [
            'client_account_id' => $clientAccount->id,
            'facility_id' => Facility::factory()->create(['client_account_id' => $clientAccount->id])->id,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => 'reported',
            'reported_by' => User::factory(),
            'reported_at' => now(),
        ];
    }

    /**
     * Use a specific client account.
     */
    public function forClient(ClientAccount $clientAccount): static
    {
        return $this->state(fn (array $attributes) => [
            'client_account_id' => $clientAccount->id,
        ]);
    }

    /**
     * Create as approved state.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Create as assigned state.
     */
    public function assigned(): static
    {
        return $this->approved()->state(fn (array $attributes) => [
            'status' => 'assigned',
            'assigned_to' => User::factory(),
            'assigned_by' => User::factory(),
            'assigned_at' => now(),
        ]);
    }

    /**
     * Create as in progress state.
     */
    public function inProgress(): static
    {
        return $this->assigned()->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'started_by' => $attributes['assigned_to'] ?? User::factory(),
            'started_at' => now(),
        ]);
    }
}
