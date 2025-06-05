<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingsService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private BookingsService $bookingsService
    ) {}

    /**
     * Store a newly created booking.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $bookingData = [
                'offer_id' => $request->offer_id,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'booking_date' => $request->booking_date,
            ];

            $result = $this->bookingsService->createBooking($bookingData);

            return $this->createdResponse($result, 'Booking created successfully');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create booking');
        }
    }

}
