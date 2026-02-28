@php
    $layout = filament()->getCurrentPanel()->getLayout();
@endphp

@extends($layout)

@section('content')
    {{ $slot }}
@endsection

@push('scripts')
    {{-- Дебаг аутентификации и userId --}}
    <script>
        window.Laravel = window.Laravel || {};

        @auth
            console.log('✅ Blade auth check: user logged in');
            console.log('auth()->id() = {{ auth()->id() }}');
            console.log('auth()->user() = ', @json(auth()->user()));

            // Meta-тег как стабильный способ передать userId в JS
            document.head.insertAdjacentHTML('beforeend', `<meta name="user-id" content="{{ auth()->id() }}">`);
            window.Laravel.userId = {{ auth()->id() }};
        @else
            console.log('❌ Blade auth check: user NOT logged in');
            window.Laravel.userId = 0;
            document.head.insertAdjacentHTML('beforeend', `<meta name="user-id" content="0">`);
        @endauth

        console.log('✅ UserId из Blade:', window.Laravel.userId);

        // Лог всех cookies для проверки сессии
        console.log('document.cookie =', document.cookie);
    </script>

    {{-- Подключаем JS через Vite --}}
    @vite(['resources/js/app.js'])
@endpush