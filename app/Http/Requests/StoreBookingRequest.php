<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow public booking requests
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'offer_id' => 'required|exists:offers,id',
            'client_name' => [
                'required',
                'string',
                'max:255'
            ],
            'client_email' => [
                'required',
                'email',
                'max:255'
            ],
            'client_phone' => [
                'required',
                'string',
                
            ],
            'booking_date' => [
                'required',
                'date',
                'after:now',
                
            ],
        ];
    }

    
}
