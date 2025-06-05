<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Offer;
use App\Models\Client;
use App\Events\BookingCreated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingsService
{
    public function __construct(
        private ClientsService $clientsService,
        private StripePaymentService $stripePaymentService
    ) {}

    /**
     * Create a new booking with payment intent
     */
    public function createBooking(array $bookingData): array
    {
        return DB::transaction(function () use ($bookingData) {
            // Get the offer and validate availability
            $offer = Offer::findOrFail($bookingData['offer_id']);

            // Create or find client
            $client = $this->clientsService->findOrCreateClient($bookingData);

            // Create booking record
            $booking = $this->createBookingRecord($offer, $client, $bookingData);

            // Create payment intent
            $paymentResult = $this->stripePaymentService->createPaymentIntent($booking);

           

            // Dispatch the BookingCreated event for email notification
            BookingCreated::dispatch($booking);

            return [
                'booking' => $booking->load(['offer', 'client', 'payment']),
                'client_secret' => $paymentResult['client_secret'],
            ];
        });
    }

    /**
     * Create the booking database record
     */
    private function createBookingRecord(Offer $offer, Client $client, array $data): Booking
    {
        return Booking::create([
            'offer_id' => $offer->id,
            'client_id' => $client->id,
            'booking_date' => $data['booking_date'],
            'total_amount' => $offer->price,
            'status' => 'pending',
        ]);
    }

    

    /**
     * Get booking by ID
     */
    public function getBookingById(int $bookingId): ?Booking
    {
        return Booking::with(['offer', 'client', 'payment'])->find($bookingId);
    }

    

  


}
