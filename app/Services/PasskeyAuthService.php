<?php

namespace App\Services;

use App\Models\Passkey;
use App\Support\PasskeyJsonSerializer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialSource;

class PasskeyAuthService
{
    public function generateOptions(string $email): array
    {
        try {
            $uuid = Str::uuid()->toString();

            $allowedCredentials = $email
                ? Passkey::whereRelation('user', 'email', $email)
                    ->get()
                    ->map(fn(Passkey $passkey) => $passkey->data)
                    ->map(fn(PublicKeyCredentialSource $publicKeyCredentialSource) => $publicKeyCredentialSource->getPublicKeyCredentialDescriptor())
                    ->all()
                : [];

            $options = new PublicKeyCredentialRequestOptions(
                challenge: Str::random(),
                rpId: config('passkey.rp_id'),
                allowCredentials: $allowedCredentials
            );

            Cache::put(
                "passkey-authentication-options-{$uuid}",
                $options,
                Carbon::now()->addMinute()
            );

            return [
                'optionsJSON' => PasskeyJsonSerializer::serialize($options),
                'uuid' => $uuid
            ];
        } catch (\Throwable $e) {
            \Log::error('Erreur dans ' . __METHOD__, [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Une erreur est survenue',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function validate(string $attResp, string $host, string $uuid): Passkey
    {
        $publicKeyCredential = PasskeyJsonSerializer::deserialize(
            $attResp,
            PublicKeyCredential::class
        );

        if (!$publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            throw new \InvalidArgumentException('Invalid public key response.');
        }

        $passkey = Passkey::firstWhere('credential_id', $publicKeyCredential->rawId);

        if (! $passkey) {
            throw new \RuntimeException('This passkey is not valid.');
        }

        try {
            $publicKeyCredentialSource = AuthenticatorAssertionResponseValidator::create(
                (new CeremonyStepManagerFactory())->requestCeremony()
            )->check(
                publicKeyCredentialSource: $passkey->data,
                authenticatorAssertionResponse: $publicKeyCredential->response,
                publicKeyCredentialRequestOptions: Cache::pull("passkey-authentication-options-{$uuid}"),
                host: $host,
                userHandle: null,
            );
        } catch (Throwable $exception) {
            throw new \RuntimeException('Failed to validate the passkey.', 0, $exception);
        }

        $passkey->update(['data' => $publicKeyCredentialSource]);

        return $passkey;
    }
}
