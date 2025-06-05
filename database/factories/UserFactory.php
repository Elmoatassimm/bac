<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->generateMedicalCenterName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Generate realistic medical center names.
     */
    private function generateMedicalCenterName(): string
    {
        $prefixes = [
            'City', 'Metro', 'Regional', 'Community', 'Family', 'Advanced',
            'Premier', 'Comprehensive', 'Integrated', 'Modern', 'Central',
            'Downtown', 'Uptown', 'Riverside', 'Hillside', 'Parkview'
        ];

        $types = [
            'Medical Center', 'Healthcare', 'Clinic', 'Hospital', 'Medical Group',
            'Health Center', 'Wellness Center', 'Care Center', 'Medical Associates',
            'Health Services', 'Medical Institute', 'Healthcare Solutions'
        ];

        $specialties = [
            'Family Medicine', 'Internal Medicine', 'Pediatric Care', 'Women\'s Health',
            'Cardiology', 'Orthopedics', 'Dermatology', 'Mental Health', 'Urgent Care',
            'Diagnostic', 'Preventive Care', 'Sports Medicine'
        ];

        // 70% chance for general name, 30% chance for specialty name
        if (rand(1, 100) <= 70) {
            return fake()->randomElement($prefixes) . ' ' . fake()->randomElement($types);
        } else {
            return fake()->randomElement($prefixes) . ' ' . fake()->randomElement($specialties) . ' ' . fake()->randomElement(['Center', 'Clinic', 'Associates']);
        }
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
