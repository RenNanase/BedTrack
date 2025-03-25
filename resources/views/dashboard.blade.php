@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Message -->
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $ward->ward_name }} Dashboard</h1>
                <p class="text-sm text-gray-500">Last updated: {{ $currentDateTime }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('select.ward') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                    Change Ward
                </a>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-200 bg-primary/5">
                <h2 class="text-xl font-semibold text-gray-900">Ward Summary</h2>
                <p class="text-sm text-gray-500">Overview of all beds in {{ $ward->ward_name }}</p>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between mb-6">
                    <div class="mb-4 md:mb-0">
                        <div class="text-4xl font-bold text-gray-800">{{ $totalBeds }}</div>
                        <div class="text-sm text-gray-500">Total Beds</div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        <div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Available</span>
                                <span class="text-sm font-medium text-green-600">{{ $percentages['available'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1.5 mb-1">
                                <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $percentages['available'] }}%"></div>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">{{ $bedCounts['available'] }}</div>
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Booked</span>
                                <span class="text-sm font-medium text-yellow-600">{{ $percentages['booked'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1.5 mb-1">
                                <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ $percentages['booked'] }}%"></div>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">{{ $bedCounts['booked'] }}</div>
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Occupied</span>
                                <span class="text-sm font-medium text-red-600">{{ $percentages['occupied'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1.5 mb-1">
                                <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $percentages['occupied'] }}%"></div>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">{{ $bedCounts['occupied'] }}</div>
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Discharged</span>
                                <span class="text-sm font-medium text-blue-600">{{ $percentages['discharged'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1.5 mb-1">
                                <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $percentages['discharged'] }}%"></div>
                            </div>
                            <div class="text-2xl font-bold text-gray-800">{{ $bedCounts['discharged'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bed Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-5 border-b border-gray-200 bg-green-50">
                    <h3 class="text-lg font-medium text-gray-900">Available Beds</h3>
                </div>
                <div class="p-5 flex items-center">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white text-xl mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $bedCounts['available'] }}</div>
                        <div class="text-sm text-gray-500">Beds available</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-5 border-b border-gray-200 bg-yellow-50">
                    <h3 class="text-lg font-medium text-gray-900">Booked Beds</h3>
                </div>
                <div class="p-5 flex items-center">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-white text-xl mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $bedCounts['booked'] }}</div>
                        <div class="text-sm text-gray-500">Beds booked</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-5 border-b border-gray-200 bg-red-50">
                    <h3 class="text-lg font-medium text-gray-900">Occupied Beds</h3>
                </div>
                <div class="p-5 flex items-center">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white text-xl mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $bedCounts['occupied'] }}</div>
                        <div class="text-sm text-gray-500">Beds occupied</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-5 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-lg font-medium text-gray-900">Today's Discharges</h3>
                </div>
                <div class="p-5 flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $todayDischarges }}</div>
                        <div class="text-sm text-gray-500">Patients discharged today</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Discharges Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-200 bg-blue-50">
                <h2 class="text-xl font-semibold text-gray-900">Recent Discharges</h2>
                <p class="text-sm text-gray-500">Recent patient discharges from {{ $ward->ward_name }}</p>
            </div>
            <div class="p-6">
                @if($recentDischarges->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bed
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Room
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Patient
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Discharged At
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentDischarges as $discharge)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('beds.show', $discharge->bed_id) }}" class="text-primary hover:text-primary-dark">
                                        Bed #{{ $discharge->bed->bed_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $discharge->room->room_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $discharge->patient_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $discharge->discharged_at->format('M d, Y - h:i A') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p>No recent discharges found in this ward.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Room and Bed Visualization -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 bg-primary/5">
                <h2 class="text-xl font-semibold text-gray-900">Room & Bed Layout</h2>
                <p class="text-sm text-gray-500">Visual overview of all rooms and beds in {{ $ward->ward_name }}</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($ward->rooms as $room)
                    <div class="border rounded-lg overflow-hidden shadow-sm">
                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                            <h3 class="font-medium text-gray-700">{{ $room->room_name }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                                Capacity: {{ $room->capacity }}
                            </span>
                        </div>

                        <div class="p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($room->beds as $bed)
                                <a href="{{ route('beds.show', $bed) }}" class="block border rounded-md overflow-hidden transition-transform hover:scale-105 hover:shadow-md {{ $bed->status == 'Available' ? 'border-green-300 bg-green-50' : ($bed->status == 'Booked' ? 'border-yellow-300 bg-yellow-50' : ($bed->status == 'Discharged' ? 'border-blue-300 bg-blue-50' : 'border-red-300 bg-red-50')) }}">
                                    <div class="px-3 py-2 border-b {{ $bed->status == 'Available' ? 'bg-green-100 text-green-800' : ($bed->status == 'Booked' ? 'bg-yellow-100 text-yellow-800' : ($bed->status == 'Discharged' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium">{{ $bed->bed_number }}</span>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full {{ $bed->status == 'Available' ? 'bg-green-200 text-green-900' : ($bed->status == 'Booked' ? 'bg-yellow-200 text-yellow-900' : ($bed->status == 'Discharged' ? 'bg-blue-200 text-blue-900' : 'bg-red-200 text-red-900')) }}">
                                                {{ $bed->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        @if($bed->status != 'Available')
                                            <p class="text-sm font-medium text-gray-700">{{ $bed->patient_name ?: 'Unknown' }}</p>
                                            @if($bed->status_changed_at)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $bed->status == 'Discharged' ? 'Discharged' : 'Since' }}:
                                                    {{ $bed->status_changed_at->format('M d, h:i A') }}
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500 italic">No patient</p>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
