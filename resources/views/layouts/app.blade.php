<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BedTrack') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CDN for rapid development (remove in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00827F',   // Teal
                        secondary: '#F5F5F5', // Light Gray
                        accent: '#FF9800',    // Warm Orange
                    },
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-secondary min-h-screen font-sans antialiased">
    <div id="app">
        @if(Auth::check())
            <nav class="bg-primary text-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <span class="font-bold text-xl">BedTrack</span>
                            </div>
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                <a href="{{ route('dashboard') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('chat.index') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md text-sm font-medium">
                                    Chat
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if(Auth::user()->is_super_admin)
                            <a href="{{ route('super-admin.dashboard') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md text-sm font-medium">
                                Super Admin Dashboard
                            </a>
                            @elseif(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md text-sm font-medium">
                                Admin Dashboard
                            </a>
                            @endif
                            <div class="ml-3 relative">
                                <div>
                                    <span class="mr-2">{{ Auth::user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded bg-white/10 hover:bg-white/20 transition">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        @endif

        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            forceTLS: true
        });
    </script>
</body>
</html>
