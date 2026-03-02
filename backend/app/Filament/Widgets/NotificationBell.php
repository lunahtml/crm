<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Widget
{
    protected static string $view = 'filament.widgets.notification-bell';
    
    protected static ?int $sort = -10; // Показывать сверху
    
    public static function canView(): bool
    {
        return Auth::check();
    }
    
    public function getUnreadCount()
    {
        return Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->count();
    }
    
    public function getNotifications()
    {
        return Auth::user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get();
    }
}