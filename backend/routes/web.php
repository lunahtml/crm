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
