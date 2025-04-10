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

    <!-- Tailwind CDN as fallback -->
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
    
    <!-- Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <!-- Pusher Test Script - Only for development/debugging -->
    @if(config('app.debug'))
    <script>
        // Create a small utility to test Pusher connection
        document.addEventListener('DOMContentLoaded', function() {
            // Only run this test script if we're not already on the chat page
            if (!window.pusher && !document.getElementById('debug-status')) {
                console.log('Running Pusher diagnostic test...');
                
                // Create a debug element
                const debugEl = document.createElement('div');
                debugEl.style.position = 'fixed';
                debugEl.style.bottom = '5px';
                debugEl.style.left = '5px';
                debugEl.style.padding = '10px';
                debugEl.style.background = 'rgba(0,0,0,0.7)';
                debugEl.style.color = 'white';
                debugEl.style.fontSize = '12px';
                debugEl.style.zIndex = '9999';
                debugEl.style.maxWidth = '400px';
                debugEl.style.maxHeight = '200px';
                debugEl.style.overflow = 'auto';
                debugEl.style.borderRadius = '4px';
                document.body.appendChild(debugEl);
                
                function log(message) {
                    console.log(message);
                    debugEl.innerHTML += message + '<br>';
                    debugEl.scrollTop = debugEl.scrollHeight;
                }
                
                // Test Pusher connection
                try {
                    log('Initializing Pusher test...');
                    const testPusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                        forceTLS: true,
                        wsHost: '{{ env('PUSHER_HOST', 'ws-'.env('PUSHER_APP_CLUSTER').'.pusher.com') }}',
                        wsPort: {{ env('PUSHER_PORT', 443) }},
                        wssPort: {{ env('PUSHER_PORT', 443) }},
                        authEndpoint: '{{ url('/broadcasting/auth') }}',
                        auth: {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        },
                        enableLogging: true
                    });
                    
                    testPusher.connection.bind('connecting', () => {
                        log('Connecting to Pusher...');
                    });
                    
                    testPusher.connection.bind('connected', () => {
                        log('Successfully connected to Pusher!');
                        
                        // Try to subscribe to a test channel
                        const testChannel = testPusher.subscribe('test-channel');
                        
                        testChannel.bind('pusher:subscription_succeeded', () => {
                            log('Successfully subscribed to test channel');
                        });
                        
                        testChannel.bind('test-event', (data) => {
                            log('Received test event: ' + JSON.stringify(data));
                        });
                        
                        log('Listening for events on test-channel...');
                    });
                    
                    testPusher.connection.bind('disconnected', () => {
                        log('Disconnected from Pusher');
                    });
                    
                    testPusher.connection.bind('error', (err) => {
                        log('Connection error: ' + JSON.stringify(err));
                    });
                    
                    // Add a close button
                    const closeBtn = document.createElement('button');
                    closeBtn.textContent = 'Close';
                    closeBtn.style.marginTop = '10px';
                    closeBtn.style.padding = '3px 8px';
                    closeBtn.style.borderRadius = '3px';
                    closeBtn.style.background = '#f44336';
                    closeBtn.style.color = 'white';
                    closeBtn.style.border = 'none';
                    closeBtn.style.cursor = 'pointer';
                    closeBtn.onclick = function() {
                        testPusher.disconnect();
                        debugEl.remove();
                    };
                    debugEl.appendChild(document.createElement('br'));
                    debugEl.appendChild(closeBtn);
                    
                } catch (error) {
                    log('Error initializing Pusher: ' + error.message);
                }
            }
        });
    </script>
    @endif
    
    <!-- Ensure Echo is properly loaded before other scripts -->
    <script>
        window.isEchoInitialized = false;
        const APP_KEY = '{{ env('PUSHER_APP_KEY') }}';
        const APP_CLUSTER = '{{ env('PUSHER_APP_CLUSTER') }}';
        
        function initEcho() {
            if (window.isEchoInitialized) return;
            
            try {
                console.log('Initializing Echo directly with key:', APP_KEY, 'cluster:', APP_CLUSTER);
                
                window.Echo = new (function() {
                    const pusher = new Pusher(APP_KEY, {
                        cluster: APP_CLUSTER,
                        forceTLS: true,
                        wsHost: '{{ env('PUSHER_HOST', 'ws-'.env('PUSHER_APP_CLUSTER').'.pusher.com') }}',
                        wsPort: {{ env('PUSHER_PORT', 443) }},
                        wssPort: {{ env('PUSHER_PORT', 443) }},
                        authEndpoint: '{{ url('/broadcasting/auth') }}',
                        auth: {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        }
                    });
                    
                    this.private = function(channel) {
                        console.log('Subscribing to private channel:', channel);
                        const listeners = {};
                        const pusherChannel = pusher.subscribe('private-' + channel);
                        
                        // Add connection event listeners for debugging
                        pusherChannel.bind('pusher:subscription_succeeded', () => {
                            console.log('Successfully subscribed to private-' + channel);
                        });
                        
                        pusherChannel.bind('pusher:subscription_error', (error) => {
                            console.error('Failed to subscribe to private-' + channel, error);
                        });
                        
                        return {
                            listen: function(event, callback) {
                                // Handle Laravel Echo event name format (.event-name)
                                const eventName = event.startsWith('.') ? event.substring(1) : event;
                                console.log('Listening for event on channel', channel + ':', eventName);
                                pusherChannel.bind(eventName, callback);
                                listeners[event] = callback;
                                return this;
                            }
                        };
                    };
                })();
                
                window.isEchoInitialized = true;
                console.log('Echo initialized directly');
            } catch (error) {
                console.error('Failed to initialize Echo:', error);
            }
        }
        
        // Initialize immediately
        document.addEventListener('DOMContentLoaded', function() {
            // Double check if Echo is still not initialized by Vite
            if (!window.Echo) {
                initEcho();
            } else {
                console.log('Echo already initialized by something else');
            }
        });
        
        // Fallback to init after a short delay if not yet initialized
        setTimeout(function() {
            if (!window.Echo && !window.isEchoInitialized) {
                console.log('Echo still not initialized, trying fallback initialization');
                initEcho();
            }
        }, 1000);
    </script>
    
    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Direct script includes as fallback -->
    @if(app()->environment('production'))
    <script src="{{ asset('build/assets/app2.js') }}" defer></script>
    <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet">
    @endif
