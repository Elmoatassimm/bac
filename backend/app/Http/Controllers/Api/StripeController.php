<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\StripePaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class StripeController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private StripePaymentService $stripePaymentService
    ) {}

    /**
     * Create a payment intent for a specific booking.
     */
    public function createPaymentIntent(Booking $booking): JsonResponse
    {
        try {
            $result = $this->stripePaymentService->createPaymentIntent($booking);

            return $this->successResponse([
                'clientSecret' => $result['client_secret'],
                'paymentIntentId' => $result['payment_intent_id']
            ], 'Payment intent created successfully');

        } catch (\Exception $e) {
            if ($e->getMessage() === 'Booking already paid') {
                return $this->errorResponse('Booking already paid', 400);
            }

            return $this->serverErrorResponse('Failed to create payment intent');
        }
    }
}
