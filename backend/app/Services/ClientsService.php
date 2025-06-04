<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ClientsService
{
    /**
     * Find or create a client based on email
     */
    public function findOrCreateClient(array $clientData): Client
    {
        $client = Client::firstOrCreate(
            ['email' => $clientData['client_email']],
            [
                'name' => $clientData['client_name'],
                'phone' => $clientData['client_phone'],
            ]
        );

     

        return $client;
    }

    /**
     * Get client by ID
     */
    public function getClientById(int $clientId): ?Client
    {
        return Client::with('bookings')->find($clientId);
    }

  

   

    

}
