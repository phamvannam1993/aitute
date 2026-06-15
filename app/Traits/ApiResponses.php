<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\ApiErrorsMessageBag;

trait ApiResponses
{
    /**
     * Success Response.
     *
     * @param  mixed  $data
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function successResponse(mixed $data, string $message = '', int $statusCode = JsonResponse::HTTP_OK, $status = 'success'): JsonResponse
    {
        if (!$message) {
            $message = JsonResponse::$statusTexts[$statusCode];
        }

        $data = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Error Response.
     *
     * @param  ApiErrorsMessageBag  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function errorResponse(ApiErrorsMessageBag $apiErrors, string $message = '', int $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $status = 'error'): JsonResponse
    {
        if (!$message) {
            $message = JsonResponse::$statusTexts[$statusCode];
        }

        $errors = $apiErrors->all();

        $data = [
            'status' => $status,
            'code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ];

        return new JsonResponse($data, $statusCode);
    }

    public function errorResponseValidate(MessageBag $apiErrors, string $message = '', int $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $status = 'error'): JsonResponse
    {
        $errors = array_map(function ($keys, $apiError) {
            return [
                'name' => $keys,
                'content' => $apiError[0] ?? '',
            ];
        }, $apiErrors->keys(), $apiErrors->messages());
        $data = [
            'status' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Response with status code 200.
     *
     * @param  mixed  $data
     * @return JsonResponse
     */
    public function okResponse(mixed $data): JsonResponse
    {
        return $this->successResponse($data);
    }

    /**
     * Response with status code 201.
     *
     * @param  mixed  $data
     * @return JsonResponse
     */
    public function createdResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->successResponse($data, $message, JsonResponse::HTTP_CREATED);
    }

    /**
     * Response with status code 204.
     *
     * @return JsonResponse
     */
    public function noContentResponse(): JsonResponse
    {
        return $this->successResponse([], '', JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Response with status code 400.
     *
     * @param  ApiErrorsMessageBag  $apiErrors
     * @param  string  $message
     * @return JsonResponse
     */
    public function badRequestResponse(ApiErrorsMessageBag $apiErrors, string $message = ''): JsonResponse
    {
        return $this->errorResponse($apiErrors, $message, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Response with status code 401.
     *
     * @param  ApiErrorsMessageBag  $apiErrors
     * @param  string  $message
     * @return JsonResponse
     */
    public function unauthorizedResponse(ApiErrorsMessageBag $apiErrors, string $message = ''): JsonResponse
    {
        return $this->errorResponse($apiErrors, $message, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Response with status code 403.
     *
     * @param  ApiErrorsMessageBag  $apiErrors
     * @param  string  $message
     * @return JsonResponse
     */
    public function forbiddenResponse(ApiErrorsMessageBag $apiErrors, string $message = ''): JsonResponse
    {
        return $this->errorResponse($apiErrors, $message, JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * Response with status code 404.
     *
     * @param  ApiErrorsMessageBag  $apiErrors
     * @param  string  $message
     * @return JsonResponse
     */
    public function notFoundResponse(ApiErrorsMessageBag $apiErrors, string $message = ''): JsonResponse
    {
        return $this->errorResponse($apiErrors, $message, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Response with status code 409.
     *
     * @param  ApiErrorsMessageBag  $apiErrors
     * @param  string  $message
     * @return JsonResponse
     */
    public function conflictResponse(ApiErrorsMessageBag $apiErrors, string $message = ''): JsonResponse
    {
        return $this->errorResponse($apiErrors, $message, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * Response with status code 422.
     *
     * @param  ApiErrorsMessageBag  $apiErrors
     * @param  string  $message
     * @return JsonResponse
     */
    public function unprocessableResponse(ApiErrorsMessageBag $apiErrors, string $message = ''): JsonResponse
    {
        return $this->errorResponse($apiErrors, $message, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
