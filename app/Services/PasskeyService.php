<?php

namespace App\Services;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Support\Str;
use Webauthn\PublicKeyCredentialSource;

class PasskeyService
{
    public function save(User $user, PublicKeyCredentialSource $publicKeyCredentialSource): Passkey
    {
        return $user->passkeys()->create([
            'name' => Str::uuid()->toString(),
            'data' => $publicKeyCredentialSource,
        ]);
    }
}
