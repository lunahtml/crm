@php
    $layout = filament()->getCurrentPanel()->getLayout();
@endphp

@extends($layout)

@section('content')
    {{ $slot }}
@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush