<?php

namespace App\Services;

use App\Models\User;
use App\Support\DeviceDetector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserAuthService
{
    private ?User $user = null;
    private ?string $email = null;
    private ?string $token = null;
    private DeviceDetector $deviceDetector;
    
    public function __construct(
        DeviceDetector $deviceDetector,
        ?User $user = null,
        ?string $email = null,
    )
    {
        $this->deviceDetector = $deviceDetector;
        $this->user = $user;
        $this->email = $email ?? $user?->email;
    }

    public static function login(string $email): self
    {
        return App::makeWith(self::class, [
            'user' => null,
            'email' => $email
        ]);
    }

    public static function fromUser(User $user): self
    {
        return App::makeWith(self::class, [
            'user' => $user,
            'email' => null
        ]);
    }

    public function withPassword($password)
    {
        if (!$this->user) {
            $this->user = User::where('email', $this->email)->first();
        }

        if (!$this->user || !Hash::check($password, $this->user->password)) {
            throw ValidationException::withMessages(['password' => [__('auth.failed')]]);
        }

        return $this;
    }

    public function withPasskey()
    {
        $this->ensureUserLoaded();

        $tokenName = $this->deviceDetector->createDeviceName();

        $this->user->tokens()
             ->where('name', $tokenName)
             ->delete();

        $this->token = $this->user->createToken($tokenName)->plainTextToken;

        return $this;
    }

    public function createToken(?string $name = null, ?DateTimeInterface $expiresAt = null): string
    {
        $this->ensureUserLoaded();

        return $this->user->createToken(
            name: $name ?? $this->deviceDetector->createDeviceName(),
            expiresAt: $expiresAt ?? Carbon::now()->addYear()
        )->plainTextToken;
    }

    public function retrieveToken(string $name): MorphMany
    {
        $this->ensureUserLoaded();

        return $this->user->tokens()->where('name', $name);
    }

    public function renewToken(string $name): string
    {
        $this->retrieveToken($name)->delete();

        return $this->createToken($name);
    }

    public function getToken(): ?string
    {
        return $this->token ?? $this->user?->currentAccessToken()?->plainTextToken;
    }

    public function getUser(): User
    {
        $this->ensureUserLoaded();

        return $this->user;
    }

    public function createResetToken(string $email): string
    {
        DB::table('password_resets')->where('email', $email)->delete();

        $key = config('app.key');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $token = hash_hmac('sha256', Str::random(40), $key);

        DB::table('password_resets')->insert([
            'email' => $this->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        return $token;
    }

    public function resetPassword(string $newPassword, string $resetToken): bool
    {
        if (!$this->email) {
            throw new \RuntimeException('Email is required to reset password');
        }

        $record = DB::table('password_resets')
            ->where('email', $this->email)
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'password' => "Le lien pour réinitialiser votre mot de passe n'est plus valide.",
            ]);
        }

        $tokenExpired = Carbon::parse($record->created_at)
            ->addMinutes(5)
            ->isPast();

        if ($tokenExpired || !Hash::check($resetToken, $record->token)) {
            throw ValidationException::withMessages([
                'password' => "Le lien pour réinitialiser votre mot de passe n'est plus valide.",
            ]);
        }

        DB::table('password_resets')->where('email', $this->email)->delete();

        return $this->user
            ->forceFill(['password' => Hash::make($newPassword)])
            ->save();
    }

    private function ensureUserLoaded(): void
    {
        if (!$this->user) {
            if (!$this->email) {
                throw new \RuntimeException('No user or email provided');
            }
            
            $this->user = User::where('email', $this->email)->firstOrFail();
        }
    }
}
