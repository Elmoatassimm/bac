<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private StripePaymentService $stripePaymentService
    ) {}
    /**
     * Handle Stripe webhook events.
     */
    public function handle(Request $request): JsonResponse
    {
        // Log incoming webhook request for debugging
        \Log::info('Webhook received', [
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'payload_length' => strlen($request->getContent()),
            'has_signature' => $request->hasHeader('Stripe-Signature'),
        ]);

        // Get webhook payload and signature
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        // Log payload details (be careful not to log sensitive data in production)
        \Log::info('Webhook payload details', [
            'payload_empty' => empty($payload),
            'payload_length' => strlen($payload),
            'signature_present' => !empty($signature),
            'webhook_secret_configured' => !empty(config('services.stripe.webhook_secret')),
        ]);

        try {
            // Delegate webhook processing to the service layer
            $result = $this->stripePaymentService->handleWebhook($payload, $signature);

            \Log::info('Webhook processed successfully', $result);

            return $this->successResponse(
                $result,
                'Webhook processed successfully'
            );

        } catch (\Exception $e) {
            // Log the specific error for debugging
            \Log::error('Webhook processing failed', [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_trace' => $e->getTraceAsString(),
                'payload_preview' => substr($payload, 0, 200), // First 200 chars for debugging
            ]);

            return $this->errorResponse('Webhook processing failed', 400);
        }
    }
}
