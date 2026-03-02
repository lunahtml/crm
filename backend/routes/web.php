<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BroadcastAuthController;
use App\Models\Notification;

Route::post('/notifications/{id}/read', function ($id) {
    $notification = Notification::where('user_id', auth()->id())
        ->where('id', $id)
        ->first();
    
    if ($notification && !$notification->read_at) {
        $notification->update(['read_at' => now()]);
    }
    
    return response()->json(['success' => true]);
})->middleware('auth');

Route::post('/notifications/read-all', function () {
    auth()->user()->notifications()->unread()->update(['read_at' => now()]);
    return response()->json(['success' => true]);
})->middleware('auth');


Route::get('/api/notifications', function () {
    $user = auth()->user();
    return response()->json([
        'notifications' => $user->notifications()->latest()->limit(10)->get(),
        'unread_count' => $user->notifications()->whereNull('read_at')->count()
    ]);
})->middleware('auth');

Route::get('/', function () {
    return view('welcome');
});

// существующий маршрут
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
// Route::get('/debug-session', function () {
//     $user = auth()->user();
    
//     return response()->json([
//         'authenticated' => auth()->check(),
//         'user' => $user ? [
//             'id' => $user->id,
//             'name' => $user->name,
//             'email' => $user->email
//         ] : null,
//         'session_id' => session()->getId(),
//         'session_data' => session()->all(),
//         'cookies' => request()->cookies->all()
//     ]);
// })->middleware('web');

