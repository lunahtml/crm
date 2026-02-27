@php
    $layout = filament()->getCurrentPanel()->getLayout();
@endphp

@extends($layout)

@section('content')
    {{ $slot }}
@endsection

@push('scripts')
    @php
        // Читаем манифест Vite и подключаем наш JS
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            if ($jsFile) {
                echo '<script type="module" src="' . asset('build/' . $jsFile) . '"></script>';
            }
        }
    @endphp

    <script>
        window.userId = {{ auth()->id() ?? 0 }};
        setTimeout(() => {
            if (window.initializeEcho) {
                window.initializeEcho(window.userId);
            }
        }, 1000);
    </script>
@endpush

@auth
    <script>
        window.initializeEcho({{ auth()->id() }});
    </script>
@endauth