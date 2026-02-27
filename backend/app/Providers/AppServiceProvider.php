<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Broadcast;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (class_exists(\Filament\FilamentManager::class)) {
            FilamentAsset::register([
                Css::make('app-css', Vite::asset('resources/css/app.css')),
                Js::make('app-js', Vite::asset('resources/js/app.js'))->module(),
            ]);
        }

        // Регистрируем broadcasting routes здесь
        Broadcast::routes(['middleware' => ['web']]);
    }
}