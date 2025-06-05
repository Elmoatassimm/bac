<?php

namespace App\Jobs;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The booking instance.
     */
    public Booking $booking;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load the booking with its relationships
            $this->booking->load(['client', 'offer', 'offer.user']);

            // Send the new booking notification email to the service provider
            Mail::to($this->booking->offer->user->email)
                ->send(new BookingConfirmationMail($this->booking));

            Log::info('New booking notification email sent successfully', [
                'booking_id' => $this->booking->id,
                'provider_email' => $this->booking->offer->user->email,
                'client_name' => $this->booking->client->name,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send new booking notification email', [
                'booking_id' => $this->booking->id,
                'provider_email' => $this->booking->offer->user->email ?? 'unknown',
                'client_name' => $this->booking->client->name ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('New booking notification email job failed permanently', [
            'booking_id' => $this->booking->id,
            'provider_email' => $this->booking->offer->user->email ?? 'unknown',
            'client_name' => $this->booking->client->name ?? 'unknown',
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
