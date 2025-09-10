<?php

namespace App\Exceptions;

use App\Exceptions\AppException;
use Illuminate\Http\JsonResponse;

class TerminalException extends AppException
{
    public static function notFound(): self
    {
        return new self('Le lecteur spécifié est introuvable.', 'not_found', 404);
    }

    public static function offline(): self
    {
        return new self('Le terminal est hors ligne.', 'offline', 503);
    }

    public static function busy(): self
    {
        return new self('Le terminal est occupé.', 'busy', 422);
    }

    public static function timeout(): self
    {
        return new self('Le terminal ne répond pas.', 'timeout', 504);
    }

    public static function invalidIntent(): self
    {
        return new self("L’Intent n’est pas à l’état requis pour effectuer l’opération.", 'intent_invalid_state', 409);
    }
}
