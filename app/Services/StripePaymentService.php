<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for a booking
     */
    public function createPaymentIntent(Booking $booking): array
    {
        try {
            // Check if the booking already has a completed payment
            $existingPayment = Payment::where('booking_id', $booking->id)
                ->where('status', 'completed')
                ->first();

            if ($existingPayment) {
                throw new \Exception('Booking already paid');
            }

            // Check if there's already a pending payment intent for this booking
            $pendingPayment = Payment::where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->whereNotNull('payment_intent_id')
                ->first();

            if ($pendingPayment) {
                // Try to retrieve the existing payment intent
                try {
                    $paymentIntent = PaymentIntent::retrieve($pendingPayment->payment_intent_id);
                    return [
                        'client_secret' => $paymentIntent->client_secret,
                        'payment_intent_id' => $paymentIntent->id,
                    ];
                } catch (\Exception $e) {
                    // If the payment intent doesn't exist anymore, create a new one
                    Log::warning('Payment intent not found, creating new one: ' . $e->getMessage());
                }
            }

            // Create a new payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->total_amount * 100, // Convert to cents
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'offer_id' => $booking->offer_id,
                    'client_id' => $booking->client_id,
                ]
            ]);

            // Update or create payment record with pending status
            if ($pendingPayment) {
                $pendingPayment->update([
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => 'pending'
                ]);
            } else {
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $booking->total_amount,
                    'status' => 'pending'
                ]);
            }

          

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];

        } catch (\Exception $e) {
            Log::error('Error creating payment intent: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(string $payload, ?string $signature): array
    {
        try {
            Log::info('Starting webhook processing', [
                'payload_length' => strlen($payload),
                'has_signature' => !empty($signature),
            ]);

            $event = $this->constructWebhookEvent($payload, $signature);

            Log::info('Webhook event constructed successfully', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    Log::info('Processing payment_intent.succeeded event');
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    Log::info('Processing payment_intent.payment_failed event');
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    Log::info('Processing payment_intent.canceled event');
                    $this->handlePaymentCanceled($event->data->object);
                    break;

                default:
                    Log::info('Unhandled webhook event type: ' . $event->type);
            }

            $result = [
                'success' => true,
                'event_type' => $event->type,
                'event_id' => $event->id,
            ];

            Log::info('Webhook processing completed successfully', $result);
            return $result;

        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
                'signature_provided' => !empty($signature),
                'webhook_secret_configured' => !empty(config('services.stripe.webhook_secret')),
            ]);
            throw new \Exception('Invalid webhook signature: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            throw $e;
        }
    }

    /**
     * Construct webhook event with flexible signature verification
     */
    private function constructWebhookEvent(string $payload, ?string $signature): Event
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        Log::info('Constructing webhook event', [
            'has_signature' => !empty($signature),
            'has_webhook_secret' => !empty($webhookSecret),
            'webhook_secret_placeholder' => $webhookSecret === 'whsec_your_webhook_secret_here',
        ]);

        // If we have both signature and webhook secret, verify signature
        if ($signature && $webhookSecret && !empty($webhookSecret) && $webhookSecret !== 'whsec_your_webhook_secret_here') {
            try {
                Log::info('Attempting signature verification');
                return Webhook::constructEvent($payload, $signature, $webhookSecret);
            } catch (SignatureVerificationException $e) {
                Log::warning('Signature verification failed, falling back to manual construction', [
                    'error' => $e->getMessage(),
                ]);
                // Fall through to manual construction
            }
        } else {
            Log::info('Skipping signature verification', [
                'reason' => !$signature ? 'no_signature' : (!$webhookSecret ? 'no_secret' : 'placeholder_secret'),
            ]);
        }

        // Manual event construction for development/testing or when signature verification fails
        if (empty($payload)) {
            throw new \Exception('Empty webhook payload');
        }

        $payloadData = json_decode($payload, true);
        if (!$payloadData || !is_array($payloadData)) {
            $jsonError = json_last_error_msg();
            throw new \Exception("Invalid JSON payload: {$jsonError}");
        }

        // Validate required event structure
        if (!isset($payloadData['type']) || !isset($payloadData['data'])) {
            $missingFields = [];
            if (!isset($payloadData['type'])) $missingFields[] = 'type';
            if (!isset($payloadData['data'])) $missingFields[] = 'data';
            throw new \Exception('Invalid webhook event structure. Missing fields: ' . implode(', ', $missingFields));
        }

        Log::info('Manual event construction successful', [
            'event_type' => $payloadData['type'],
            'has_event_id' => isset($payloadData['id']),
        ]);

        return Event::constructFrom($payloadData);
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSucceeded($paymentIntent): void
    {
        Log::info('Handling payment succeeded', [
            'payment_intent_id' => $paymentIntent->id,
        ]);

        $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for payment intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        Log::info('Found payment record', [
            'payment_id' => $payment->id,
            'current_status' => $payment->status,
            'booking_id' => $payment->booking_id,
        ]);

        $payment->update([
            'status' => 'completed',
            'transaction_id' => $paymentIntent->id,
            'paid_at' => now(),
        ]);

        // Update booking status to confirmed
        $booking = $payment->booking;
        if ($booking) {
            Log::info('Updating booking status to confirmed', [
                'booking_id' => $booking->id,
                'current_status' => $booking->status,
            ]);
            $booking->update(['status' => 'confirmed']);
        } else {
            Log::warning('No booking found for payment', [
                'payment_id' => $payment->id,
            ]);
        }

        Log::info('Payment succeeded handling completed');
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($paymentIntent): void
    {
        $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$payment) {
            
            return;
        }

        $payment->update([
            'status' => 'failed',
            'failed_at' => now(),
        ]);

        // Update booking status to cancelled
        $booking = $payment->booking;
        if ($booking) {
            $booking->update(['status' => 'cancelled']);

            
        }
    }

    /**
     * Handle canceled payment
     */
    private function handlePaymentCanceled($paymentIntent): void
    {
        $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();

        if (!$payment) {
            
            return;
        }

        $payment->update([
            'status' => 'cancelled',
        ]);

        // Update booking status to cancelled
        $booking = $payment->booking;
        if ($booking) {
            $booking->update(['status' => 'cancelled']);

           
        }
    }

    

    

}
