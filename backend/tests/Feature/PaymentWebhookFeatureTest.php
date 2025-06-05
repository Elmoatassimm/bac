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
    $this->payment = Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_intent_id' => 'pi_test_webhook_123456',
        'amount' => $this->booking->total_amount,
        'status' => 'pending',
    ]);
    
    // Set webhook secret for testing
    Config::set('services.stripe.webhook_secret', 'whsec_79d0966b68db24133f1981cf43d6133790913982b213924b0e588fe40feffa89');
});

describe('Stripe Webhook Integration Tests', function () {
    test('webhook endpoint returns ApiResponseTrait format for valid payload', function () {
        $validPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => $this->payment->payment_intent_id,
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $validPayload);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        $responseData = $response->json();
        expect($responseData)->toHaveKey('success');
        expect($responseData)->toHaveKey('message');
    });

    test('webhook handles payment_intent.succeeded event', function () {
        // Verify initial state
        expect($this->payment->status)->toBe('pending');
        expect($this->booking->status)->toBe('pending');

        $successPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => $this->payment->payment_intent_id,
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $successPayload);

        // Should process successfully
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        // Verify payment status was updated
        $updatedPayment = $this->payment->fresh();
        expect($updatedPayment->status)->toBe('completed');

        // Verify booking status was updated
        $updatedBooking = $this->booking->fresh();
        expect($updatedBooking->status)->toBe('confirmed');
    });

    test('webhook handles payment_intent.payment_failed event', function () {
        // Verify initial state
        expect($this->payment->status)->toBe('pending');
        expect($this->booking->status)->toBe('pending');

        $failedPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => $this->payment->payment_intent_id,
                    'object' => 'payment_intent',
                    'status' => 'failed',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $failedPayload);

        // Should process successfully
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        // Verify payment status was updated
        $updatedPayment = $this->payment->fresh();
        expect($updatedPayment->status)->toBe('failed');

        // Verify booking status was updated
        $updatedBooking = $this->booking->fresh();
        expect($updatedBooking->status)->toBe('cancelled');
    });

    test('webhook handles unknown event types gracefully', function () {
        $unknownEventPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'unknown.event.type',
            'data' => [
                'object' => [
                    'id' => 'some_id',
                    'object' => 'unknown_object',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $unknownEventPayload);

        // Should handle gracefully and return success
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        $responseData = $response->json();
        expect($responseData['success'])->toBeTrue();
        expect($responseData['message'])->toBe('Webhook processed successfully');
    });

    test('webhook handles invalid JSON payload', function () {
        $response = $this->call('POST', '/api/v1/webhook/stripe', [], [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], 'invalid json payload');

        $response->assertStatus(400)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'errors',
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid payload',
                ]);
    });

    test('webhook handles empty payload', function () {
        $response = $this->postJson('/api/v1/webhook/stripe', []);

        // Should handle gracefully - empty payload might be processed or rejected
        expect($response->status())->toBeIn([200, 400]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
    });

    test('webhook handles payment intent not found in database', function () {
        $nonExistentPaymentPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_nonexistent_payment_intent',
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $nonExistentPaymentPayload);

        // Should handle gracefully even if payment not found
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        $responseData = $response->json();
        expect($responseData['success'])->toBeTrue();
        expect($responseData['message'])->toBe('Webhook processed successfully');
    });
});

describe('Webhook Business Logic Tests', function () {
    test('webhook prevents duplicate payment status updates', function () {
        // Mark payment as completed first
        $this->payment->markAsCompleted();
        $this->booking->update(['status' => 'confirmed']);

        $successPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => $this->payment->payment_intent_id,
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $successPayload);

        // Should handle gracefully
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);

        // Status should remain completed
        expect($this->payment->fresh()->status)->toBe('completed');
        expect($this->booking->fresh()->status)->toBe('confirmed');
    });

    test('webhook updates payment timestamps correctly', function () {
        $originalPaidAt = $this->payment->paid_at;
        $originalFailedAt = $this->payment->failed_at;

        expect($originalPaidAt)->toBeNull();
        expect($originalFailedAt)->toBeNull();

        // Test successful payment
        $this->payment->markAsCompleted();
        expect($this->payment->fresh()->paid_at)->not->toBeNull();
        expect($this->payment->fresh()->failed_at)->toBeNull();

        // Reset and test failed payment
        $this->payment->update(['status' => 'pending', 'paid_at' => null]);
        $this->payment->markAsFailed();
        expect($this->payment->fresh()->failed_at)->not->toBeNull();
    });

    test('webhook maintains data consistency between payment and booking', function () {
        $successPayload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => $this->payment->payment_intent_id,
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $successPayload);

        // Verify both payment and booking are updated consistently
        $updatedPayment = $this->payment->fresh();
        $updatedBooking = $this->booking->fresh();

        expect($updatedPayment->status)->toBe('completed');
        expect($updatedBooking->status)->toBe('confirmed');
        expect($updatedPayment->booking_id)->toBe($updatedBooking->id);
    });
});

describe('Webhook Security and Validation Tests', function () {
    test('webhook endpoint is publicly accessible', function () {
        // Webhook should not require authentication
        $payload = [
            'id' => 'evt_test_webhook',
            'object' => 'event',
            'type' => 'test.event',
            'data' => ['object' => []]
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $payload);

        // Should not return 401 or 403 (authentication/authorization errors)
        expect($response->status())->not->toBe(401);
        expect($response->status())->not->toBe(403);
    });

    test('webhook handles malformed event structure', function () {
        $malformedPayload = [
            'not_an_event' => 'invalid structure',
            'missing' => 'required fields'
        ];

        $response = $this->postJson('/api/v1/webhook/stripe', $malformedPayload);

        // Should handle gracefully
        expect($response->status())->toBeIn([200, 400]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors',
        ]);
    });
});
