<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kaizen')</title>

    <!-- CSS Resources -->
    @include('components.styles')
    
    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Background Elements -->
    @include('components.background')
    
    <!-- Navigation -->
    @include('components.navbar')
    
    <!-- Main Content -->
    <main class="container py-4 flex-grow-1" id="content">
        <div class="content-container @if(request()->is('login') || request()->is('register')) auth-page @endif">
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    @include('components.footer')
    
    <!-- JS Resources -->
    @include('components.scripts')
    
    @yield('scripts')
</body>
</html>
