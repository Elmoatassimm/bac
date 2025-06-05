<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Jobs\SendBookingConfirmationEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        try {
            // Dispatch the email job to the queue
            SendBookingConfirmationEmailJob::dispatch($event->booking);

            Log::info('New booking notification email job dispatched', [
                'booking_id' => $event->booking->id,
                'provider_email' => $event->booking->offer->user->email,
                'client_name' => $event->booking->client->name,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to dispatch new booking notification email job', [
                'booking_id' => $event->booking->id,
                'provider_email' => $event->booking->offer->user->email ?? 'unknown',
                'client_name' => $event->booking->client->name ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            // Don't re-throw to prevent blocking the booking creation
        }
    }
}
