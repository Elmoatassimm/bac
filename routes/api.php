<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API v1 routes
Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{id}', [OfferController::class, 'show']);

    // Booking routes
    Route::post('/bookings', [BookingController::class, 'store']);

    // Stripe routes
    Route::post('/create-payment-intent/{booking}', [StripeController::class, 'createPaymentIntent']);

    // Stripe webhook (no authentication required)
    Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);


    
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
