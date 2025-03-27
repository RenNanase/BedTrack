@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Super Admin Dashboard</h1>
            <div class="text-sm text-gray-600">{{ now()->format('F j, Y, g:i a') }}</div>
        </div>

        <!-- Super Admin Actions -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Super Admin Controls</h2>
            <div class="flex space-x-4">
                <a href="{{ route('super-admin.ward-management') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3 16a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" />
                    </svg>
                    Ward Management
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Regular Dashboard
                </a>
            </div>
        </div>

        <!-- Wards Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Wards Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($wards as $ward)
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-lg mb-2">{{ $ward->ward_name }}</h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600">Total Rooms: {{ $ward->rooms->count() }}</p>
                            <p class="text-sm text-gray-600">Total Beds: {{ $ward->rooms->flatMap->beds->count() }}</p>
                            <p class="text-sm text-gray-600">Available Beds: {{ $ward->rooms->flatMap->beds->where('status', 'Available')->count() }}</p>
                            <p class="text-sm text-gray-600">Occupied Beds: {{ $ward->rooms->flatMap->beds->where('status', 'Occupied')->count() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
