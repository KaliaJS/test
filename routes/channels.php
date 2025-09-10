<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', fn (User $user, $userId) => (string) $user->id === (string) $userId);
Broadcast::channel('Guest.{guestId}', fn ($fakeUser, $guestId) => (string) $fakeUser->guest_id === (string) $guestId);
Broadcast::channel('adminChannel', fn (User $user) => $user->is_admin);
Broadcast::channel('terminalChannel', fn (User $user) => $user->is_terminal);