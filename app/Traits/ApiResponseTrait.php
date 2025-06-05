<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a successful JSON response.
     *
     * @param mixed $data The data to return
     * @param string $message Success message
     * @param int $statusCode HTTP status code (200, 201, etc.)
     * @param array|object|null $meta Additional metadata (pagination, timestamps, etc.)
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Operation successful', int $statusCode = 200, $meta = null): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ];

        // Add meta information if provided
        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code (400, 403, etc.)
     * @param mixed $errors Detailed error information
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400, $errors = null, $meta = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ];

        // Add meta information if provided
        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a 404 not found JSON response.
     *
     * @param string $message Not found message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found', $meta = null): JsonResponse
    {
        return $this->errorResponse($message, 404, null, $meta);
    }

    /**
     * Return a 422 validation error JSON response.
     *
     * @param mixed $errors Validation errors (typically from validator)
     * @param string $message Validation error message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed', $meta = null): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors, $meta);
    }

    /**
     * Return a 500 server error JSON response.
     *
     * @param string $message Server error message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Internal server error', $meta = null): JsonResponse
    {
        return $this->errorResponse($message, 500, null, $meta);
    }

    /**
     * Return a 201 created JSON response.
     *
     * @param mixed $data The created resource data
     * @param string $message Success message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function createdResponse($data = null, string $message = 'Resource created successfully', $meta = null): JsonResponse
    {
        return $this->successResponse($data, $message, 201, $meta);
    }

    /**
     * Return a 204 no content JSON response.
     *
     * @param string $message Success message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function noContentResponse(string $message = 'Operation completed successfully', $meta = null): JsonResponse
    {
        return $this->successResponse(null, $message, 204, $meta);
    }

    /**
     * Return a 401 unauthorized JSON response.
     *
     * @param string $message Unauthorized message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized access', $meta = null): JsonResponse
    {
        return $this->errorResponse($message, 401, null, $meta);
    }

    /**
     * Return a 403 forbidden JSON response.
     *
     * @param string $message Forbidden message
     * @param array|object|null $meta Additional metadata
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Access forbidden', $meta = null): JsonResponse
    {
        return $this->errorResponse($message, 403, null, $meta);
    }

    /**
     * Return a paginated success response with pagination metadata.
     *
     * @param mixed $data Paginated data
     * @param string $message Success message
     * @param array $paginationMeta Pagination metadata (current_page, total, etc.)
     * @param array|object|null $additionalMeta Additional metadata
     * @return JsonResponse
     */
    protected function paginatedResponse($data, string $message = 'Data retrieved successfully', array $paginationMeta = [], $additionalMeta = null): JsonResponse
    {
        $meta = $paginationMeta;
        
        if ($additionalMeta !== null) {
            $meta = array_merge($meta, (array) $additionalMeta);
        }

        return $this->successResponse($data, $message, 200, $meta);
    }
}
