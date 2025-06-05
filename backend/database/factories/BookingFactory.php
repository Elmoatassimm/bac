<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']);

        return [
            'offer_id' => Offer::factory(),
            'client_id' => Client::factory(),
            'booking_date' => $this->getBookingDateForStatus($status),
            'status' => $status,
            'total_amount' => fake()->randomFloat(2, 50, 500),
        ];
    }

    /**
     * Get appropriate booking date based on status.
     */
    private function getBookingDateForStatus(string $status): \Carbon\Carbon
    {
        return match ($status) {
            'completed' => now()->subDays(rand(1, 60)), // Past dates for completed
            'cancelled' => rand(1, 100) <= 50 ? now()->subDays(rand(1, 30)) : now()->addDays(rand(1, 30)), // Mix of past and future
            'confirmed' => now()->addDays(rand(1, 45)), // Future dates for confirmed
            'pending' => now()->addDays(rand(1, 30)), // Near future for pending
            default => now()->addDays(rand(1, 30)),
        };
    }

    /**
     * Create a pending booking.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'booking_date' => now()->addDays(rand(1, 30)),
        ]);
    }

    /**
     * Create a confirmed booking.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'booking_date' => now()->addDays(rand(1, 45)),
        ]);
    }

    /**
     * Create a completed booking.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'booking_date' => now()->subDays(rand(1, 60)),
        ]);
    }

    /**
     * Create a cancelled booking.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'booking_date' => rand(1, 100) <= 50 ? now()->subDays(rand(1, 30)) : now()->addDays(rand(1, 30)),
        ]);
    }
}
