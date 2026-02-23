<?php

declare(strict_types=1);

namespace App\Infrastructure\Exceptions;

use Throwable;

class CustomException extends \Exception
{
    private array $errors = [];
    private int $statusCode = 400;

    public function __construct(string $message = "", int $statusCode = 400, array $errors = [], ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
