<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Offer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Check if we're in testing environment and should seed test data
        if (app()->environment('testing')) {
            $this->call(TestDataSeeder::class);
            return;
        }

        // Production/Development seeding
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create sample users (formerly clinics)
        $user1 = User::create([
            'name' => 'Downtown Medical Center',
            'email' => 'admin@downtownmedical.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Wellness Family Clinic',
            'email' => 'info@wellnessfamily.com',
            'password' => Hash::make('password'),
        ]);

        $user3 = User::create([
            'name' => 'Advanced Healthcare Solutions',
            'email' => 'contact@advancedhealthcare.com',
            'password' => Hash::make('password'),
        ]);

        // Create sample offers for user 1
        Offer::create([
            'user_id' => $user1->id,
            'title' => 'General Health Checkup',
            'description' => 'Comprehensive health examination including vital signs, blood pressure check, and general wellness assessment. Perfect for routine health monitoring.',
            'price' => 150.00,
        ]);

        Offer::create([
            'user_id' => $user1->id,
            'title' => 'Blood Work Analysis',
            'description' => 'Complete blood count, cholesterol levels, and metabolic panel analysis. Results available within 24 hours.',
            'price' => 85.00,
        ]);

        // Create sample offers for user 2
        Offer::create([
            'user_id' => $user2->id,
            'title' => 'Family Consultation',
            'description' => 'Comprehensive family health consultation including pediatric and adult care. Suitable for all family members.',
            'price' => 120.00,
        ]);

        Offer::create([
            'user_id' => $user2->id,
            'title' => 'Vaccination Services',
            'description' => 'Complete vaccination services for children and adults. Includes flu shots, travel vaccines, and routine immunizations.',
            'price' => 75.00,
        ]);

        // Create sample offers for user 3
        Offer::create([
            'user_id' => $user3->id,
            'title' => 'Specialist Consultation',
            'description' => 'Advanced medical consultation with board-certified specialists. Includes detailed diagnosis and treatment planning.',
            'price' => 250.00,
        ]);

        Offer::create([
            'user_id' => $user3->id,
            'title' => 'Diagnostic Imaging',
            'description' => 'State-of-the-art diagnostic imaging services including X-rays, ultrasounds, and MRI scans.',
            'price' => 300.00,
        ]);
    }
}
