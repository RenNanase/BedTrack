@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <div class="text-sm text-gray-600">{{ $currentDateTime }}</div>
        </div>

        <!-- Admin Actions -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Admin Controls</h2>
            <div class="flex space-x-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Regular Dashboard
                </a>
            </div>
        </div>

        <!-- Wards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($wardSummaries as $summary)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Ward Header -->
                <div class="bg-primary text-white p-4">
                    <h2 class="text-xl font-semibold">{{ $summary['ward']->ward_name }}</h2>
                </div>

                <!-- Ward Stats -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <div class="text-3xl font-bold text-blue-600">{{ $summary['total_beds'] }}</div>
                            <div class="text-sm text-blue-800">Total Beds</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">{{ $summary['available_beds'] }}</div>
                            <div class="text-sm text-green-800">Available</div>
                            <div class="text-xs text-green-600">{{ $summary['available_percentage'] }}%</div>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <div class="text-3xl font-bold text-yellow-600">{{ $summary['booked_beds'] }}</div>
                            <div class="text-sm text-yellow-800">Booked</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <div class="text-3xl font-bold text-red-600">{{ $summary['occupied_beds'] }}</div>
                            <div class="text-sm text-red-800">Occupied</div>
                            <div class="text-xs text-red-600">{{ $summary['occupied_percentage'] }}%</div>
                        </div>
                    </div>

                    <!-- Discharge Info -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Today's Discharges:</span>
                            <span class="font-medium text-primary">{{ $summary['today_discharges'] }}</span>
                        </div>
                    </div>

                    <!-- View Details Link -->
                    <div class="mt-4 pt-3 flex justify-end">
                        <form action="{{ route('select.ward') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="ward_id" value="{{ $summary['ward']->id }}">
                            <button type="submit" class="inline-flex items-center text-sm text-primary hover:text-primary/80">
                                View Details
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- No wards message -->
        @if(count($wardSummaries) === 0)
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-600">No wards available in the system.</p>
        </div>
        @endif
    </div>
</div>
@endsection
