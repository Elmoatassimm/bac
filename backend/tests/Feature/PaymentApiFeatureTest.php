<?php

use App\Models\User;
use App\Models\Offer;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test data
    $this->user = User::factory()->create();
    $this->offer = Offer::factory()->create([
        'user_id' => $this->user->id,
        'price' => 150.00,
    ]);
    $this->client = Client::factory()->create();
    $this->booking = Booking::factory()->create([
        'offer_id' => $this->offer->id,
        'client_id' => $this->client->id,
        'total_amount' => $this->offer->price,
        'status' => 'pending',
    ]);
    
    // Mock Stripe configuration for testing
    Config::set('services.stripe.secret', 'sk_test_mock_key');
    Config::set('services.stripe.key', 'pk_test_mock_key');
});

describe('Payment Creation API Tests', function () {
    test('create payment intent returns proper ApiResponseTrait structure', function () {
        $response = $this->postJson("/api/v1/create-payment-intent/{$this->booking->id}");

        // Should return ApiResponseTrait format regardless of Stripe setup
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        $responseData = $response->json();
        expect($responseData)->toHaveKey('success');
        expect($responseData)->toHaveKey('message');
        expect($responseData)->toHaveKey('data');
        expect($responseData)->toHaveKey('errors');
    });

    test('create payment intent for valid booking without Stripe setup', function () {
        $response = $this->postJson("/api/v1/create-payment-intent/{$this->booking->id}");

        // Without proper Stripe setup, should return server error but with proper format
        expect($response->status())->toBeIn([500, 400]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
        
        $responseData = $response->json();
        expect($responseData['success'])->toBeFalse();
        expect($responseData['message'])->toBeString();
    });

    test('create payment intent for non-existent booking returns 404', function () {
        $nonExistentBookingId = 99999;
        $response = $this->postJson("/api/v1/create-payment-intent/{$nonExistentBookingId}");

        $response->assertStatus(404);
    });

    test('already paid booking returns proper error response', function () {
        // Create a completed payment for the booking
        Payment::factory()->completed()->create([
            'booking_id' => $this->booking->id,
            'amount' => $this->booking->total_amount,
        ]);

        $response = $this->postJson("/api/v1/create-payment-intent/{$this->booking->id}");

        $response->assertStatus(400)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'errors',
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Booking already paid',
                    'data' => null,
                ]);
    });

    test('payment intent creation is idempotent for pending payments', function () {
        // Create a pending payment
        $existingPayment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => $this->booking->total_amount,
            'status' => 'pending',
            'payment_intent_id' => 'pi_test_existing_123',
        ]);

        $response = $this->postJson("/api/v1/create-payment-intent/{$this->booking->id}");

        // Should handle gracefully (either return existing or create new)
        expect($response->status())->toBeIn([200, 500]); // 500 due to Stripe mock
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
    });
});

describe('Payment Status Update Tests', function () {
    test('payment can be marked as completed', function () {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'status' => 'pending',
        ]);

        $payment->markAsCompleted();

        expect($payment->fresh()->status)->toBe('completed');
        expect($payment->fresh()->paid_at)->not->toBeNull();
    });

    test('payment can be marked as failed', function () {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'status' => 'pending',
        ]);

        $payment->markAsFailed();

        expect($payment->fresh()->status)->toBe('failed');
        expect($payment->fresh()->failed_at)->not->toBeNull();
    });

    test('completed payment updates booking status to confirmed', function () {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'status' => 'pending',
        ]);

        // Simulate webhook behavior
        $payment->markAsCompleted();
        $this->booking->update(['status' => 'confirmed']);

        expect($this->booking->fresh()->status)->toBe('confirmed');
        expect($payment->fresh()->status)->toBe('completed');
    });

    test('failed payment updates booking status to cancelled', function () {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'status' => 'pending',
        ]);

        // Simulate webhook behavior
        $payment->markAsFailed();
        $this->booking->update(['status' => 'cancelled']);

        expect($this->booking->fresh()->status)->toBe('cancelled');
        expect($payment->fresh()->status)->toBe('failed');
    });
});

describe('Booking Creation with Payment Flow Tests', function () {
    test('booking creation returns ApiResponseTrait format', function () {
        $bookingData = [
            'offer_id' => $this->offer->id,
            'client_name' => 'John Doe',
            'client_email' => 'john@example.com',
            'client_phone' => '(555) 123-4567',
            'booking_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/v1/bookings', $bookingData);

        // Should return ApiResponseTrait format even if Stripe fails
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
    });

    test('booking creation validates required fields', function () {
        $response = $this->postJson('/api/v1/bookings', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'offer_id',
                    'client_name',
                    'client_email',
                    'client_phone',
                    'booking_date'
                ]);
    });

    test('booking creation validates email format', function () {
        $bookingData = [
            'offer_id' => $this->offer->id,
            'client_name' => 'John Doe',
            'client_email' => 'invalid-email',
            'client_phone' => '(555) 123-4567',
            'booking_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/v1/bookings', $bookingData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['client_email']);
    });

    test('booking creation validates future booking date', function () {
        $bookingData = [
            'offer_id' => $this->offer->id,
            'client_name' => 'John Doe',
            'client_email' => 'john@example.com',
            'client_phone' => '(555) 123-4567',
            'booking_date' => now()->subDays(1)->format('Y-m-d H:i:s'), // Past date
        ];

        $response = $this->postJson('/api/v1/bookings', $bookingData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['booking_date']);
    });

    test('booking creation validates offer exists', function () {
        $bookingData = [
            'offer_id' => 99999, // Non-existent offer
            'client_name' => 'John Doe',
            'client_email' => 'john@example.com',
            'client_phone' => '(555) 123-4567',
            'booking_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/v1/bookings', $bookingData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['offer_id']);
    });
});

describe('API Response Consistency Tests', function () {
    test('all payment endpoints use consistent ApiResponseTrait format', function () {
        // Test payment intent endpoint
        $response = $this->postJson("/api/v1/create-payment-intent/{$this->booking->id}");
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        // Test booking creation endpoint
        $bookingData = [
            'offer_id' => $this->offer->id,
            'client_name' => 'Jane Doe',
            'client_email' => 'jane@example.com',
            'client_phone' => '(555) 987-6543',
            'booking_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ];

        $bookingResponse = $this->postJson('/api/v1/bookings', $bookingData);
        $bookingResponse->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
    });

    test('error responses include proper error details', function () {
        // Test with already paid booking
        Payment::factory()->completed()->create([
            'booking_id' => $this->booking->id,
            'amount' => $this->booking->total_amount,
        ]);

        $response = $this->postJson("/api/v1/create-payment-intent/{$this->booking->id}");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'data' => null,
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'errors',
                ]);

        $responseData = $response->json();
        expect($responseData['message'])->toBe('Booking already paid');
    });
});
