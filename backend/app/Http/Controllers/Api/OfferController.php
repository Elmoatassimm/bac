<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of offers.
     */
    public function index(): JsonResponse
    {
        try {
            $offers = Offer::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(
                $offers,
                'Offers retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse(
                'Failed to retrieve offers'
            );
        }
    }

    /**
     * Display the specified offer.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $offer = Offer::with('user')->findOrFail($id);

            return $this->successResponse(
                $offer,
                'Offer retrieved successfully'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Offer not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve offer');
        }
    }

}
