@php
    $layout = filament()->getCurrentPanel()->getLayout();
@endphp

@extends($layout)

@section('content')
    {{ $slot }}
@endsection

@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @else
        <meta name="user-id" content="0">
    @endauth

    @vite(['resources/js/app.js'])
@endpush