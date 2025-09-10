<?php

namespace App\Services;

use App\Support\PasskeyJsonSerializer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialUserEntity;

class PasskeyRegisterService
{
    public function generateOptions(string $email): string
    {
        $uuid = Str::uuid()->toString();

        $options = PublicKeyCredentialCreationOptions::create(
            rp: PublicKeyCredentialRpEntity::create(
                name: config('passkey.rp_name'),
                id: config('passkey.rp_id')
            ),
            user: PublicKeyCredentialUserEntity::create(
                name: $email,
                id: $uuid,
                displayName: $email
            ),
            challenge: Str::random(),
        );

        Cache::put(
            "passkey-registration-options-{$uuid}",
            $options,
            Carbon::now()->addMinute()
        );

        return PasskeyJsonSerializer::serialize($options);
    }

    public function validate(string $attResp, string $uuid, string $host): PublicKeyCredentialSource
    {
        $publicKeyCredential = PasskeyJsonSerializer::deserialize($attResp, PublicKeyCredential::class);

        if (!$publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            throw new \InvalidArgumentException('Invalid public key response type.');
        }

        return AuthenticatorAttestationResponseValidator::create(
            (new CeremonyStepManagerFactory())->creationCeremony(),
        )->check(
            authenticatorAttestationResponse: $publicKeyCredential->response,
            publicKeyCredentialCreationOptions: Cache::pull("passkey-registration-options-{$uuid}"),
            host: $host,
        );
    }
}
