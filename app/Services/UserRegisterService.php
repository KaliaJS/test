<?php

namespace App\Services;

use App\Mail\NewUserCreated;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserRegisterService
{
    private ?User $user = null;

    private function __construct(
        private readonly string $email
    ) {}

    public static function create(string $email): self
    {
        return new self($email);
    }

    public function withPassword($password)
    {
        $this->user = User::create([
            'email' => $this->email,
            'password' => Hash::make($password),
        ]);
        return $this;
    }

    public function withPasskey()
    {
        $this->user = User::create(['email' => $this->email]);
        return $this;
    }

    public function notify(): self
    {
        Mail::to($this->email)->send(new NewUserCreated());

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
