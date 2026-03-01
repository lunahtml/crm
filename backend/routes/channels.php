<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('user.{id}', function ($user, $id) {
    Log::info('Channel authorization check', [
        'auth_user_id' => $user?->id,
        'channel_user_id' => $id,
        'match' => (int) $user?->id === (int) $id
    ]);

    return (int) $user?->id === (int) $id;
});