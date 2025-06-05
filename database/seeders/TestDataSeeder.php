<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Offer;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the database with comprehensive test data.
     */
    public function run(): void
    {
        // Create test users with realistic medical center profiles
        $users = $this->createTestUsers();
        
        // Create diverse offers for each user
        $offers = $this->createTestOffers($users);
        
        // Create test clients
        $clients = $this->createTestClients();
        
        // Create bookings with various statuses
        $bookings = $this->createTestBookings($offers, $clients);
        
        // Create payments for bookings with different statuses
        $this->createTestPayments($bookings);
    }

    /**
     * Create test users representing medical centers and clinics.
     */
    private function createTestUsers(): \Illuminate\Support\Collection
    {
        $userProfiles = [
            [
                'name' => 'Metropolitan Medical Center',
                'email' => 'admin@metropolitanmedical.com',
            ],
            [
                'name' => 'Sunrise Family Clinic',
                'email' => 'info@sunrisefamily.com',
            ],
            [
                'name' => 'Advanced Diagnostic Center',
                'email' => 'contact@advanceddiagnostic.com',
            ],
            [
                'name' => 'Wellness Plus Healthcare',
                'email' => 'hello@wellnessplus.com',
            ],
            [
                'name' => 'City General Hospital',
                'email' => 'appointments@citygeneral.com',
            ],
            [
                'name' => 'Pediatric Care Specialists',
                'email' => 'care@pediatricspecialists.com',
            ],
            [
                'name' => 'Heart & Vascular Institute',
                'email' => 'info@heartvascular.com',
            ],
            [
                'name' => 'Women\'s Health Center',
                'email' => 'appointments@womenshealth.com',
            ],
            [
                'name' => 'Orthopedic Sports Medicine',
                'email' => 'contact@orthosports.com',
            ],
            [
                'name' => 'Mental Health & Wellness',
                'email' => 'support@mentalwellness.com',
            ],
        ];

        $users = collect();
        
        foreach ($userProfiles as $profile) {
            $users->push(User::create([
                'name' => $profile['name'],
                'email' => $profile['email'],
                'password' => Hash::make('password'),
            ]));
        }

        return $users;
    }

    /**
     * Create diverse test offers for each user.
     */
    private function createTestOffers($users): \Illuminate\Support\Collection
    {
        $offerTemplates = [
            // General Medical Services
            [
                'title' => 'Annual Physical Examination',
                'description' => 'Comprehensive annual health checkup including vital signs, blood work, and preventive care screening.',
                'price_range' => [120, 180],
            ],
            [
                'title' => 'Urgent Care Consultation',
                'description' => 'Same-day medical consultation for non-emergency health concerns and minor injuries.',
                'price_range' => [80, 120],
            ],
            [
                'title' => 'Blood Pressure Monitoring',
                'description' => 'Professional blood pressure check and cardiovascular health assessment.',
                'price_range' => [40, 60],
            ],
            [
                'title' => 'Diabetes Management',
                'description' => 'Comprehensive diabetes care including glucose monitoring and lifestyle counseling.',
                'price_range' => [150, 200],
            ],
            
            // Specialized Services
            [
                'title' => 'Cardiology Consultation',
                'description' => 'Expert cardiac evaluation with ECG and heart health assessment.',
                'price_range' => [250, 350],
            ],
            [
                'title' => 'Dermatology Screening',
                'description' => 'Skin cancer screening and dermatological health evaluation.',
                'price_range' => [180, 250],
            ],
            [
                'title' => 'Mental Health Counseling',
                'description' => 'Professional mental health support and therapy sessions.',
                'price_range' => [100, 150],
            ],
            
            // Diagnostic Services
            [
                'title' => 'Laboratory Blood Work',
                'description' => 'Complete blood count, metabolic panel, and lipid profile analysis.',
                'price_range' => [75, 120],
            ],
            [
                'title' => 'X-Ray Imaging',
                'description' => 'Digital X-ray imaging for bone and joint evaluation.',
                'price_range' => [90, 140],
            ],
            [
                'title' => 'Ultrasound Examination',
                'description' => 'Non-invasive ultrasound imaging for various medical conditions.',
                'price_range' => [150, 220],
            ],
            
            // Preventive Care
            [
                'title' => 'Vaccination Services',
                'description' => 'Immunizations including flu shots, travel vaccines, and routine boosters.',
                'price_range' => [50, 100],
            ],
            [
                'title' => 'Health Screening Package',
                'description' => 'Comprehensive health screening including multiple diagnostic tests.',
                'price_range' => [200, 300],
            ],
        ];

        $offers = collect();
        
        foreach ($users as $user) {
            // Each user gets 3-6 random offers
            $userOffers = collect($offerTemplates)->random(rand(3, 6));
            
            foreach ($userOffers as $template) {
                $offers->push(Offer::create([
                    'user_id' => $user->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'price' => rand($template['price_range'][0], $template['price_range'][1]),
                ]));
            }
        }

        return $offers;
    }

    /**
     * Create test clients with realistic contact information.
     */
    private function createTestClients(): \Illuminate\Support\Collection
    {
        $clients = collect();
        
        // Create 25 test clients with varied profiles
        for ($i = 1; $i <= 25; $i++) {
            $clients->push(Client::factory()->create());
        }

        return $clients;
    }

    /**
     * Create test bookings with various statuses and dates.
     */
    private function createTestBookings($offers, $clients): \Illuminate\Support\Collection
    {
        $bookings = collect();
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        
        // Create 50 bookings with varied scenarios
        for ($i = 1; $i <= 50; $i++) {
            $offer = $offers->random();
            $client = $clients->random();
            $status = collect($statuses)->random();
            
            // Create booking dates based on status
            $bookingDate = $this->getBookingDateForStatus($status);
            
            $bookings->push(Booking::create([
                'offer_id' => $offer->id,
                'client_id' => $client->id,
                'booking_date' => $bookingDate,
                'status' => $status,
                'total_amount' => $offer->price,
            ]));
        }

        return $bookings;
    }

    /**
     * Create test payments for bookings with realistic scenarios.
     */
    private function createTestPayments($bookings): void
    {
        foreach ($bookings as $booking) {
            // Only create payments for confirmed or completed bookings (80% chance)
            // and some pending bookings (30% chance)
            $shouldCreatePayment = match ($booking->status) {
                'confirmed', 'completed' => rand(1, 100) <= 80,
                'pending' => rand(1, 100) <= 30,
                'cancelled' => rand(1, 100) <= 10, // Some cancelled bookings might have failed payments
                default => false,
            };

            if ($shouldCreatePayment) {
                $paymentStatus = $this->getPaymentStatusForBooking($booking);
                
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'payment_intent_id' => 'pi_test_' . rand(100000, 999999),
                    'amount' => $booking->total_amount,
                    'status' => $paymentStatus,
                    'transaction_id' => $paymentStatus === 'completed' ? 'txn_' . rand(100000, 999999) : null,
                    'paid_at' => $paymentStatus === 'completed' ? now()->subDays(rand(1, 30)) : null,
                    'failed_at' => $paymentStatus === 'failed' ? now()->subDays(rand(1, 15)) : null,
                ]);
            }
        }
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
     * Get appropriate payment status based on booking status.
     */
    private function getPaymentStatusForBooking(Booking $booking): string
    {
        return match ($booking->status) {
            'completed' => 'completed',
            'confirmed' => collect(['completed', 'pending'])->random(),
            'pending' => collect(['pending', 'processing'])->random(),
            'cancelled' => collect(['failed', 'refunded'])->random(),
            default => 'pending',
        };
    }
}
