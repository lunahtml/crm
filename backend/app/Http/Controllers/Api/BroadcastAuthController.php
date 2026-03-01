<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Controller;

class BroadcastAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        Log::info('=== BROADCAST AUTH DEBUG ===', [
            'user_id' => auth()->user()?->id,
            'user_email' => auth()->user()?->email,
            'channel' => $request->channel_name,
            'socket_id' => $request->socket_id,
            'session_id' => session()->getId(),
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => [
                'cookie' => $request->header('Cookie'),
                'x-csrf' => $request->header('X-CSRF-TOKEN'),
                'accept' => $request->header('Accept'),
            ]
        ]);

        try {
            // Проверяем авторизацию
            if (!auth()->check()) {
                Log::error('Broadcast auth failed: user not authenticated');
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Проверяем, имеет ли пользователь доступ к каналу
            $channel = $request->channel_name;
            $socketId = $request->socket_id;
            
            // Извлекаем ID из channel_name (формат: private-user.2)
            preg_match('/private-user\.(\d+)/', $channel, $matches);
            $channelUserId = $matches[1] ?? null;
            
            Log::info('Channel parsing', [
                'channel' => $channel,
                'parsed_user_id' => $channelUserId,
                'auth_user_id' => auth()->id()
            ]);
            
            // Проверяем соответствие ID
            if ($channelUserId && (int)$channelUserId !== (int)auth()->id()) {
                Log::error('Broadcast auth failed: user ID mismatch');
                return response()->json(['error' => 'Forbidden'], 403);
            }

            // Используем стандартный механизм Broadcast
            $response = Broadcast::auth($request);
            
            Log::info('Broadcast auth success', [
                'user' => auth()->id(),
                'channel' => $channel
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Broadcast auth exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Broadcast auth failed',
                'message' => $e->getMessage()
            ], 403);
        }
    }
}