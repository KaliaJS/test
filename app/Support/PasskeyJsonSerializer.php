<?php

namespace App\Support;

use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;

class PasskeyJsonSerializer
{
    public static function serialize(object $data): string
    {
        return (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->serialize($data, 'json');
    }

    public static function deserialize(string $json, string $into)
    {
        return (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($json, $into, 'json');
    }
}
