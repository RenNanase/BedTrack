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
                        <div class="text-lg font-bold text-gray-900">{{ $bedCounts['transfer_in'] ?? 0 }}</div>
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
                @if($ward->ward_name === 'Nursery Ward')
                    <!-- Nursery Room Layout -->
                    <div class="border rounded-lg overflow-hidden shadow-sm">
                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                            <h3 class="font-medium text-gray-700">{{ $ward->rooms->first()->room_name }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                                Capacity: {{ $ward->rooms->first()->capacity }} Cribs
                            </span>
                        </div>

                        <div class="p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($ward->rooms->first()->beds->where('is_crib', true) as $crib)
                                <a href="{{ route('beds.show', $crib) }}" class="block border rounded-md overflow-hidden transition-transform hover:scale-105 hover:shadow-md {{
                                    $crib->status == 'Available' ? 'border-green-300 bg-green-50' :
                                    ($crib->status == 'Booked' ? 'border-yellow-300 bg-yellow-50' :
                                    ($crib->status == 'Discharged' ? 'border-blue-300 bg-blue-50' :
                                    ($crib->status == 'Housekeeping' ? 'border-pink-300 bg-pink-50' :
                                    ($crib->status == 'Transfer-out' ? 'border-orange-300 bg-orange-50' :
                                    ($crib->status == 'Transfer-in' ? 'border-purple-300 bg-purple-50' :
                                    'border-red-300 bg-red-50'))))) }}">
                                    <div class="px-3 py-2 border-b {{
                                        $crib->status == 'Available' ? 'bg-green-100 text-green-800' :
                                        ($crib->status == 'Booked' ? 'bg-yellow-100 text-yellow-800' :
                                        ($crib->status == 'Discharged' ? 'bg-blue-100 text-blue-800' :
                                        ($crib->status == 'Housekeeping' ? 'bg-pink-100 text-pink-800' :
                                        ($crib->status == 'Transfer-out' ? 'bg-orange-100 text-orange-800' :
                                        ($crib->status == 'Transfer-in' ? 'bg-purple-100 text-purple-800' :
                                        'bg-red-100 text-red-800'))))) }}">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium">{{ $crib->bed_number }}</span>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full {{
                                                $crib->status == 'Available' ? 'bg-green-200 text-green-900' :
                                                ($crib->status == 'Booked' ? 'bg-yellow-200 text-yellow-900' :
                                                ($crib->status == 'Discharged' ? 'bg-blue-200 text-blue-900' :
                                                ($crib->status == 'Housekeeping' ? 'bg-pink-200 text-pink-900' :
                                                ($crib->status == 'Transfer-out' ? 'bg-orange-200 text-orange-900' :
                                                ($crib->status == 'Transfer-in' ? 'bg-purple-200 text-purple-900' :
                                                'bg-red-200 text-red-900'))))) }}">
                                                {{ $crib->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        @if($crib->status != 'Available')
                                            <div class="flex justify-between items-start">
                                                <p class="text-sm font-medium text-gray-700">{{ $crib->patient_name ?: 'Unknown' }}</p>
                                                <div class="flex items-center space-x-1">
                                                    @if($crib->has_hazard)
                                                    <span class="text-red-600 text-1xl" title="{{ $crib->hazard_notes }}">
                                                        ☠️
                                                    </span>
                                                    @endif
                                                    @if($crib->status == 'Housekeeping')
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-100 text-pink-700">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                    @if($crib->gender)
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $crib->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold">
                                                            {{ $crib->gender == 'Male' ? '♂' : '♀' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($crib->status_changed_at)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @if($crib->status == 'Housekeeping')
                                                        Started: {{ $crib->housekeeping_started_at->format('M d, h:i A') }}
                                                        <br>
                                                        <span class="text-pink-600">
                                                            @php
                                                                $minutesRemaining = Carbon\Carbon::now()->diffInMinutes($crib->housekeeping_started_at->addHours(2));
                                                                $hoursRemaining = floor($minutesRemaining / 60);
                                                                $minutesLeft = $minutesRemaining % 60;

                                                                if ($minutesRemaining <= 0) {
                                                                    $timeDisplay = 'Available soon';
                                                                } elseif ($hoursRemaining > 0) {
                                                                    $timeDisplay = $hoursRemaining . ' hour' . ($hoursRemaining > 1 ? 's' : '') . ' ' . $minutesLeft . ' minute' . ($minutesLeft > 1 || $minutesLeft == 0 ? 's' : '');
                                                                } else {
                                                                    $timeDisplay = $minutesLeft . ' minute' . ($minutesLeft > 1 ? 's' : '');
                                                                }
                                                            @endphp
                                                            Auto-available in {{ $timeDisplay }}
                                                        </span>
                                                    @else
                                                        {{ $crib->status == 'Discharged' ? 'Discharged' : 'Since' }}:
                                                        {{ $crib->status_changed_at->format('M d, h:i A') }}
                                                    @endif
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500 italic">
                                                No baby
                                                @if($crib->has_hazard)
                                                <span class="text-red-600 float-right text-1xl" title="{{ $crib->hazard_notes }}">
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
                    </div>
                @else
                    <!-- Regular Ward Layout -->
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
                                                <span class="font-medium">{{ $bed->bed_number }}</span>
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
                                                    <p class="text-sm font-medium text-gray-700">{{ $bed->patient_name ?: 'Unknown' }}</p>
                                                    <div class="flex items-center space-x-1">
                                                        @if($bed->has_hazard)
                                                        <span class="text-red-600 text-1xl" title="{{ $bed->hazard_notes }}">
                                                            ☠️
                                                        </span>
                                                        @endif
                                                        @if($bed->status == 'Housekeeping')
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-pink-100 text-pink-700">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </span>
                                                        @endif
                                                        @if($bed->gender)
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $bed->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold">
                                                                {{ $bed->gender == 'Male' ? '♂' : '♀' }}
                                                            </span>
                                                        @endif
                                                        @if($bed->patient_category)
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-700">
                                                                {{ $bed->patient_category == 'Adult' ? '👨' : '👶' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($bed->status_changed_at)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        @if($bed->status == 'Housekeeping')
                                                            Started: {{ $bed->housekeeping_started_at->format('M d, h:i A') }}
                                                            <br>
                                                            <span class="text-pink-600">
                                                                @php
                                                                    $minutesRemaining = Carbon\Carbon::now()->diffInMinutes($bed->housekeeping_started_at->addHours(2));
                                                                    $hoursRemaining = floor($minutesRemaining / 60);
                                                                    $minutesLeft = $minutesRemaining % 60;

                                                                    if ($minutesRemaining <= 0) {
                                                                        $timeDisplay = 'Available soon';
                                                                    } elseif ($hoursRemaining > 0) {
                                                                        $timeDisplay = $hoursRemaining . ' hour' . ($hoursRemaining > 1 ? 's' : '') . ' ' . $minutesLeft . ' minute' . ($minutesLeft > 1 || $minutesLeft == 0 ? 's' : '');
                                                                    } else {
                                                                        $timeDisplay = $minutesLeft . ' minute' . ($minutesLeft > 1 ? 's' : '');
                                                                    }
                                                                @endphp
                                                                Auto-available in {{ $timeDisplay }}
                                                            </span>
                                                        @else
                                                            {{ $bed->status == 'Discharged' ? 'Discharged' : 'Since' }}:
                                                            {{ $bed->status_changed_at->format('M d, h:i A') }}
                                                        @endif
                                                    </p>
                                                @endif
                                            @else
                                                <p class="text-sm text-gray-500 italic">
                                                    No patient
                                                    @if($bed->has_hazard)
                                                    <span class="text-red-600 float-right text-1xl" title="{{ $bed->hazard_notes }}">
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
                        </div>
                        @endforeach
                    </div>
                @endif
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
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            `;
        } else {
            legendContent.classList.add('hidden');
            legendToggleIcon.innerHTML = `
                <path fill-rule="evenodd" d="M5.293 12.707a1 1 0 010-1.414L9.586 7l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            `;
        }
    }

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
                fetch(`/activity-logs/load-more?offset=${offset}&limit=${limit}`, {
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
</script>
@endsection
