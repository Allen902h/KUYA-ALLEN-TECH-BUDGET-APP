<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', config('app.name', 'Budget App'))</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
</head>
<body class="@yield('body_class', 'app-shell')">
    <div class="ambient ambient-one"></div>
    <div class="ambient ambient-two"></div>

    @php
        $isAuthenticated = auth()->check();
        $brandName = 'KUYA ALLEN TECH SOLUTIONS';
        $brandTagline = 'Smart budget and tech workflow';
        $brandLogoFile = file_exists(public_path('images/kuya-allen-logo.png'))
            ? 'images/kuya-allen-logo.png'
            : (file_exists(public_path('images/wowlogo.png')) ? 'images/wowlogo.png' : 'icons/icon.svg');
        $brandLogo = asset($brandLogoFile).'?v='.(file_exists(public_path($brandLogoFile)) ? filemtime(public_path($brandLogoFile)) : time());
    @endphp

    <header class="topbar">
        <div class="shell topbar-inner">
            <div class="brand">
                <a href="{{ route('logo.viewer') }}" aria-label="View {{ $brandName }} logo">
                    <img class="brand-logo" src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
                </a>
                <a href="{{ $isAuthenticated ? route('dashboard') : route('welcome') }}">
                    <span>
                        <strong>{{ $brandName }}</strong>
                        <small>{{ $brandTagline }}</small>
                    </span>
                </a>
            </div>

            <nav class="topnav">
                @if ($isAuthenticated)
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('csv-import.index') }}">CSV Import</a>
                    <a href="{{ route('dashboard', ['cycle' => request('cycle')]) }}#planner">Planner</a>
                    <button type="button" class="install-trigger" hidden>Install App</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ghost-button">Logout</button>
                    </form>
                @else
                    <a href="{{ route('welcome') }}">Home</a>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}" class="ghost-button">Get Started</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="shell page-content">
        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="flash flash-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/app.js') }}?v={{ file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : time() }}"></script>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset('sw.js') }}');
        }
    </script>
</body>
</html>
