<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Ward - {{ config('app.name', 'BedTrack') }}</title>

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
<body class="bg-secondary min-h-screen font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="px-8 pt-8 pb-6">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">BedTrack</h1>
                    <p class="text-gray-500 mt-2">Select your working ward</p>
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

                <form method="POST" action="{{ route('select.ward') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Select Ward</label>
                        <select id="ward_id" name="ward_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select a ward...</option>
                            @foreach($wards as $ward)
                                <option value="{{ $ward->id }}">{{ $ward->ward_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Continue
                        </button>
                    </div>
                </form>
            </div>

            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-center">
                <p class="text-xs text-gray-500">© {{ date('Y') }} BedTrack. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
