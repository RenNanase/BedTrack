@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Ward Management</h1>
            <a href="{{ route('super-admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add New Ward Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Ward</h2>
            <form action="{{ route('super-admin.add-ward') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="ward_name" class="block text-sm font-medium text-gray-700">Ward Name</label>
                    <input type="text" name="ward_name" id="ward_name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition">
                    Add Ward
                </button>
            </form>
        </div>

        <!-- Wards List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Wards List</h2>
            <div class="space-y-6">
                @foreach($wards as $ward)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-semibold text-lg">{{ $ward->ward_name }}</h3>
                            <form action="{{ route('super-admin.delete-ward', $ward) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this ward?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Add New Room Form -->
                        <div class="mb-4">
                            <form action="{{ route('super-admin.add-room') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="room_number_{{ $ward->id }}" class="block text-sm font-medium text-gray-700">Room Number</label>
                                        <input type="text" name="room_number" id="room_number_{{ $ward->id }}" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    </div>
                                    <div>
                                        <label for="room_type_{{ $ward->id }}" class="block text-sm font-medium text-gray-700">Room Type</label>
                                        <select name="room_type" id="room_type_{{ $ward->id }}" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                            <option value="regular">Regular</option>
                                            <option value="nursery">Nursery</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition">
                                    Add Room
                                </button>
                            </form>
                        </div>

                        <!-- Rooms List -->
                        <div class="space-y-4">
                            @foreach($ward->rooms as $room)
                                <div class="border rounded-lg p-4 ml-4">
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="font-semibold">Room {{ $room->room_number }} ({{ ucfirst($room->room_type) }})</h4>
                                        <form action="{{ route('super-admin.delete-room', $room) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this room?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Add New Bed Form -->
                                    <div class="mb-4">
                                        <form action="{{ route('super-admin.add-bed') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="bed_number_{{ $room->id }}" class="block text-sm font-medium text-gray-700">Bed Number</label>
                                                    <input type="text" name="bed_number" id="bed_number_{{ $room->id }}" required
                                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                </div>
                                                <div>
                                                    <label for="bed_type_{{ $room->id }}" class="block text-sm font-medium text-gray-700">Bed Type</label>
                                                    <select name="bed_type" id="bed_type_{{ $room->id }}" required
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                        <option value="regular">Regular</option>
                                                        <option value="crib">Crib</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition">
                                                Add Bed
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Beds List -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($room->beds as $bed)
                                            <div class="border rounded-lg p-3">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="font-medium">Bed {{ $bed->bed_number }}</p>
                                                        <p class="text-sm text-gray-600">{{ ucfirst($bed->bed_type) }}</p>
                                                        <p class="text-sm {{ $bed->status === 'Available' ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $bed->status }}
                                                        </p>
                                                    </div>
                                                    <form action="{{ route('super-admin.delete-bed', $bed) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this bed?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
