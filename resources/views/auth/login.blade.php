<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'BedTrack') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00827F',
                        secondary: '#F5F5F5',
                        accent: '#FF9800',
                    },
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-primary min-h-screen font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="px-8 pt-8 pb-6">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">BedTrack</h1>
                    <p class="text-gray-500 mt-2">Please sign in to your account</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 rounded bg-red-50 border border-red-200">
                        <ul class="list-disc pl-5 text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-600" onclick="togglePasswordVisibility('password')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Select Ward <span class="text-xs text-gray-500">(Optional for admin)</span></label>
                        <select id="ward_id" name="ward_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select a ward...</option>
                            @foreach($wards as $ward)
                                <option value="{{ $ward->id }}">{{ $ward->ward_name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Admin users can access all wards from the admin dashboard.</p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-accent hover:bg-green-500/90 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>

            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-center">
                <p class="text-xs text-gray-500">© {{ date('Y') }} BedTrack. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Toggle Eye Icon for Password Visibility -->
<script>
    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeOpen = passwordInput.parentElement.querySelector('.eye-open');
        const eyeClosed = passwordInput.parentElement.querySelector('.eye-closed');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>

</body>
</html>
