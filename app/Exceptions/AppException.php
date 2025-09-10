<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class AppException extends Exception
{
    protected string $errorCode;

    public function __construct(
        string $message,
        ?string $errorCode = 'generic',
        protected int $statusCode = 500
    ) {
        parent::__construct($message);

        $prefix = $this->inferErrorCode();
        $this->errorCode = $errorCode
            ? "$prefix.$errorCode"
            : $prefix;
    }

    public function render(Request $request): JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'message' => $this->getMessage(),
                'code' => $this->errorCode,
            ]
        ], $this->statusCode);
    }

    protected function inferErrorCode(): string
    {
        $name = class_basename(static::class);

        if ($name === 'AppException' || !str_ends_with($name, 'Exception')) {
            return 'app';
        }

        return strtolower(str_replace('Exception', '', $name));
    }
}