</head>
<body class="bg-secondary min-h-screen font-sans antialiased">
    <div id="app">
        @if(Auth::check())
            <nav class="bg-primary text-white shadow-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center space-x-4">
                                <span class="font-bold text-xl">BedTrack</span>
                            </div>
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                <a href="{{ route('dashboard') }}"
                                   class="relative inline-flex items-center px-3 py-2 text-base font-medium rounded-md transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                    @if(request()->routeIs('dashboard'))
                                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-white rounded-full"></span>
                                    @endif
                                </a>
                                @auth
                                <a href="{{ route('chat.show', ['chatRoom' => \App\Models\ChatRoom::where('type', 'global')->first()->id]) }}"
                                   class="relative inline-flex items-center px-3 py-2 text-base font-medium rounded-md transition-all duration-200 {{ request()->routeIs('chat.*') ? 'bg-white/20 text-white' : 'text-white hover:bg-white/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Chat
                                    @if(request()->routeIs('chat.*'))
                                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-white rounded-full"></span>
                                    @endif
                                    @php
                                        $unreadCount = \App\Models\ChatMessage::whereHas('chatRoom', function($query) {
                                            $query->where('type', 'global');
                                        })
                                        ->where('user_id', '!=', auth()->id())
                                        ->whereDoesntHave('reads', function($query) {
                                            $query->where('user_id', auth()->id());
                                        })
                                        ->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="absolute -bottom-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </a>
                                @endauth
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if(Auth::user()->role === 'superadmin')
                            <a href="{{ route('super-admin.dashboard') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md text-sm font-medium">
                                Super Admin Dashboard
                            </a>
                            <a href="{{ route('users.index') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md text-sm font-medium">
                                User Management
                            </a>
                            @elseif(Auth::user()->role === 'admin')
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
</body>
</html>
