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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // // Регистрируем broadcasting routes с middleware 'web'
        // Broadcast::routes(['middleware' => ['web']]);

        // // Регистрируем драйвер centrifugo (чтобы Laravel знал, как его использовать)
        // Broadcast::extend('centrifugo', function ($app, $name) {
        //     return $app->make(\Denis660\LaravelCentrifugo\CentrifugoBroadcaster::class);
        // });

        // Подключаем кастомные Vite-ассеты в Filament
        if (class_exists(\Filament\FilamentManager::class)) {
            FilamentAsset::register([
                Css::make('app-css', Vite::asset('resources/css/app.css')),
                Js::make('app-js', Vite::asset('resources/js/app.js'))->module(),
            ]);
        }
    }
}