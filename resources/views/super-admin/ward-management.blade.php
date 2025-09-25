@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Ward Management</h1>
                <p class="text-sm text-gray-500">Manage hospital wards, rooms, and beds</p>
            </div>
            <a href="{{ route('super-admin.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Add New Ward Form -->
        <div class="bg-white rounded-lg shadow-sm p-5 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Ward</h2>
            <form action="{{ route('super-admin.add-ward') }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <label for="ward_name" class="block text-sm font-medium text-gray-700 mb-1">Ward Name</label>
                        <input type="text" name="ward_name" id="ward_name" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Ward
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Wards Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($wards->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ward Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rooms</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beds</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($wards as $ward)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $ward->ward_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $ward->rooms->count() }} rooms
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $ward->rooms->sum(function($room) { return $room->beds->count(); }) }} beds
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" 
                                                    onclick="toggleWardDetails('ward-{{ $ward->id }}')"
                                                    class="flex items-center px-3 py-1 bg-cyan-600 text-white rounded-md hover:bg-cyan-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                                Manage
                                            </button>
                                            <form action="{{ route('super-admin.delete-ward', $ward) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center px-3 py-1 bg-red-700 text-white rounded-md hover:bg-red-600 transition-colors" 
                                                        onclick="return confirm('Are you sure you want to delete this ward? This will also delete all rooms and beds within this ward.')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Ward Details Section (Hidden by Default) -->
                                <tr id="ward-{{ $ward->id }}" class="hidden bg-gray-50">
                                    <td colspan="4" class="px-6 py-4">
                                        <div class="border-t border-gray-200 pt-4">
                                            <!-- Add New Room Form -->
                                            <div class="bg-white p-4 rounded-md shadow-sm mb-4">
                                                <h4 class="text-sm font-medium text-gray-700 mb-3">Add Room to {{ $ward->ward_name }}</h4>
                                                <form method="POST" action="{{ route('super-admin.add-room') }}">
                                                    @csrf
                                                    <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                                                    <div class="flex flex-col md:flex-row gap-4">
                                                        <div class="md:w-1/4">
                                                            <label for="room_name_{{ $ward->id }}" class="block text-xs font-medium text-gray-500 mb-1">Room Name</label>
                                                            <input type="text" name="room_name" id="room_name_{{ $ward->id }}" required
                                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3">
                                                        </div>
                                                        <div class="md:w-1/4">
                                                            <label for="room_type_{{ $ward->id }}" class="block text-xs font-medium text-gray-500 mb-1">Room Type</label>
                                                            <select name="room_type" id="room_type_{{ $ward->id }}" required
                                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3">
                                                                <option value="regular">Regular</option>
                                                                <option value="nursery">Nursery</option>
                                                            </select>
                                                        </div>
                                                        <div class="md:w-1/4">
                                                            <label for="capacity_{{ $ward->id }}" class="block text-xs font-medium text-gray-500 mb-1">Capacity</label>
                                                            <input type="number" name="capacity" id="capacity_{{ $ward->id }}" value="1" min="1" max="20" required
                                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3">
                                                        </div>
                                                        <div class="md:w-1/4 flex items-end">
                                                            <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors text-sm">
                                                                Add Room
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Rooms List -->
                                            @if($ward->rooms->count() > 0)
                                                <div class="bg-white rounded-md shadow-sm overflow-hidden">
                                                    <div class="p-4 border-b border-gray-200">
                                                        <h4 class="text-sm font-medium text-gray-700">Rooms in {{ $ward->ward_name }}</h4>
                                                        <p class="text-xs text-gray-500">Drag and drop to reorder rooms</p>
                                                    </div>
                                                    <ul id="rooms-list-{{ $ward->id }}" class="divide-y divide-gray-200">
                                                        @foreach($ward->rooms->sortBy('sequence') as $room)
                                                            <li class="p-4 hover:bg-gray-50 cursor-move" data-room-id="{{ $room->id }}">
                                                                <div class="flex items-center justify-between">
                                                                    <div class="flex items-center space-x-3">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                                                        </svg>
                                                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-xs font-medium">
                                                                            Room {{ $room->room_name ?? $room->room_number ?? 'Unknown' }}
                                                                        </span>
                                                                        <span class="text-xs text-gray-500 capitalize">{{ $room->room_type }}</span>
                                                                    </div>
                                                                    <div class="flex items-center space-x-2">
                                                                            <button type="button" 
                                                                                    onclick="toggleRoomDetails('room-{{ $room->id }}')"
                                                                                    class="flex items-center px-2 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors text-xs">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                                                </svg>
                                                                                Beds
                                                                            </button>
                                                                            <form action="{{ route('super-admin.delete-room', $room) }}" method="POST" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="flex items-center px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors text-xs"
                                                                                        onclick="return confirm('Are you sure you want to delete this room? This will also delete all beds within this room.')">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                                    </svg>
                                                                                    Delete
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                </div>
                                                                <!-- Room Details Section (Hidden by Default) -->
                                                                <div id="room-{{ $room->id }}" class="hidden mt-4">
                                                                    <div class="bg-white p-4 rounded-md shadow-sm">
                                                                        <h5 class="text-sm font-medium text-gray-700 mb-3">Beds in {{ $room->room_name }}</h5>
                                                                        @if($room->beds->count() > 0)
                                                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                                @foreach($room->beds as $bed)
                                                                                    <div class="border rounded-lg p-3">
                                                                                        <div class="flex justify-between items-center mb-2">
                                                                                            <span class="text-sm font-medium text-gray-700">Bed {{ $bed->bed_number }}</span>
                                                                                            <div class="flex items-center space-x-2">
                                                                                                <span class="px-2 py-1 text-xs rounded-full 
                                                                                                    @if($bed->status === 'Available') bg-green-100 text-green-800
                                                                                                    @elseif($bed->status === 'Occupied') bg-blue-100 text-blue-800
                                                                                                    @elseif($bed->status === 'Maintenance') bg-yellow-100 text-yellow-800
                                                                                                    @else bg-gray-100 text-gray-800 @endif">
                                                                                                    {{ $bed->status }}
                                                                                                </span>
                                                                                                <form action="{{ route('super-admin.delete-bed', $bed) }}" method="POST" class="inline">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this bed?')">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="text-xs text-gray-500">
                                                                                            Type: {{ ucfirst($bed->bed_type) }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <p class="text-sm text-gray-500">No beds added to this room yet.</p>
                                                                        @endif
                                                                        
                                                                        <!-- Add New Bed Form -->
                                                                        <div class="mt-4 pt-4 border-t">
                                                                            <h6 class="text-sm font-medium text-gray-700 mb-3">Add New Bed</h6>
                                                                            <form action="{{ route('super-admin.add-bed') }}" method="POST" class="space-y-3">
                                                                                @csrf
                                                                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                    <div>
                                                                                        <label for="bed_number_{{ $room->id }}" class="block text-xs font-medium text-gray-500 mb-1">Bed Number</label>
                                                                                        <input type="text" name="bed_number" id="bed_number_{{ $room->id }}" required
                                                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3 text-sm">
                                                                                    </div>
                                                                                    <div>
                                                                                        <label for="bed_type_{{ $room->id }}" class="block text-xs font-medium text-gray-500 mb-1">Bed Type</label>
                                                                                        <select name="bed_type" id="bed_type_{{ $room->id }}" required
                                                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3 text-sm">
                                                                                            <option value="regular">Regular</option>
                                                                                            <option value="crib">Crib</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex justify-end">
                                                                                    <button type="submit" class="px-3 py-1 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors text-xs">
                                                                                            Add Bed
                                                                                        </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>

                                                                        @if($room->ward->ward_name === 'Maternity Ward')
                                                                            <div class="mt-4">
                                                                                <h5 class="text-sm font-medium text-gray-700 mb-3">Bassinets in {{ $room->room_name }}</h5>
                                                                                @if($room->bassinets->count() > 0)
                                                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                                                                        @foreach($room->bassinets as $bassinet)
                                                                                            <div class="border rounded-md p-3 {{
                                                                                                $bassinet->status === 'Available' ? 'bg-green-50 border-green-200' :
                                                                                                ($bassinet->status === 'Occupied' ? 'bg-blue-50 border-blue-200' :
                                                                                                ($bassinet->status === 'Transfer-out' ? 'bg-yellow-50 border-yellow-200' :
                                                                                                ($bassinet->status === 'Transfer-in' ? 'bg-purple-50 border-purple-200' :
                                                                                                'bg-gray-50 border-gray-200')))
                                                                                            }}">
                                                                                            <div class="flex justify-between items-start">
                                                                                                <div>
                                                                                                        <h6 class="font-medium text-gray-900">Bassinet {{ $bassinet->bassinet_number }}</h6>
                                                                                                        <p class="text-sm text-gray-500">
                                                                                                            Status: <span class="font-medium">{{ $bassinet->status }}</span>
                                                                                                        </p>
                                                                                                        @if($bassinet->patient_name)
                                                                                                            <p class="text-sm text-gray-700 mt-1">
                                                                                                                Baby: {{ $bassinet->patient_name }}
                                                                                                                @if($bassinet->gender)
                                                                                                                    <span class="ml-1">({{ $bassinet->gender }})</span>
                                                                                                                @endif
                                                                                                            </p>
                                                                                                        @endif
                                                                                                </div>
                                                                                                    <form action="{{ route('super-admin.delete-bassinet', $bassinet) }}" method="POST" class="inline">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                        <button type="submit" class="text-red-600 hover:text-red-800" 
                                                                                                                onclick="return confirm('Are you sure you want to delete this bassinet?')">
                                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                                @else
                                                                                    <p class="text-sm text-gray-500">No bassinets added to this room yet.</p>
                                                                                @endif
                                                                                
                                                                                <!-- Add New Bassinet Form -->
                                                                                <div class="mt-4 pt-4 border-t">
                                                                                    <h6 class="text-sm font-medium text-gray-700 mb-3">Add New Bassinet</h6>
                                                                                    <form action="{{ route('super-admin.add-bassinet') }}" method="POST" class="space-y-3">
                                                                                        @csrf
                                                                                        <input type="hidden" name="room_id" value="{{ $room->id }}">
                                                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                                            <div>
                                                                                                <label for="bassinet_number_{{ $room->id }}" class="block text-xs font-medium text-gray-500 mb-1">Bassinet Number</label>
                                                                                                <input type="text" name="bassinet_number" id="bassinet_number_{{ $room->id }}" required
                                                                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary py-2 px-3 text-sm">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="flex justify-end">
                                                                                            <button type="submit" class="px-3 py-1 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors text-xs">
                                                                                                Add Bassinet
                                                                                            </button>
                                                                                        </div>
                                                                                    </form>
                                                                            </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <div class="text-center py-4 bg-white rounded-md shadow-sm">
                                                    <p class="text-gray-500">No rooms added to this ward yet</p>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No wards yet</h3>
                    <p class="text-gray-600 mb-4">Start by adding your first ward using the form above.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    // Store expanded states in sessionStorage
    function saveExpandedState(id) {
        const isExpanded = !document.getElementById(id).classList.contains('hidden');
        sessionStorage.setItem(id, isExpanded);
    }

    function toggleWardDetails(id) {
        const element = document.getElementById(id);
        if (element) {
            element.classList.toggle('hidden');
            saveExpandedState(id);
            
            if (!element.classList.contains('hidden')) {
                // Initialize Sortable for this ward's rooms
                const roomsList = document.getElementById(`rooms-list-${id.split('-')[1]}`);
                if (roomsList) {
                    new Sortable(roomsList, {
                        animation: 150,
                        onEnd: function(evt) {
                            const roomIds = Array.from(roomsList.children).map((item, index) => ({
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
                                    showMessage('Room order updated successfully', 'success');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showMessage('Failed to update room order', 'error');
                            });
                        }
                    });
                }
            }
        }
    }
    
    function toggleRoomDetails(id) {
        const element = document.getElementById(id);
        if (element) {
            element.classList.toggle('hidden');
            saveExpandedState(id);
        }
    }

    // Show message helper function
    function showMessage(message, type = 'success') {
        const messageElement = document.createElement('div');
        messageElement.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        messageElement.textContent = message;
        document.body.appendChild(messageElement);
        setTimeout(() => messageElement.remove(), 3000);
    }

    // Restore expanded states on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check for flash session data first (from redirects after form submissions)
        @if (session('expanded_ward'))
            const wardId = 'ward-{{ session('expanded_ward') }}';
            const wardElement = document.getElementById(wardId);
            if (wardElement) {
                wardElement.classList.remove('hidden');
            }
        @endif

        @if (session('expanded_room'))
            const roomId = 'room-{{ session('expanded_room') }}';
            const roomElement = document.getElementById(roomId);
            if (roomElement) {
                roomElement.classList.remove('hidden');
            }
        @endif

        // Restore ward expanded states from sessionStorage
        document.querySelectorAll('[id^="ward-"]').forEach(element => {
            const isExpanded = sessionStorage.getItem(element.id) === 'true';
            if (isExpanded) {
                element.classList.remove('hidden');
            }
        });

        // Restore room expanded states from sessionStorage
        document.querySelectorAll('[id^="room-"]').forEach(element => {
            const isExpanded = sessionStorage.getItem(element.id) === 'true';
            if (isExpanded) {
                element.classList.remove('hidden');
            }
        });
    });

    // Handle room form submission
    document.querySelectorAll('form[action="{{ route("super-admin.add-room") }}"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const wardId = formData.get('ward_id');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Keep the ward expanded
                    const wardId = `ward-${formData.get('ward_id')}`;
                    sessionStorage.setItem(wardId, 'true');
                    window.location.reload();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while adding the room', 'error');
            });
        });
    });

    // Handle bed form submission
    document.querySelectorAll('form[action="{{ route("super-admin.add-bed") }}"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const roomId = formData.get('room_id');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Keep both ward and room expanded
                    const room = document.querySelector(`[data-room-id="${roomId}"]`);
                    const wardId = room.closest('tr[id^="ward-"]').id;
                    const roomDetailsId = `room-${roomId}`;
                    sessionStorage.setItem(wardId, 'true');
                    sessionStorage.setItem(roomDetailsId, 'true');
                    window.location.reload();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while adding the bed', 'error');
            });
        });
    });

    // Handle bassinet form submission
    document.querySelectorAll('form[action="{{ route("super-admin.add-bassinet") }}"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const roomId = formData.get('room_id');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Keep both ward and room expanded
                    const room = document.querySelector(`[data-room-id="${roomId}"]`);
                    const wardId = room.closest('tr[id^="ward-"]').id;
                    const roomDetailsId = `room-${roomId}`;
                    sessionStorage.setItem(wardId, 'true');
                    sessionStorage.setItem(roomDetailsId, 'true');
                    window.location.reload();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while adding the bassinet', 'error');
            });
        });
    });

    // Handle bassinet deletion
    document.querySelectorAll('form[action*="delete-bassinet"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this bassinet?')) {
                return;
            }
            
            fetch(this.action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Keep the expanded states
                    const room = this.closest('[data-room-id]');
                    const roomId = room.dataset.roomId;
                    const wardId = room.closest('tr[id^="ward-"]').id;
                    const roomDetailsId = `room-${roomId}`;
                    sessionStorage.setItem(wardId, 'true');
                    sessionStorage.setItem(roomDetailsId, 'true');
                    window.location.reload();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while deleting the bassinet', 'error');
            });
        });
    });
</script>
@endsection
