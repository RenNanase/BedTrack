@extends('layouts.app')

@section('content')
<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Add jQuery before the content -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            <div class="flex space-x-3">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-[#BB8858] text-white rounded-md hover:bg-[#D8A48F] transition-colors">
                    Admin Dashboard
                </a>
                @endif
            </div>
        </div>

        <!-- Main Dashboard Content with Activity Timeline -->
                <div class="flex flex-col md:flex-row gap-6 mb-8">
                    <!-- Main Dashboard Content -->
                    <div class="w-full md:w-2/3">
                        <!-- Activity Timeline -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-4 border-b border-gray-200 bg-primary/5">
                                <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                            </div>
                            <div class="p-4">
                                @if(count($activityLogs) > 0)
                                <div class="relative" id="activity-container">
                                    <!-- Activity logs will be loaded here -->
                                    @include('partials.activity-logs', ['activityLogs' => $activityLogs])

                                    @if($hasMoreLogs)
                                    <div class="text-center mt-6">
                                        <button id="load-more-btn"
                                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors"
                                            data-offset="5">
                                            Load More
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p>No activity logs found.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Legend Section -->
                            <div class="w-full md:w-1/3 shadow-md overflow-hidden mb-8 md:top-4 md:self-start md:ml-auto">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    <div class="p-4 border-b border-gray-200 bg-primary/5 cursor-pointer flex justify-between items-center"
                                        onclick="toggleLegend()">
                                        <h2 class="text-lg font-semibold text-gray-900">Legend</h2>
                                        <svg id="legendToggleIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 12.707a1 1 0 010-1.414L9.586 7l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div id="legendContent" class="p-4 hidden">
                                        <div class="space-y-4">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-700 mb-2">Gender</h3>
                                                <div class="space-y-2">
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 flex items-center justify-center text-blue-600 font-bold mr-2">♂</span>
                                                        <span class="text-sm">Male</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 flex items-center justify-center text-pink-600 font-bold mr-2">♀</span>
                                                        <span class="text-sm">Female</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-700 mb-2">Patient Category</h3>
                                                <div class="space-y-2">
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 flex items-center justify-center text-gray-600 mr-2">👨</span>
                                                        <span class="text-sm">Adult</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 flex items-center justify-center text-gray-600 mr-2">👶</span>
                                                        <span class="text-sm">Paediatric</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-700 mb-2">Hazard</h3>
                                                <div class="space-y-2">
                                                    <div class="flex items-center">
                                                        <span class="w-6 h-6 flex items-center justify-center text-red-600 mr-2">
                                                            ☠️
                                                        </span>
                                                        <span class="text-sm">Patient/Bed Hazard</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>

                <!-- Bed Status Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 mb-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-900">Total Beds</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $totalBeds }}</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-green-50">
                    <h3 class="text-sm font-medium text-gray-900">Available</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $bedCounts['available'] }}</div>
                        <div class="text-xs text-gray-500">Available</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-yellow-50">
                    <h3 class="text-sm font-medium text-gray-900">Booked</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $bedCounts['booked'] }}</div>
                        <div class="text-xs text-gray-500">Booked</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-red-50">
                    <h3 class="text-sm font-medium text-gray-900">Occupied</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $bedCounts['occupied'] }}</div>
                        <div class="text-xs text-gray-500">Occupied</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-sm font-medium text-gray-900">Discharges</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $todayDischarges }}</div>
                        <div class="text-xs text-gray-500">Today's</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-pink-50">
                    <h3 class="text-sm font-medium text-gray-900">Housekeeping</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $bedCounts['housekeeping'] }}</div>
                        <div class="text-xs text-gray-500">Cleaning</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-purple-50">
                    <h3 class="text-sm font-medium text-gray-900">Transfer In</h3>
                </div>
                <div class="p-3 flex items-center">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $bedCounts['transfer_in'] }}</div>
                        <div class="text-xs text-gray-500">Transfer In</div>
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
                                    Category
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
                                    <div class="flex items-center">
                                        <span class="mr-2">{{ $discharge->patient_name }}</span>
                                        @if($discharge->gender)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $discharge->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold text-xs">
                                                {{ $discharge->gender == 'Male' ? '♂' : '♀' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($discharge->patient_category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $discharge->patient_category == 'Adult' ? '👨 Adult' : '👶 Paediatric' }}
                                        </span>
                                    @endif
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

        <!-- Recent Transfers -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Transfer Ins -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Transfer Ins</h3>
                    <a href="{{ route('transfers.index', ['type' => 'in']) }}" class="text-sm text-primary hover:text-primary-dark">
                        View All
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentTransfers['transfer_ins'] as $transfer)
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $transfer->patient_name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        From: {{ $transfer->sourceBed->room->ward->ward_name }} -
                                        {{ $transfer->sourceBed->room->room_name }} -
                                        Bed {{ $transfer->sourceBed->bed_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        To: {{ $transfer->destinationBed->room->ward->ward_name }} -
                                        {{ $transfer->destinationBed->room->room_name }} -
                                        Bed {{ $transfer->destinationBed->bed_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $transfer->patient_category }} | {{ $transfer->gender }} | MRN: {{ $transfer->mrn }} | {{ $transfer->sourceBed->has_hazard ? '💀 Hazard' : '' }}
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $transfer->transferred_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent transfer ins</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transfer Outs -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Transfer Outs</h3>
                    <a href="{{ route('transfers.index', ['type' => 'out']) }}" class="text-sm text-primary hover:text-primary-dark">
                        View All
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentTransfers['transfer_outs'] as $transfer)
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $transfer->patient_name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        From: {{ $transfer->sourceBed->room->ward->ward_name }} -
                                        {{ $transfer->sourceBed->room->room_name }} -
                                        Bed {{ $transfer->sourceBed->bed_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        To: {{ $transfer->destinationBed->room->ward->ward_name }} -
                                        {{ $transfer->destinationBed->room->room_name }} -
                                        Bed {{ $transfer->destinationBed->bed_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $transfer->patient_category }} | {{ $transfer->gender }} | MRN: {{ $transfer->mrn }} | {{ $transfer->sourceBed->has_hazard ? '☠️ Hazard' : '' }}
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $transfer->transferred_at->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent transfer outs</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Room and Bed Visualization -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 bg-primary/5">
                <h2 class="text-xl font-semibold text-gray-900">Room & Bed Layout</h2>
                <p class="text-sm text-gray-500">Visual overview of all rooms and beds in {{ $ward->ward_name }}</p>
            </div>

            <div class="p-6">
                @if($ward->ward_name === 'Maternity Ward')
                    <!-- Maternity Ward Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($rooms as $room)
                        <div class="border rounded-lg overflow-hidden shadow-sm" data-room-id="{{ $room->id }}">
                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                <h3 class="font-medium text-gray-700">{{ $room->room_name }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                                    Capacity: {{ $room->capacity }} Beds + 2 Bassinets
                            </span>
                        </div>

                            @if($room->is_blocked)
                            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            <strong>Room Blocked</strong> - {{ $room->block_remarks }}
                                            <br>
                                            <small class="text-red-600">Blocked by {{ $room->blockedByUser->name }} on {{ $room->blocked_at->format('M d, Y h:i A') }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                        <div class="p-4">
                                <!-- Beds Section -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Beds</h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        @foreach($room->beds->sortBy('bed_number') as $bed)
                                        <a href="{{ route('beds.show', $bed) }}" class="block border rounded-md overflow-hidden transition-transform hover:scale-105 hover:shadow-md {{
                                            $bed->status == 'Available' ? 'border-green-300 bg-green-50' :
                                            ($bed->status == 'Booked' ? 'border-yellow-300 bg-yellow-50' :
                                            ($bed->status == 'Discharged' ? 'border-blue-300 bg-blue-50' :
                                            ($bed->status == 'Housekeeping' ? 'border-pink-300 bg-pink-50' :
                                            ($bed->status == 'Transfer-out' ? 'border-orange-300 bg-orange-50' :
                                            ($bed->status == 'Transfer-in' ? 'border-purple-300 bg-purple-50' :
                                    'border-red-300 bg-red-50'))))) }}">
                                    <div class="px-3 py-2 border-b {{
                                                $bed->status == 'Available' ? 'bg-green-100 text-green-800' :
                                                ($bed->status == 'Booked' ? 'bg-yellow-100 text-yellow-800' :
                                                ($bed->status == 'Discharged' ? 'bg-blue-100 text-blue-800' :
                                                ($bed->status == 'Housekeeping' ? 'bg-pink-100 text-pink-800' :
                                                ($bed->status == 'Transfer-out' ? 'bg-orange-100 text-orange-800' :
                                                ($bed->status == 'Transfer-in' ? 'bg-purple-100 text-purple-800' :
                                        'bg-red-100 text-red-800'))))) }}">
                                        <div class="flex justify-between items-center">
                                                    <span class="font-medium">Bed {{ $bed->bed_number }}</span>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full {{
                                                        $bed->status == 'Available' ? 'bg-green-200 text-green-900' :
                                                        ($bed->status == 'Booked' ? 'bg-yellow-200 text-yellow-900' :
                                                        ($bed->status == 'Discharged' ? 'bg-blue-200 text-blue-900' :
                                                        ($bed->status == 'Housekeeping' ? 'bg-pink-200 text-pink-900' :
                                                        ($bed->status == 'Transfer-out' ? 'bg-orange-200 text-orange-900' :
                                                        ($bed->status == 'Transfer-in' ? 'bg-purple-200 text-purple-900' :
                                                'bg-red-200 text-red-900'))))) }}">
                                                        {{ $bed->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                                @if($bed->status != 'Available')
                                            <div class="flex justify-between items-start">
                                                        <div class="space-y-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-sm font-medium text-gray-700">{{ $bed->patient_name }}</span>
                                                                @if($bed->gender)
                                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $bed->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold text-xs">
                                                                        {{ $bed->gender == 'Male' ? '♂' : '♀' }}
                                                        </span>
                                                    @endif
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($bed->patient_category)
                                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-700">
                                                                    {{ $bed->patient_category == 'Adult' ? '👨' : '👶' }}
                                                                </span>
                                                            @endif
                                                            @if($bed->has_hazard)
                                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700" title="{{ $bed->hazard_notes }}">
                                                                    ☠️
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($bed->occupied_at)
                                                            <div class="text-sm text-gray-600 italic">
                                                                Occupied: {{ $bed->occupied_at->format('M d, Y h:i A') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if($bed->status == 'Housekeeping')
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-100 text-pink-700">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">
                                                        No patient
                                                        @if($bed->has_hazard)
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700 float-right" title="{{ $bed->hazard_notes }}">
                                                            ☠️
                                                        </span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>

                                <!-- Bassinets Section -->
                                <div class="mt-6 pt-6 border-t">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Bassinets</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($room->bassinets as $bassinet)
                                        <div class="block border rounded-md overflow-hidden transition-transform hover:scale-105 hover:shadow-md {{
                                            $bassinet->status === 'Available' ? 'border-green-300 bg-green-50' :
                                            ($bassinet->status === 'Occupied' ? 'border-blue-300 bg-blue-50' :
                                            ($bassinet->status === 'Transfer-out' ? 'border-yellow-300 bg-yellow-50' :
                                            ($bassinet->status === 'Transfer-in' ? 'border-purple-300 bg-purple-50' :
                                            'border-gray-300 bg-gray-50')))
                                        }}">
                                            <div class="px-3 py-2 border-b {{
                                                $bassinet->status === 'Available' ? 'bg-green-100 text-green-800' :
                                                ($bassinet->status === 'Occupied' ? 'bg-blue-100 text-blue-800' :
                                                ($bassinet->status === 'Transfer-out' ? 'bg-yellow-100 text-yellow-800' :
                                                ($bassinet->status === 'Transfer-in' ? 'bg-purple-100 text-purple-800' :
                                                'bg-gray-100 text-gray-800')))
                                            }}">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium">Bassinet {{ $bassinet->bassinet_number }}</span>
                                                    <span class="text-xs px-1.5 py-0.5 rounded-full {{
                                                        $bassinet->status === 'Available' ? 'bg-green-200 text-green-900' :
                                                        ($bassinet->status === 'Occupied' ? 'bg-blue-200 text-blue-900' :
                                                        ($bassinet->status === 'Transfer-out' ? 'bg-yellow-200 text-yellow-900' :
                                                        ($bassinet->status === 'Transfer-in' ? 'bg-purple-200 text-purple-900' :
                                                        'bg-gray-200 text-gray-900')))
                                                    }}">
                                                        {{ $bassinet->status }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                @if($bassinet->status === 'Occupied')
                                                    <div class="flex justify-between items-start">
                                                        <div class="space-y-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-sm font-medium text-gray-700">{{ $bassinet->patient_name }}</span>
                                                                @if($bassinet->gender)
                                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $bassinet->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold text-xs">
                                                                        {{ $bassinet->gender == 'Male' ? '♂' : '♀' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-sm text-gray-600">Mother: {{ $bassinet->mother_name }}</span>
                                                                @if($bassinet->mother_mrn)
                                                                    <span class="text-xs text-gray-500">(MRN: {{ $bassinet->mother_mrn }})</span>
                                                                @endif
                                                            </div>
                                                            @if($bassinet->occupied_at)
                                                                <p class="text-xs text-gray-500">
                                                                    Occupied: {{ $bassinet->occupied_at->format('M d, Y h:i A') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="flex space-x-2">
                                                            <button type="button" class="transfer-bassinet px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors" 
                                                                    data-bassinet-id="{{ $bassinet->id }}">
                                                                Transfer
                                                            </button>
                                                            <button type="button" class="discharge-bassinet px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors" 
                                                                    data-bassinet-id="{{ $bassinet->id }}">
                                                                Discharge
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex justify-between items-center">
                                                        <p class="text-sm text-gray-500 italic">Available</p>
                                                        <button type="button" class="register-baby px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors" 
                                                                data-bassinet-id="{{ $bassinet->id }}">
                                                            Register Baby
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Regular Ward Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($rooms as $room)
                        <div class="border rounded-lg overflow-hidden shadow-sm" data-room-id="{{ $room->id }}">
                            <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                <h3 class="font-medium text-gray-700">{{ $room->room_name }}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                                    Capacity: {{ $room->capacity }}
                                </span>
                                @if(auth()->user()->role === 'superadmin')
                                <div class="cursor-move">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @endif
                            </div>

                            @if($room->is_blocked)
                            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            <strong>Room Blocked</strong> - {{ $room->block_remarks }}
                                            <br>
                                            <small class="text-red-600">Blocked by {{ $room->blockedByUser->name }} on {{ $room->blocked_at->format('M d, Y h:i A') }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(auth()->user()->wards->contains($room->ward_id) || auth()->user()->isAdmin)
                            <div class="mt-4 flex justify-end space-x-2 px-4">
                                @if(!$room->is_blocked)
                                <button onclick="showBlockRoomModal('{{ $room->id }}')" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm">
                                    Block Room
                                </button>
                                @else
                                <form action="{{ route('rooms.unblock', $room) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
                                        Unblock Room
                                    </button>
                                </form>
                                @endif
                            </div>
                            @endif

                            <div class="p-4">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    @foreach($room->beds->sortBy('bed_number') as $bed)
                                    <a href="{{ route('beds.show', $bed) }}" class="block border rounded-md overflow-hidden transition-transform hover:scale-105 hover:shadow-md {{
                                        $bed->status == 'Available' ? 'border-green-300 bg-green-50' :
                                        ($bed->status == 'Booked' ? 'border-yellow-300 bg-yellow-50' :
                                        ($bed->status == 'Discharged' ? 'border-blue-300 bg-blue-50' :
                                        ($bed->status == 'Housekeeping' ? 'border-pink-300 bg-pink-50' :
                                        ($bed->status == 'Transfer-out' ? 'border-orange-300 bg-orange-50' :
                                        ($bed->status == 'Transfer-in' ? 'border-purple-300 bg-purple-50' :
                                        'border-red-300 bg-red-50'))))) }}">
                                        <div class="px-3 py-2 border-b {{
                                            $bed->status == 'Available' ? 'bg-green-100 text-green-800' :
                                            ($bed->status == 'Booked' ? 'bg-yellow-100 text-yellow-800' :
                                            ($bed->status == 'Discharged' ? 'bg-blue-100 text-blue-800' :
                                            ($bed->status == 'Housekeeping' ? 'bg-pink-100 text-pink-800' :
                                            ($bed->status == 'Transfer-out' ? 'bg-orange-100 text-orange-800' :
                                            ($bed->status == 'Transfer-in' ? 'bg-purple-100 text-purple-800' :
                                            'bg-red-100 text-red-800'))))) }}">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium">Bed {{ $bed->bed_number }}</span>
                                                <span class="text-xs px-1.5 py-0.5 rounded-full {{
                                                    $bed->status == 'Available' ? 'bg-green-200 text-green-900' :
                                                    ($bed->status == 'Booked' ? 'bg-yellow-200 text-yellow-900' :
                                                    ($bed->status == 'Discharged' ? 'bg-blue-200 text-blue-900' :
                                                    ($bed->status == 'Housekeeping' ? 'bg-pink-200 text-pink-900' :
                                                    ($bed->status == 'Transfer-out' ? 'bg-orange-200 text-orange-900' :
                                                    ($bed->status == 'Transfer-in' ? 'bg-purple-200 text-purple-900' :
                                                    'bg-red-200 text-red-900'))))) }}">
                                                    {{ $bed->status }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <div class="flex justify-between items-start">
                                                <div class="space-y-2">
                                                    @if($bed->status != 'Available')
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-sm font-medium text-gray-700">{{ $bed->patient_name }}</span>
                                                            @if($bed->gender)
                                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $bed->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold text-xs">
                                                                    {{ $bed->gender == 'Male' ? '♂' : '♀' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($bed->patient_category)
                                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-700">
                                                                    {{ $bed->patient_category == 'Adult' ? '👨' : '👶' }}
                                                                </span>
                                                            @endif
                                                            @if($bed->has_hazard)
                                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700" title="{{ $bed->hazard_notes }}">
                                                                    ☠️
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($bed->occupied_at)
                                                            <div class="text-sm text-gray-600 italic">
                                                                Occupied: {{ $bed->occupied_at->format('M d, Y h:i A') }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <p class="text-sm text-gray-500 italic">
                                                            No patient
                                                        </p>
                                                    @endif
                                                </div>
                                                @if($bed->status == 'Housekeeping')
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-100 text-pink-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Block Room Modal -->
        <div id="blockRoomModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Block Room</h3>
                    <form id="blockRoomForm" method="POST" class="mt-4">
                        @csrf
                        <div class="mt-2">
                            <label for="block_remarks" class="block text-sm font-medium text-gray-700">Reason for Blocking</label>
                            <textarea id="block_remarks" name="block_remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required></textarea>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="button" onclick="hideBlockRoomModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Block Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transfer Modal FOR BABY IN THE BASSINET -->
        <div id="transferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-[#EFEBCE]">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Transfer Baby</h3>
                        <button onclick="hideTransferModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="transferForm" class="mt-4 space-y-4">
                        <input type="hidden" id="transferBassinetId" name="bassinet_id">
                        <div class="space-y-2">
                            <label for="destinationWard" class="block text-sm font-medium text-gray-700">Destination Ward</label>
                            <select id="destinationWard" name="destination_ward_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                    required>
                                <option value="">Select Ward</option>
                                @foreach($wards as $ward)
                                    @if($ward->ward_name === 'Nursery Ward' || $ward->ward_name === 'Nursery')
                                        <option value="{{ $ward->id }}">{{ $ward->ward_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="destinationRoom" class="block text-sm font-medium text-gray-700">Destination Room</label>
                            <select id="destinationRoom" name="destination_room_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                    required>
                                <option value="">Select Room</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="destinationCrib" class="block text-sm font-medium text-gray-700">Available Cribs</label>
                            <select id="destinationCrib" name="destination_crib_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                    required>
                                <option value="">Select Crib</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="hideTransferModal()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-[#BB8858] text-white rounded-md hover:bg-[#D8A48F] transition-colors">
                                Transfer Baby
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Register Baby Modal -->
        <div id="registerBabyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-[#EFEBCE]">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Register New Baby</h3>
                        <button onclick="hideRegisterBabyModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="registerBabyForm" class="mt-4 space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label for="baby_name" class="block text-sm font-medium text-gray-700">Baby's Name</label>
                            <input type="text" id="baby_name" name="baby_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                   placeholder="Enter baby's name" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="baby_gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select id="baby_gender" name="baby_gender" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                    required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="baby_mrn" class="block text-sm font-medium text-gray-700">Baby's MRN</label>
                            <input type="text" id="baby_mrn" name="baby_mrn" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                   placeholder="Enter baby's MRN" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="mother_name" class="block text-sm font-medium text-gray-700">Mother's Name</label>
                            <input type="text" id="mother_name" name="mother_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                   placeholder="Enter mother's name" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="mother_mrn" class="block text-sm font-medium text-gray-700">Mother's MRN</label>
                            <input type="text" id="mother_mrn" name="mother_mrn" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                   placeholder="Enter mother's MRN" required>
                        </div>

                        <div class="space-y-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#BB8858] focus:border-[#BB8858]"
                                      placeholder="Enter any additional notes"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="hideRegisterBabyModal()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-[#BB8858] text-white rounded-md hover:bg-[#D8A48F] transition-colors">
                                Register Baby
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Discharge Confirmation Modal -->
        <div id="dischargeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-[#EFEBCE]">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Confirm Discharge</h3>
                        <button onclick="hideDischargeModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2">
                        <p class="text-gray-700">Are you sure you want to discharge this baby?</p>
                        <p class="text-sm text-gray-500 mt-2">This action cannot be undone.</p>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="hideDischargeModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmDischarge()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Discharge Baby
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleLegend() {
                const legendContent = document.getElementById('legendContent');
                const legendToggleIcon = document.getElementById('legendToggleIcon');

                if (legendContent.classList.contains('hidden')) {
                    legendContent.classList.remove('hidden');
                    legendToggleIcon.innerHTML = `
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                `;
                } else {
                    legendContent.classList.add('hidden');
                    legendToggleIcon.innerHTML = `
                        <path fill-rule="evenodd" d="M5.293 12.707a1 1 0 010-1.414L9.586 7l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                `;
                }
            }

            function showBlockRoomModal(roomId) {
                const modal = document.getElementById('blockRoomModal');
                const form = document.getElementById('blockRoomForm');
                form.action = `/BedTrack/public/rooms/${roomId}/block`;
                modal.classList.remove('hidden');
                // Clear any previous input
                document.getElementById('block_remarks').value = '';
            }

            function hideBlockRoomModal() {
                const modal = document.getElementById('blockRoomModal');
                modal.classList.add('hidden');
            }

            // Close modal when clicking outside
            document.getElementById('blockRoomModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideBlockRoomModal();
                }
            });

            // Handle form submission
            document.getElementById('blockRoomForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const formData = new FormData(form);
                const blockRemarks = document.getElementById('block_remarks').value.trim();
                
                if (!blockRemarks) {
                    alert('Please enter a reason for blocking the room.');
                    return;
                }

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 404) {
                            throw new Error('Room not found');
                        }
                        return response.json().then(data => {
                            throw new Error(data.error || 'Failed to block room');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Failed to block room');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Failed to block room. Please try again.');
                });
            });

            // Load more activity logs
            document.addEventListener('DOMContentLoaded', function() {
                const loadMoreBtn = document.getElementById('load-more-btn');
                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function() {
                        const offset = parseInt(this.getAttribute('data-offset'));
                        const limit = 5; // Number of logs to load each time

                        // Show loading state
                        this.innerHTML = 'Loading...';
                        this.disabled = true;

                        // Make AJAX request to load more logs
                        fetch(`/BedTrack/public/activity-logs/load-more?offset=${offset}&limit=${limit}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Append new logs to the container
                            const activityContainer = document.getElementById('activity-container');
                            activityContainer.insertAdjacentHTML('beforeend', data.html);

                            // Update button state
                            this.innerHTML = 'Load More';
                            this.disabled = false;

                            // Update offset for next request
                            this.setAttribute('data-offset', offset + limit);

                            // Hide button if no more logs
                            if (!data.hasMore) {
                                this.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error loading more logs:', error);
                            this.innerHTML = 'Error loading more logs. Try again.';
                            this.disabled = false;
                        });
                    });
                }
            });

            @if(auth()->user()->role === 'superadmin')
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const roomContainer = document.querySelector('.grid');
                    if (roomContainer) {
                        new Sortable(roomContainer, {
                            animation: 150,
                            handle: '.cursor-move',
                            onEnd: function(evt) {
                                const roomIds = Array.from(roomContainer.children).map((item, index) => ({
                                    id: item.dataset.roomId,
                                    sequence: index
                                }));
                                
                                fetch('{{ route("super-admin.update-room-sequence") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ rooms: roomIds })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Show success message
                                        const successMessage = document.createElement('div');
                                        successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg';
                                        successMessage.textContent = 'Room order updated successfully';
                                        document.body.appendChild(successMessage);
                                        setTimeout(() => successMessage.remove(), 3000);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    // Show error message
                                    const errorMessage = document.createElement('div');
                                    errorMessage.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg';
                                    errorMessage.textContent = 'Failed to update room order';
                                    document.body.appendChild(errorMessage);
                                    setTimeout(() => errorMessage.remove(), 3000);
                                });
                            }
                        });
                    }
                });
            </script>
            @endif

            // Handle transfer button click
            $(document).on('click', '.transfer-bassinet', function(e) {
                e.preventDefault();
                const bassinetId = $(this).data('bassinet-id');
                $('#transferBassinetId').val(bassinetId);
                
                // Reset the form
                $('#destinationWard').val('');
                $('#destinationRoom').empty().append('<option value="">Select Room</option>');
                $('#destinationCrib').empty().append('<option value="">Select Crib</option>');
                
                // Show the modal
                showTransferModal();
            });

            function showTransferModal() {
                const modal = document.getElementById('transferModal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.style.opacity = '1';
                }, 10);
            }

            function hideTransferModal() {
                const modal = document.getElementById('transferModal');
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Load rooms when ward is selected
            $('#destinationWard').change(function() {
                const wardId = $(this).val();
                const roomSelect = $('#destinationRoom');
                const cribSelect = $('#destinationCrib');
                
                // Clear room and crib selections
                roomSelect.empty().append('<option value="">Select Room</option>');
                cribSelect.empty().append('<option value="">Select Crib</option>');
                
                if (wardId) {
                    console.log('Loading rooms for ward:', wardId);
                    // Fetch rooms for the selected ward
                    $.ajax({
                        url: `/BedTrack/public/api/wards/${wardId}/rooms`,
                        type: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(rooms) {
                            console.log('Rooms loaded:', rooms);
                            roomSelect.empty().append('<option value="">Select Room</option>');
                            if (rooms.length === 0) {
                                roomSelect.append('<option value="" disabled>No rooms available</option>');
                            } else {
                                rooms.forEach(room => {
                                    roomSelect.append(`<option value="${room.id}">${room.room_name}</option>`);
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading rooms:', xhr);
                            showAlert('error', 'Failed to load rooms: ' + (xhr.responseJSON?.error || 'Unknown error'));
                        }
                    });
                }
            });

            // Load cribs when room is selected
            $('#destinationRoom').change(function() {
                const roomId = $(this).val();
                const cribSelect = $('#destinationCrib');
                
                // Clear crib selection
                cribSelect.empty().append('<option value="">Select Crib</option>');
                
                if (roomId) {
                    console.log('Loading cribs for room:', roomId);
                    // Fetch available cribs for the selected room
                    $.ajax({
                        url: `/BedTrack/public/api/rooms/${roomId}/available-cribs`,
                        type: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(cribs) {
                            console.log('Cribs loaded:', cribs);
                            cribSelect.empty().append('<option value="">Select Crib</option>');
                            if (cribs.length === 0) {
                                cribSelect.append('<option value="" disabled>No available cribs</option>');
                            } else {
                                cribs.forEach(crib => {
                                    cribSelect.append(`<option value="${crib.id}">${crib.bed_number}</option>`);
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading cribs:', xhr);
                            showAlert('error', 'Failed to load cribs: ' + (xhr.responseJSON?.error || 'Unknown error'));
                        }
                    });
                }
            });

            // Handle transfer form submission
            $('#transferForm').submit(function(e) {
                e.preventDefault();
                
                const bassinetId = $('#transferBassinetId').val();
                const formData = new FormData(this);
                
                $.ajax({
                    url: `/BedTrack/public/bassinets/${bassinetId}/transfer`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            hideTransferModal();
                            showAlert('success', response.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Transfer error:', xhr);
                        showAlert('error', xhr.responseJSON?.message || 'Failed to transfer baby');
                    }
                });
            });

            // Close modal when clicking outside
            document.getElementById('transferModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideTransferModal();
                }
            });

            // Handle register baby button click
            $(document).on('click', '.register-baby', function(e) {
                e.preventDefault();
                const bassinetId = $(this).data('bassinet-id');
                $('#registerBabyForm').data('bassinet-id', bassinetId);
                
                // Reset the form
                $('#registerBabyForm')[0].reset();
                
                // Show the modal
                showRegisterBabyModal();
            });

            function showRegisterBabyModal() {
                const modal = document.getElementById('registerBabyModal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.style.opacity = '1';
                }, 10);
            }

            function hideRegisterBabyModal() {
                const modal = document.getElementById('registerBabyModal');
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Handle register baby form submission
            $('#registerBabyForm').submit(function(e) {
                e.preventDefault();
                
                const bassinetId = $(this).data('bassinet-id');
                const formData = new FormData(this);
                
                $.ajax({
                    url: `/BedTrack/public/bassinets/${bassinetId}/register`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            hideRegisterBabyModal();
                            showAlert('success', response.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Registration error:', xhr);
                        showAlert('error', xhr.responseJSON?.message || 'Failed to register baby');
                    }
                });
            });

            // Close modal when clicking outside
            document.getElementById('registerBabyModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideRegisterBabyModal();
                }
            });

            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white`;
                alertDiv.textContent = message;
                document.body.appendChild(alertDiv);
                setTimeout(() => alertDiv.remove(), 3000);
            }

            // Discharge modal functions
            let currentBassinetId = null;

            function showDischargeModal(bassinetId) {
                currentBassinetId = bassinetId;
                const modal = document.getElementById('dischargeModal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.style.opacity = '1';
                }, 10);
            }

            function hideDischargeModal() {
                const modal = document.getElementById('dischargeModal');
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.classList.add('hidden');
                    currentBassinetId = null;
                }, 300);
            }

            function confirmDischarge() {
                if (!currentBassinetId) return;

                $.ajax({
                    url: `/BedTrack/public/bassinets/${currentBassinetId}/discharge`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            hideDischargeModal();
                            showAlert('success', response.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Discharge error:', xhr);
                        showAlert('error', xhr.responseJSON?.message || 'Failed to discharge baby');
                    }
                });
            }

            // Handle discharge bassinet button click
            $(document).on('click', '.discharge-bassinet', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const bassinetId = $(this).data('bassinet-id');
                showDischargeModal(bassinetId);
            });

            // Close modal when clicking outside
            document.getElementById('dischargeModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDischargeModal();
                }
            });

            // Remove any duplicate event handlers
            $(document).off('click', '.discharge-bassinet');
            $(document).on('click', '.discharge-bassinet', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const bassinetId = $(this).data('bassinet-id');
                showDischargeModal(bassinetId);
            });
        </script>
@endsection

