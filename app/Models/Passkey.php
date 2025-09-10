<?php

namespace App\Models;

use App\Support\PasskeyJsonSerializer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webauthn\PublicKeyCredentialSource;

class Passkey extends Model
{
    use HasFactory;

    protected $casts = [
        'credential_id' => 'encrypted',
        'data' => 'json',
    ];

    public function data(): Attribute
    {
        return new Attribute(
            get: fn(string $value) => PasskeyJsonSerializer::deserialize($value, PublicKeyCredentialSource::class),
            set: fn(PublicKeyCredentialSource $value) => [
                'credential_id' => $value->publicKeyCredentialId,
                'data' => PasskeyJsonSerializer::serialize($value),
            ],
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
