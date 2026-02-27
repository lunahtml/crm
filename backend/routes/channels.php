<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Log::info('Broadcast routes registered with middleware: web');

Broadcast::routes(['middleware' => ['web']]);

Broadcast::channel('user.{id}', function ($user, $id) {
    Log::info('Auth check for user channel', ['user_id' => $user?->id ?? 'guest', 'requested_id' => $id]);
    return (int) $user->id === (int) $id;
});
