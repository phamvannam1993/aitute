<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class AjaxException extends ApiException
{
    public function __construct(
        protected readonly string $ajaxMessage,
        protected readonly int $ajaxCode,
        protected readonly Throwable|null $ajaxPrevious,
        protected array $headers = []
    ) {
        parent::__construct(
            $ajaxCode,
            new DomainException($ajaxMessage, $ajaxCode, $ajaxPrevious),
            $headers,
        );
    }
}
