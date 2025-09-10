<?php

namespace App\Exceptions;

use App\Exceptions\AppException;
use Illuminate\Http\JsonResponse;

class OrderException extends AppException
{
    public static function alreadyPaid(): self
    {
        return new self('Cette commande a déjà été payée.', 'already_paid', 409);
    }

    public static function notFound(): self
    {
        return new self('Commande introuvable.', 'not_found', 404);
    }
}
