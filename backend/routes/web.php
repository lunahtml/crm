<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BroadcastAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Ваш существующий маршрут
Route::get('/api/current-user', function () {
    return auth()->user() ? [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
    ] : null;
})->middleware('auth');

// Добавляем маршрут для broadcast авторизации
Route::post('/broadcasting/auth', [BroadcastAuthController::class, 'authenticate'])
    ->middleware(['web', 'auth']);


    // Тестовый маршрут для проверки авторизации
Route::get('/debug-session', function () {
    $user = auth()->user();
    
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ] : null,
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'cookies' => request()->cookies->all()
    ]);
})->middleware('web');


Route::get('/test-pusher', function () {
    try {
        $pusher = new Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'host' => 'api-' . env('PUSHER_APP_CLUSTER') . '.pusher.com',
                'port' => 443,
            ]
        );
        
        $result = $pusher->trigger('test-channel', 'test-event', ['message' => 'Hello from Laravel!']);
        
        Log::info('Pusher test result', ['result' => $result]);
        
        return response()->json([
            'success' => $result,
            'message' => 'Pusher test completed'
        ]);
    } catch (\Exception $e) {
        Log::error('Pusher test failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});
