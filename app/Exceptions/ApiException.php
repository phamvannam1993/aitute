<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Helper\ApiErrorsMessageBag;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\Response;
use Throwable;

class ApiException extends \Exception implements HttpExceptionInterface
{

    public function __construct(
        protected readonly int $statusCode,
        protected readonly Throwable|null $previous,
        protected array $headers = []
    ) {
        parent::__construct(
            $previous?->getMessage() ?: '',
            $previous?->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            $previous ?? null,
        );
    }

    /**
     * Get the exception's context information.
     *
     * @return array
     */
    public function context(): array
    {
        try {
            return [
                "status" => 'error',
                "code" => $this->getCode(),
                "message" => $this->getStatusTexts(),
                "errors" => (new ApiErrorsMessageBag(['message' => $this->getMessage()]))->all(),
            ];
        } catch (\Throwable) {
            return [];
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
    public function getStatusTexts(): string
    {
        return Response::$statusTexts[$this->getCode()] ?? 'unknown status';
    }
}
