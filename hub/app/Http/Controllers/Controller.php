<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Return a standard JSON payload with a top-level data key.
     */
    protected function jsonData(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standard JSON payload with data and meta keys.
     *
     * @param  array<string, mixed>  $meta
     */
    protected function jsonDataWithMeta(mixed $data, array $meta, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    /**
     * Return a standard JSON payload with data and message keys.
     */
    protected function jsonDataWithMessage(mixed $data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * Return a standard not-found JSON response.
     */
    protected function jsonNotFound(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 404);
    }

    /**
     * Return a standard data response or a 404 response when the payload is missing.
     */
    protected function jsonDataOrNotFound(mixed $data, string $notFoundMessage): JsonResponse
    {
        if ($data === null) {
            return $this->jsonNotFound($notFoundMessage);
        }

        return $this->jsonData($data);
    }
}
