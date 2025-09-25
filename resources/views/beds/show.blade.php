@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Message -->
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Hidden fields for JavaScript -->
        <input type="hidden" id="is_crib" value="{{ $bed->is_crib ? '1' : '0' }}">
        <input type="hidden" id="ward_type" value="{{ $bed->room->ward->ward_name === 'Nursery Ward' ? 'nursery' : 'regular' }}">

        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-primary/5">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Bed Details</h1>
                    <p class="text-sm text-gray-500">{{ $bed->room->ward->ward_name }} - {{ $bed->room->room_name }} - {{ $bed->bed_number }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Back to Dashboard
                </a>
            </div>

            <div class="p-6">
                <!-- Current Status Card -->
                <div class="mb-8 border rounded-lg overflow-hidden shadow-sm
                    {{ $bed->status == 'Available' ? 'border-green-300' :
                      ($bed->status == 'Booked' ? 'border-yellow-300' :
                       ($bed->status == 'Discharged' ? 'border-blue-300' : 'border-red-300')) }}">
                    <div class="px-4 py-3 border-b
                        {{ $bed->status == 'Available' ? 'bg-green-100 text-green-800' :
                          ($bed->status == 'Booked' ? 'bg-yellow-100 text-yellow-800' :
                           ($bed->status == 'Discharged' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                        <h2 class="font-semibold text-lg">Current Status</h2>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 rounded-full mr-4 flex items-center justify-center
                                {{ $bed->status == 'Available' ? 'bg-green-500' :
                                  ($bed->status == 'Booked' ? 'bg-yellow-500' :
                                   ($bed->status == 'Discharged' ? 'bg-blue-500' : 'bg-red-500')) }} text-white">
                                @if($bed->status == 'Available')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($bed->status == 'Booked')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($bed->status == 'Discharged')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800">{{ $bed->status }}</div>
                                @if($bed->status != 'Available')
                                    <div class="text-gray-600">Patient: {{ $bed->patient_name }}</div>
                                @endif
                                @if($bed->status_changed_at)
                                    <div class="text-xs text-gray-500 mt-1">Status updated: {{ $bed->status_changed_at->format('M d, Y - h:i A') }}</div>
                                @endif
                                
                                <!-- Housekeeping Information -->
                                @if($bed->status === 'Housekeeping' && $bed->housekeeping_started_at)
                                    <div class="mt-2 p-2 bg-gray-100 rounded-md">
                                        <div class="flex items-center mb-1 text-sm font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ str_contains($bed->housekeeping_remarks ?? '', 'Terminal') ? 'Terminal Cleaning (2 hours)' : 'Normal Cleaning (1 hour)' }}</span>
                                        </div>
                                        
                                        <div class="mb-1 text-xs text-gray-600">Started: {{ $bed->housekeeping_started_at->format('M d, Y - h:i A') }}</div>
                                        
                                        @php
                                            $completionTime = str_contains($bed->housekeeping_remarks ?? '', 'Terminal') 
                                                ? $bed->housekeeping_started_at->addHours(2) 
                                                : $bed->housekeeping_started_at->addHour();
                                            $now = \Carbon\Carbon::now();
                                            $remainingTime = $completionTime->diffForHumans(['parts' => 2]);
                                            $isCompleted = $now->greaterThan($completionTime);
                                            
                                            // Calculate percentage complete - from start time to completion time
                                            $totalMinutes = $completionTime->diffInMinutes($bed->housekeeping_started_at);
                                            $minutesPassed = $now->diffInMinutes($bed->housekeeping_started_at);
                                            $percentComplete = min(100, max(0, ($minutesPassed / $totalMinutes) * 100));
                                        @endphp
                                        
                                        <div class="mb-1 text-xs {{ $isCompleted ? 'text-green-600 font-bold' : 'text-orange-600' }}">
                                            {{ $isCompleted ? 'Cleaning completed' : "Expected completion $remainingTime" }}
                                        </div>
                                        
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentComplete }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Status Form -->
                <div class="mb-8 border rounded-lg overflow-hidden shadow-sm">
                    <div class="px-4 py-3 border-b bg-primary/5">
                        <h2 class="font-semibold text-lg text-gray-800">Change Status</h2>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('beds.update-status', $bed) }}" method="POST" id="bedStatusForm" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- Display Laravel validation errors -->
                            @if ($errors->any())
                                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                    <p class="font-bold">Error</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Quick Check-in Button for Available Beds -->
                            @if($bed->status == 'Available')
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h3 class="text-lg font-bold text-green-800 mb-3 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Quick Check-in
                                </h3>
                                <p class="text-sm text-green-700 mb-4">You can check-in a patient directly with this form:</p>
                                
                                <!-- Check-in Form -->
                                <form action="{{ route('beds.update-status', $bed) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="Occupied">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="patient_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                            <select name="patient_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select category...</option>
                                                <option value="Adult">Adult</option>
                                                <option value="Paediatric">Paediatric</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                            <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select gender...</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">MRN <span class="text-red-500">*</span></label>
                                            <input type="text" name="mrn" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                                    </div>
                                    
                                    <button type="submit" class="w-full py-3 px-4 bg-green-600 text-white text-lg font-bold rounded-md hover:bg-green-700 transition-colors">
                                        Check-in Patient Now
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Quick Book Button -->
                            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h3 class="text-lg font-bold text-yellow-800 mb-3 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Quick Book
                                </h3>
                                <p class="text-sm text-yellow-700 mb-4">You can book this bed for an upcoming patient:</p>
                                
                                <!-- Book Form -->
                                <form action="{{ route('beds.update-status', $bed) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="Booked">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="patient_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                            <select name="patient_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select category...</option>
                                                <option value="Adult">Adult</option>
                                                <option value="Paediatric">Paediatric</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                            <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select gender...</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">MRN <span class="text-red-500">*</span></label>
                                            <input type="text" name="mrn" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                                    </div>
                                    
                                    <button type="submit" class="w-full py-3 px-4 bg-yellow-600 text-white text-lg font-bold rounded-md hover:bg-yellow-700 transition-colors">
                                        Book Bed Now
                                    </button>
                                </form>
                            </div>
                            @endif

                            <div class="mb-4 bg-blue-50 p-3 rounded-md text-sm text-blue-800">
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    Fields marked with <span class="text-red-500 mx-1">*</span> are required when registering a patient.
                                </p>
                            </div>

                            <!-- Status Selection -->
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" onchange="togglePatientFields(); console.log('Status changed to: ' + this.value);"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                    @if($bed->status == 'Available')
                                    <!-- <option value="Available" {{ $bed->status == 'Available' ? 'selected' : '' }}>Ready for Patient</option>
                                    <option value="Booked" {{ $bed->status == 'Booked' ? 'selected' : '' }}>Book</option>
                                    <option value="Occupied" {{ $bed->status == 'Occupied' ? 'selected' : '' }}>Check-In</option> -->
                                    <!-- <option value="Housekeeping" {{ $bed->status == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option> -->
                                    @elseif($bed->status == 'Occupied')
                                    <option value="Available" {{ $bed->status == 'Available' ? 'selected' : '' }}>Ready for Patient</option>
                                    <option value="Booked" {{ $bed->status == 'Booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="Occupied" {{ $bed->status == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="Discharged" {{ $bed->status == 'Discharged' ? 'selected' : '' }}>Discharge Patient</option>
                                    <option value="Transfer-out" {{ $bed->status == 'Transfer-out' ? 'selected' : '' }}>Transfer Patient Out</option>
                                    <!-- <option value="Housekeeping" {{ $bed->status == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option> -->
                                    @elseif($bed->status == 'Transfer-in')
                                    <option value="Available" {{ $bed->status == 'Available' ? 'selected' : '' }}>Ready for Patient</option>
                                    <option value="Booked" {{ $bed->status == 'Booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="Occupied" {{ $bed->status == 'Occupied' ? 'selected' : '' }}>Complete Transfer</option>
                                    <!-- <option value="Housekeeping" {{ $bed->status == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option> -->
                                    @elseif($bed->status == 'Housekeeping')
                                    <option value="Housekeeping" {{ $bed->status == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                                    <!-- <option value="Available" {{ $bed->status == 'Available' ? 'selected' : '' }}>Mark as Ready for Patient</option> -->
                                    @else
                                    <option value="Available" {{ $bed->status == 'Available' ? 'selected' : '' }}>Ready for Patient</option>
                                    <option value="Booked" {{ $bed->status == 'Booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="Occupied" {{ $bed->status == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="Discharged" {{ $bed->status == 'Discharged' ? 'selected' : '' }}>Discharge Patient</option>
                                    <option value="Housekeeping" {{ $bed->status == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                                    @endif
                                </select>

                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        When you select "Discharge Patient", the patient will be discharged and the bed will automatically be set to "Housekeeping" status.
                                    </span>
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Beds in "Housekeeping" status will automatically become "Available" after 1-2 hours. If cleaning is completed early, you can manually mark the bed as "Ready for Patient".
                                    </span>
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        You can set an "Available" bed to "Housekeeping" status when needed for additional cleaning or maintenance.
                                    </span>
                                </p>
                            </div>

                            <!-- Quick Housekeeping Button (only for available beds) -->
                            @if($bed->status == 'Available')
                            <div class="mt-4 p-3 bg-blue-50 rounded-md border border-blue-200">
                                <h3 class="font-medium text-blue-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Need to clean or maintain this bed?
                                </h3>
                                <form action="{{ route('beds.update-status', $bed) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="Housekeeping">
                                    <input type="hidden" name="housekeeping_remarks" value="Special Maintenance (Manual) - Requires staff check">
                                    <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                        Mark for Housekeeping
                                    </button>
                                </form>
                                <p class="text-xs text-blue-700 mt-2">
                                    Click this button to quickly set this bed for housekeeping or maintenance.
                                </p>
                            </div>
                            @endif

                            <!-- Transfer Destination Bed Selection (shown only when Transfer-out is selected) -->
                            <div id="transferDestinationBed" class="mb-4 {{ $bed->status == 'Transfer-out' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Destination</label>
                                <div class="space-y-4">
                                    <!-- Ward Selection -->
                                    <div>
                                        <label for="transfer_ward_id" class="block text-sm font-medium text-gray-700 mb-1">Select Ward</label>
                                        <select name="destination_ward_id" id="transfer_ward_id" onchange="loadRooms(this.value)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a ward...</option>
                                            @foreach($wards as $ward)
                                                <option value="{{ $ward->id }}">{{ $ward->ward_name }}{{ $ward->id == $bed->room->ward_id ? ' (Current Ward)' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                You can transfer patients within the same ward or to other wards.
                                            </span>
                                        </p>
                                    </div>

                                    <!-- Room Selection -->
                                    <div id="transfer_room_container" class="hidden">
                                        <label for="transfer_room_id" class="block text-sm font-medium text-gray-700 mb-1">Select Room</label>
                                        <select name="transfer_room_id" id="transfer_room_id" onchange="loadBeds(this.value)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a room...</option>
                                        </select>
                                    </div>

                                    <!-- Bassinet Selection (only shown for transfers to maternity wards) -->
                                    <div id="transfer_bassinet_container" class="hidden">
                                        <label for="destination_bassinet_id" class="block text-sm font-medium text-gray-700 mb-1">Select Bassinet</label>
                                        <select name="destination_bassinet_id" id="destination_bassinet_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a bassinet...</option>
                                        </select>
                                        
                                        <!-- Additional mother information fields for bassinet transfers -->
                                        <div class="mt-3">
                                            <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                                            <input type="text" name="mother_name" id="mother_name" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                                placeholder="Enter mother's name">
                                        </div>
                                        
                                        <div class="mt-3">
                                            <label for="mother_mrn" class="block text-sm font-medium text-gray-700 mb-1">Mother's MRN</label>
                                            <input type="text" name="mother_mrn" id="mother_mrn" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                                placeholder="Enter mother's MRN">
                                        </div>
                                        
                                        <p class="text-xs text-gray-500 mt-3">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Transfer baby directly to a maternity ward bassinet
                                            </span>
                                        </p>
                                    </div>

                                    <!-- Bed Selection -->
                                    <div id="transfer_bed_container" class="hidden">
                                        <label for="transfer_destination_bed_id" class="block text-sm font-medium text-gray-700 mb-1">Select Bed</label>
                                        <select name="destination_bed_id" id="transfer_destination_bed_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a bed...</option>
                                        </select>
                                        
                                        <!-- Maintain Hazard Option (Only shown when source bed has hazard) -->
                                        @if($bed->has_hazard)
                                        <div class="mt-3 flex items-center">
                                            <input type="checkbox" name="maintain_hazard" id="maintain_hazard" value="1" checked class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                                            <label for="maintain_hazard" class="ml-2 block text-sm text-gray-700">
                                                Transfer hazard status with the patient
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 ml-6">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Hazard status will follow the patient to their new bed. If unchecked, hazard will be removed completely.
                                            </span>
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Source Bed Selection (shown only when Transfer-in is selected) -->
                            <div id="transferSourceBed" class="mb-4 {{ $bed->status == 'Transfer-in' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Source</label>
                                <div class="space-y-4">
                                    <!-- Ward Selection -->
                                    <div>
                                        <label for="source_ward_id" class="block text-sm font-medium text-gray-700 mb-1">Select Ward</label>
                                        <select name="source_ward_id" id="source_ward_id" onchange="loadSourceRooms(this.value)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a ward...</option>
                                            @foreach($wards as $ward)
                                                <option value="{{ $ward->id }}">{{ $ward->ward_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Room Selection -->
                                    <div id="source_room_container" class="hidden">
                                        <label for="source_room_id" class="block text-sm font-medium text-gray-700 mb-1">Select Room</label>
                                        <select name="source_room_id" id="source_room_id" onchange="loadSourceBeds(this.value)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a room...</option>
                                        </select>
                                    </div>

                                    <!-- Bed Selection -->
                                    <div id="source_bed_container" class="hidden">
                                        <label for="transfer_source_bed_id" class="block text-sm font-medium text-gray-700 mb-1">Select Bed</label>
                                        <select name="source_bed_id" id="transfer_source_bed_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Select a bed...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Housekeeping Section (shows only when Housekeeping is selected) -->
                            <div id="housekeepingSection" class="mb-4 border rounded-lg p-4 bg-gray-50 {{ $bed->status == 'Housekeeping' ? '' : 'hidden' }}">
                                <div class="flex items-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <label class="text-sm font-semibold text-gray-700">Housekeeping Details</label>
                                </div>

                                <div class="mb-3">
                                    <label for="housekeeping_remarks" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cleaning Type
                                    </label>
                                    <select name="housekeeping_remarks" id="housekeeping_remarks" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                        <option value="Normal Cleaning (1 hour) - Standard case" {{ $bed->housekeeping_remarks == 'Normal Cleaning (1 hour) - Standard case' ? 'selected' : '' }}>
                                            Normal Cleaning (1 hour)
                                        </option>
                                        <option value="Terminal Cleaning (2 hours) - Hazardous case" {{ $bed->housekeeping_remarks == 'Terminal Cleaning (2 hours) - Hazardous case' ? 'selected' : '' }}>
                                            Terminal Cleaning (2 hours)
                                        </option>
                                        <option value="Special Maintenance (Manual) - Requires staff check" {{ $bed->housekeeping_remarks == 'Special Maintenance (Manual) - Requires staff check' ? 'selected' : '' }}>
                                            Special Maintenance (Manual)
                                        </option>
                                    </select>
                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <strong>Terminal Cleaning (2 hours)</strong>: For beds that had patients with hazardous conditions.
                                    </span>
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <strong>Normal Cleaning (1 hour)</strong>: Standard cleaning procedure for regular cases.
                                    </span>
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <strong>Special Maintenance (Manual)</strong>: Use for available beds that need maintenance or special cleaning.
                                    </span>
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <strong>Manual Completion</strong>: If cleaning is finished early, you can manually mark the bed as "Ready for Patient" using the status dropdown above.
                                    </span>
                                </p>
                                
                                <!-- Quick Mark as Ready Button (only for housekeeping beds) -->
                                @if($bed->status == 'Housekeeping')
                                <div class="mt-4 p-3 bg-green-50 rounded-md border border-green-200">
                                    <h3 class="font-medium text-green-800 mb-2 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Cleaning completed?
                                    </h3>
                                    <form action="{{ route('beds.update-status', $bed) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Available">
                                        <button type="submit" class="w-full py-2 px-4 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                                            Mark as Ready for Patient
                                        </button>
                                    </form>
                                    <p class="text-xs text-green-700 mt-2">
                                        Click to mark this bed as clean and ready for the next patient.
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- Discharge Section (shows only when Discharged is selected) -->
                            <div id="dischargeSection" class="mb-4 border rounded-lg p-4 bg-blue-50 {{ $bed->status == 'Discharged' ? '' : 'hidden' }}">
                                <div class="flex items-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <label class="text-sm font-semibold text-gray-700">Discharge Details</label>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="text-sm text-blue-800">
                                        Patient will be discharged and the bed will automatically be set to "Housekeeping" status.
                                    </p>
                                    <p class="text-sm text-blue-800 mt-2">
                                        Make sure all patient information is correct before proceeding.
                                    </p>
                                </div>
                            </div>

                            <!-- Patient Information Fields -->
                            <div id="patientFields" class="space-y-4 {{ in_array($bed->status, ['Booked', 'Occupied', 'Transfer-out', 'Transfer-in']) ? '' : 'hidden' }}">
                                <div class="mb-4">
                                    <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Patient Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="patient_name" id="patient_name" value="{{ $bed->patient_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="mb-4">
                                    <label for="patient_category" class="block text-sm font-medium text-gray-700 mb-2">
                                        Patient Category <span class="text-red-500">*</span>
                                    </label>
                                    <select name="patient_category" id="patient_category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                        <option value="">Select category...</option>
                                        <option value="Adult" {{ $bed->patient_category == 'Adult' ? 'selected' : '' }}>Adult</option>
                                        <option value="Paediatric" {{ $bed->patient_category == 'Paediatric' ? 'selected' : '' }}>Paediatric</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select name="gender" id="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                        <option value="">Select gender...</option>
                                        <option value="Male" {{ $bed->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $bed->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="mrn" class="block text-sm font-medium text-gray-700 mb-2">
                                        MRN (Medical Record Number) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="mrn" id="mrn" value="{{ $bed->mrn }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">{{ $bed->notes }}</textarea>
                                </div>
                                
                                <!-- Backup Check-in Button (if normal submission fails) -->
                                @if($bed->status == 'Available')
                                <div class="mt-2 p-3 bg-yellow-50 rounded-md border border-yellow-200">
                                    <h3 class="font-medium text-yellow-800 mb-2 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Having trouble with Check-in?
                                    </h3>
                                    <form action="{{ route('beds.update-status', $bed) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Occupied">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
                                            <input type="text" name="patient_name" placeholder="Patient Name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            <select name="patient_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select category...</option>
                                                <option value="Adult">Adult</option>
                                                <option value="Paediatric">Paediatric</option>
                                            </select>
                                            <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select gender...</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                            <input type="text" name="mrn" placeholder="MRN Number" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                        <button type="submit" class="w-full py-2 px-4 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                                            Emergency Check-in Patient
                                        </button>
                                    </form>
                                    <p class="text-xs text-yellow-700 mt-2">
                                        This button provides a direct check-in option that bypasses JavaScript validation.
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- Hazard Toggle Section -->
                            <div class="mb-4 border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center mb-2">
                                    <span class="text-red-600 mr-2">☠️</span>
                                    <label for="has_hazard" class="text-sm font-semibold text-gray-700">Bed Hazard Warning</label>
                                </div>

                                <div class="flex items-center mb-2">
                                    <input type="checkbox" name="has_hazard" id="has_hazard" value="1" class="form-checkbox h-5 w-5 text-primary"
                                        {{ $bed->has_hazard ? 'checked' : '' }} onchange="toggleHazardNotes(this.checked)">
                                    <label for="has_hazard" class="ml-2 text-sm text-gray-700">
                                        Mark this bed as having a hazard
                                    </label>
                                </div>

                                <div id="hazard_notes_container" class="{{ $bed->has_hazard ? '' : 'hidden' }}">
                                    <label for="hazard_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Hazard Details <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="hazard_notes" id="hazard_notes" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                        placeholder="Describe the hazard...">{{ $bed->hazard_notes }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            This hazard will be displayed on the dashboard with a 💀 skull emoji.
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Patient Information Card (if not available) -->
                @if($bed->status != 'Available' && $bed->status != 'Housekeeping')
                    <div class="border rounded-lg overflow-hidden shadow-sm">
                        <div class="px-4 py-3 border-b bg-primary/5 flex justify-between items-center">
                            <h2 class="font-semibold text-lg text-gray-800">Patient Information</h2>
                            <a href="{{ route('beds.edit-patient', $bed) }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-sm">
                                Edit Details
                            </a>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Patient Name</h3>
                                    <p class="text-lg text-gray-800">{{ $bed->patient_name }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Category</h3>
                                    <p class="text-lg text-gray-800">{{ $bed->patient_category ?: 'Not specified' }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Gender</h3>
                                    <p class="text-lg text-gray-800">{{ $bed->gender ?: 'Not specified' }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 mb-1">MRN</h3>
                                    <p class="text-lg text-gray-800">{{ $bed->mrn ?: 'Not specified' }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 mb-1">Notes</h3>
                                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                    @if($bed->notes)
                                        <p class="text-gray-800 whitespace-pre-line">{{ $bed->notes }}</p>
                                    @else
                                        <p class="text-gray-500 italic">No additional notes</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script>
function togglePatientFields() {
    console.log("%c[togglePatientFields] Function called", "background-color: #4b5563; color: white; padding: 2px 4px; border-radius: 4px;");
    const status = document.getElementById('status').value;
    console.log("%c[togglePatientFields] Current status:", "color: blue; font-weight: bold;", status);
    
    // Get references to all relevant DOM elements
    const patientFields = document.getElementById('patientFields');
    const transferDestinationBed = document.getElementById('transferDestinationBed');
    const transferSourceBed = document.getElementById('transferSourceBed');
    const housekeepingSection = document.getElementById('housekeepingSection');
    const dischargeSection = document.getElementById('dischargeSection');
    
    // Log the existence of each element
    console.log("[togglePatientFields] DOM elements status:", {
        patientFields: patientFields ? "Found" : "Missing",
        transferDestinationBed: transferDestinationBed ? "Found" : "Missing",
        transferSourceBed: transferSourceBed ? "Found" : "Missing", 
        housekeepingSection: housekeepingSection ? "Found" : "Missing",
        dischargeSection: dischargeSection ? "Found" : "Missing",
    });
    
    // Helper function to safely toggle element visibility
    const safeToggle = (element, shouldShow) => {
        if (element) {
            if (shouldShow) {
                element.classList.remove('hidden');
                console.log(`[togglePatientFields] ${element.id} is now visible`);
            } else {
                element.classList.add('hidden');
                console.log(`[togglePatientFields] ${element.id} is now hidden`);
            }
            return true;
        } else {
            if (shouldShow) {
                console.warn(`%c[togglePatientFields] WARNING: Could not show element - element not found`, "color: orange");
            } else {
                console.warn(`%c[togglePatientFields] WARNING: Could not hide element - element not found`, "color: orange");
            }
            return false;
        }
    };
    
    // First, handle patient fields visibility
    if (['Booked', 'Occupied', 'Transfer-out', 'Transfer-in'].includes(status)) {
        console.log("%c[togglePatientFields] Status requires patient fields - showing patient fields", "color: green");
        if (safeToggle(patientFields, true)) {
            // Focus on the first input field to help the user
            setTimeout(() => {
                const patientNameField = document.getElementById('patient_name');
                if (patientNameField) {
                    console.log("[togglePatientFields] Focusing on patient name field");
                    patientNameField.focus();
                } else {
                    console.error("[togglePatientFields] Could not find patient name field to focus");
                }
            }, 100);
        } else {
            console.error("%c[togglePatientFields] ERROR: Patient fields section not found in the DOM", "color: red; font-weight: bold");
        }
    } else {
        console.log("%c[togglePatientFields] Status does not require patient fields - hiding patient fields", "color: orange");
        safeToggle(patientFields, false);
    }
    
    // Handle transfer destination visibility
    console.log("[togglePatientFields] Handling transfer destination visibility");
    safeToggle(transferDestinationBed, status === 'Transfer-out');
    
    // Handle transfer source visibility
    console.log("[togglePatientFields] Handling transfer source visibility");
    safeToggle(transferSourceBed, status === 'Transfer-in');
    
    // Handle housekeeping section visibility
    console.log("[togglePatientFields] Handling housekeeping section visibility");
    safeToggle(housekeepingSection, status === 'Housekeeping');
    
    // Handle discharge section visibility
    console.log("[togglePatientFields] Handling discharge section visibility");
    safeToggle(dischargeSection, status === 'Discharged');
    
    console.log("%c[togglePatientFields] Function completed", "background-color: #4b5563; color: white; padding: 2px 4px; border-radius: 4px;");
}

function toggleHazardNotes(isChecked) {
    const hazardNotesContainer = document.getElementById('hazard_notes_container');
    if (isChecked) {
        hazardNotesContainer.classList.remove('hidden');
    } else {
        hazardNotesContainer.classList.add('hidden');
    }
}

function validateForm() {
    const status = document.getElementById('status').value;
    let isValid = true;
    
    console.log("Validating form with status:", status);
    
    // For statuses requiring patient info, only validate if those fields are visible
    if (['Booked', 'Occupied', 'Transfer-out', 'Transfer-in'].includes(status)) {
        const patientFields = document.getElementById('patientFields');
        const isVisible = patientFields && !patientFields.classList.contains('hidden');
        
        console.log("Patient fields visible:", isVisible);
        
        // Only validate if the fields are actually visible
        if (isVisible) {
            const patientName = document.getElementById('patient_name').value.trim();
            const patientCategory = document.getElementById('patient_category').value;
            const gender = document.getElementById('gender').value;
            const mrn = document.getElementById('mrn').value.trim();
            
            console.log("Patient validation values: ", {
                patientName,
                patientCategory,
                gender,
                mrn,
                patientNameExists: !!patientName,
                patientCategoryExists: !!patientCategory,
                genderExists: !!gender,
                mrnExists: !!mrn
            });
            
            if (!patientName || !patientCategory || !gender || !mrn) {
                alert('Please fill in all required patient information fields (Name, Category, Gender, and MRN).');
                console.log("Patient fields validation failed. Empty fields were detected.");
                
                // Highlight the first empty field
                if (!patientName) {
                    console.log("Patient name field is empty. Focusing on it.");
                    document.getElementById('patient_name').focus();
                }
                else if (!patientCategory) {
                    console.log("Patient category field is empty. Focusing on it.");
                    document.getElementById('patient_category').focus();
                }
                else if (!gender) {
                    console.log("Gender field is empty. Focusing on it.");
                    document.getElementById('gender').focus();
                }
                else if (!mrn) {
                    console.log("MRN field is empty. Focusing on it.");
                    document.getElementById('mrn').focus();
                }
                
                isValid = false;
                return isValid;
            } else {
                console.log("Patient information validation passed.");
            }
        }
    }
    
    // Additional validation for specific statuses
    if (status === 'Booked' || status === 'Occupied') {
        console.log("Validating Book/Check-In fields");
        
        // Make sure patient fields are filled
        const patientName = document.getElementById('patient_name').value.trim();
        const patientCategory = document.getElementById('patient_category').value;
        const gender = document.getElementById('gender').value;
        const mrn = document.getElementById('mrn').value.trim();
        
        console.log("Required fields for check-in: ", {
            patientName,
            patientCategory,
            gender,
            mrn
        });
        
        if (!patientName) {
            alert('Patient Name is required.');
            document.getElementById('patient_name').focus();
            console.error("Check-in validation failed: Patient name missing");
            isValid = false;
            return isValid;
        }
        
        if (!patientCategory) {
            alert('Patient Category is required.');
            document.getElementById('patient_category').focus();
            console.error("Check-in validation failed: Patient category missing");
            isValid = false;
            return isValid;
        }
        
        if (!gender) {
            alert('Gender is required.');
            document.getElementById('gender').focus();
            console.error("Check-in validation failed: Gender missing");
            isValid = false;
            return isValid;
        }
        
        if (!mrn) {
            alert('MRN (Medical Record Number) is required.');
            document.getElementById('mrn').focus();
            console.error("Check-in validation failed: MRN missing");
            isValid = false;
            return isValid;
        }
        
        console.log("Check-in validation passed successfully.");
    }
    
    // For housekeeping
    if (status === 'Housekeeping') {
        // Check if housekeeping section is visible
        const housekeepingSection = document.getElementById('housekeepingSection');
        const isVisible = housekeepingSection && !housekeepingSection.classList.contains('hidden');
        
        console.log("Housekeeping section visible:", isVisible);
        
        // Only validate if visible
        if (isVisible) {
            // Check if housekeeping remarks are selected
            const remarksSelect = document.getElementById('housekeeping_remarks');
            if (remarksSelect && !remarksSelect.value) {
                alert('Please select a cleaning type.');
                isValid = false;
                return isValid;
            }
        }
    }
    
    // For transfer-out
    if (status === 'Transfer-out') {
        // Check if transfer section is visible
        const transferDestinationBed = document.getElementById('transferDestinationBed');
        const isVisible = transferDestinationBed && !transferDestinationBed.classList.contains('hidden');
        
        console.log("Transfer destination section visible:", isVisible);
        
        if (!isVisible) {
            return isValid;
        }
        
        // Get nursery/maternity info
        const isCrib = document.getElementById('is_crib').value === '1';
        const isNurseryWard = document.getElementById('ward_type').value === 'nursery';
        
        // Get destination ward info
        const destinationWardId = document.getElementById('transfer_ward_id').value;
        const destinationRoomId = document.getElementById('transfer_room_id').value;
        
        if (!destinationWardId) {
            alert('Please select a destination ward.');
            isValid = false;
            return isValid;
        }
        
        // Create a map of ward IDs to their nursery status
        const wardNurseryData = {};
        @foreach($wards as $ward)
            wardNurseryData[{{ $ward->id }}] = {{ $ward->is_nursery ? 'true' : 'false' }};
        @endforeach
        
        // Check if selected ward is maternity
        const isMaternityWard = !wardNurseryData[destinationWardId];
        
        // Check if a destination room is selected
        if (!destinationRoomId) {
            alert('Please select a destination room.');
            isValid = false;
            return isValid;
        }
        
        // Check if a destination bed is selected
        const destinationBedId = document.getElementById('transfer_destination_bed_id').value;
        if (!destinationBedId) {
            alert('Please select a destination bed.');
            isValid = false;
            return isValid;
        }
        
        // If this is a transfer from nursery to maternity, check if a bassinet is selected
        if (isCrib && isNurseryWard && isMaternityWard) {
            // Make sure the bassinet container is visible
            const bassinetContainer = document.getElementById('transfer_bassinet_container');
            if (bassinetContainer && !bassinetContainer.classList.contains('hidden')) {
                const bassinetId = document.getElementById('destination_bassinet_id').value;
                if (!bassinetId) {
                    alert('Please select a destination bassinet.');
                    isValid = false;
                    return isValid;
                }
            }
        }
    }
    
    // For transfer-in
    if (status === 'Transfer-in') {
        // Check if transfer source section is visible
        const transferSourceBed = document.getElementById('transferSourceBed');
        const isVisible = transferSourceBed && !transferSourceBed.classList.contains('hidden');
        
        console.log("Transfer source section visible:", isVisible);
        
        if (!isVisible) {
            return isValid;
        }
        
        const sourceWardId = document.getElementById('source_ward_id').value;
        const sourceBedId = document.getElementById('transfer_source_bed_id').value;
        
        if (!sourceWardId) {
            alert('Please select a source ward.');
            isValid = false;
            return isValid;
        } else if (!sourceBedId) {
            alert('Please select a source bed.');
            isValid = false;
            return isValid;
        }
    }
    
    console.log("Form validation result:", isValid);
    return isValid;
}

// Add these new functions for handling the hierarchical selection
function loadRooms(wardId) {
    const roomContainer = document.getElementById('transfer_room_container');
    const bedContainer = document.getElementById('transfer_bed_container');
    const bassinetContainer = document.getElementById('transfer_bassinet_container');
    const roomSelect = document.getElementById('transfer_room_id');

    // Reset and hide dependent selects
    roomSelect.innerHTML = '<option value="">Select a room...</option>';
    document.getElementById('transfer_destination_bed_id').innerHTML = '<option value="">Select a bed...</option>';
    roomContainer.classList.add('hidden');
    bedContainer.classList.add('hidden');
    bassinetContainer.classList.add('hidden');

    if (!wardId) return;
    
    // Create a map of ward IDs to their nursery status
    const wardNurseryData = {};
    @foreach($wards as $ward)
        wardNurseryData[{{ $ward->id }}] = {{ $ward->is_nursery ? 'true' : 'false' }};
    @endforeach
    
    // Check if the selected ward is a maternity ward (not nursery)
    const isMaternityWard = !wardNurseryData[wardId];
    console.log('Selected ward ID:', wardId, 'Is nursery:', wardNurseryData[wardId], 'Is maternity:', isMaternityWard);
    
    // Store the current bed information
    const isCrib = document.getElementById('is_crib').value === '1';
    const isNurseryWard = document.getElementById('ward_type').value === 'nursery';
    
    // Show loading state for rooms
    roomSelect.innerHTML = '<option value="">Loading rooms...</option>';
    roomContainer.classList.remove('hidden');

    // Fetch rooms for the selected ward
    fetch(`/BedTrack/public/wards/${wardId}/rooms`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(rooms => {
        roomSelect.innerHTML = '<option value="">Select a room...</option>';
        if (rooms.length === 0) {
            roomSelect.innerHTML += '<option value="" disabled>No rooms available</option>';
        } else {
            rooms.forEach(room => {
                roomSelect.innerHTML += `<option value="${room.id}">${room.room_name}</option>`;
            });
        }
    })
    .catch(error => {
        console.error('Error loading rooms:', error);
        roomSelect.innerHTML = '<option value="">Error loading rooms. Please try again.</option>';
    });
}

function loadBeds(roomId) {
    // Reset and hide dependent selects
    const bedSelect = document.getElementById('transfer_destination_bed_id');
    bedSelect.innerHTML = '<option value="">Select a bed...</option>';
    document.getElementById('transfer_bassinet_container').classList.add('hidden');

    if (!roomId) {
        document.getElementById('transfer_bed_container').classList.add('hidden');
        return;
    }

    // Get the nursery and ward info
    const isCrib = document.getElementById('is_crib').value === '1';
    const isNurseryWard = document.getElementById('ward_type').value === 'nursery';
    
    // Get the current ward selection
    const transferWardSelect = document.getElementById('transfer_ward_id');
    
    // Create a map of ward IDs to their nursery status
    const wardNurseryData = {};
    @foreach($wards as $ward)
        wardNurseryData[{{ $ward->id }}] = {{ $ward->is_nursery ? 'true' : 'false' }};
    @endforeach
    
    // Check if selected ward is maternity (not nursery)
    const wardId = transferWardSelect.value;
    const isMaternityWard = wardId && !wardNurseryData[wardId];
    
    console.log('Load beds for room:', roomId);
    console.log('Is crib:', isCrib);
    console.log('Is nursery ward:', isNurseryWard);
    console.log('Is maternity ward:', isMaternityWard);
    console.log('Ward ID:', wardId);

    // Show loading state
    bedSelect.innerHTML = '<option value="">Loading beds...</option>';
    document.getElementById('transfer_bed_container').classList.remove('hidden');

    // For any transfer, we first need to load the available beds
    fetch(`/BedTrack/public/rooms/${roomId}/beds`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(beds => {
        bedSelect.innerHTML = '<option value="">Select a bed...</option>';
        if (beds.length === 0) {
            bedSelect.innerHTML += '<option value="" disabled>No beds available</option>';
        } else {
            beds.forEach(bed => {
                bedSelect.innerHTML += `<option value="${bed.id}">${bed.name}</option>`;
            });
        }
        
        // If this is a transfer from nursery crib to a maternity ward,
        // add an event listener to the bed selection to load bassinets
        if (isCrib && isNurseryWard && isMaternityWard) {
            console.log('This is a nursery to maternity transfer - setting up bed change event');
            bedSelect.addEventListener('change', function() {
                const selectedBedId = this.value;
                if (selectedBedId) {
                    console.log('Bed selected, loading bassinets for room:', roomId);
                    loadBassinets(roomId);
                } else {
                    // Hide bassinet container if no bed is selected
                    document.getElementById('transfer_bassinet_container').classList.add('hidden');
                }
            });
        }
    })
    .catch(error => {
        console.error('Error loading beds:', error);
        bedSelect.innerHTML = '<option value="">Error loading beds. Please try again.</option>';
    });
}

function loadSourceRooms(wardId) {
    const roomContainer = document.getElementById('source_room_container');
    const bedContainer = document.getElementById('source_bed_container');
    const roomSelect = document.getElementById('source_room_id');
    const bedSelect = document.getElementById('transfer_source_bed_id');

    // Reset and hide dependent selects
    roomSelect.innerHTML = '<option value="">Select a room...</option>';
    bedSelect.innerHTML = '<option value="">Select a bed...</option>';
    roomContainer.classList.add('hidden');
    bedContainer.classList.add('hidden');

    if (!wardId) return;

    // Show loading state
    roomSelect.innerHTML = '<option value="">Loading rooms...</option>';
    roomContainer.classList.remove('hidden');

    // Fetch rooms for the selected ward
    fetch(`/BedTrack/public/wards/${wardId}/rooms`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(rooms => {
        roomSelect.innerHTML = '<option value="">Select a room...</option>';
        if (rooms.length === 0) {
            roomSelect.innerHTML += '<option value="" disabled>No rooms available</option>';
        } else {
            rooms.forEach(room => {
                roomSelect.innerHTML += `<option value="${room.id}">${room.room_name}</option>`;
            });
        }
    })
    .catch(error => {
        console.error('Error loading rooms:', error);
        roomSelect.innerHTML = '<option value="">Error loading rooms. Please try again.</option>';
    });
}

function loadSourceBeds(roomId) {
    const bedContainer = document.getElementById('source_bed_container');
    const bedSelect = document.getElementById('transfer_source_bed_id');

    // Reset and hide bed select
    bedSelect.innerHTML = '<option value="">Select a bed...</option>';
    bedContainer.classList.add('hidden');

    if (!roomId) return;

    // Show loading state
    bedSelect.innerHTML = '<option value="">Loading beds...</option>';
    bedContainer.classList.remove('hidden');

    // Fetch transfer-out beds for the selected room
    fetch(`/BedTrack/public/rooms/${roomId}/transfer-out-beds`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(beds => {
        bedSelect.innerHTML = '<option value="">Select a bed...</option>';
        if (beds.length === 0) {
            bedSelect.innerHTML += '<option value="" disabled>No transfer-out beds available</option>';
        } else {
            beds.forEach(bed => {
                bedSelect.innerHTML += `<option value="${bed.id}">${bed.bed_number}</option>`;
            });
        }
    })
    .catch(error => {
        console.error('Error loading beds:', error);
        bedSelect.innerHTML = '<option value="">Error loading beds. Please try again.</option>';
    });
}

// Function to load available bassinets for the selected maternity ward
function loadBassinets(roomId) {
    const bassinetContainer = document.getElementById('transfer_bassinet_container');
    const bassinetSelect = document.getElementById('destination_bassinet_id');
    
    console.log('Loading bassinets for room ID:', roomId);
    
    // Reset and hide bassinet select
    bassinetSelect.innerHTML = '<option value="">Select a bassinet...</option>';
    
    if (!roomId) {
        bassinetContainer.classList.add('hidden');
        return;
    }
    
    // Show loading state
    bassinetSelect.innerHTML = '<option value="">Loading bassinets...</option>';
    bassinetContainer.classList.remove('hidden');
    
    // Fetch available bassinets for the selected room
    fetch(`/BedTrack/public/rooms/${roomId}/bassinets`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(bassinets => {
        console.log('Bassinets loaded:', bassinets);
        bassinetSelect.innerHTML = '<option value="">Select a bassinet...</option>';
        if (bassinets.length === 0) {
            bassinetSelect.innerHTML += '<option value="" disabled>No bassinets available</option>';
        } else {
            bassinets.forEach(bassinet => {
                bassinetSelect.innerHTML += `<option value="${bassinet.id}">${bassinet.bassinet_number}</option>`;
            });
        }
    })
    .catch(error => {
        console.error('Error loading bassinets:', error);
        bassinetSelect.innerHTML = '<option value="">Error loading bassinets. Please try again.</option>';
    });
}

// Function to pre-validate before form submission
function preValidateCheckIn() {
    const status = document.getElementById('status').value;
    
    // Only run for Occupied/Check-in status
    if (status === 'Occupied') {
        console.log("Pre-validating Check-In form...");
        
        // Required fields for check-in
        const patientName = document.getElementById('patient_name').value.trim();
        const patientCategory = document.getElementById('patient_category').value;
        const gender = document.getElementById('gender').value;
        const mrn = document.getElementById('mrn').value.trim();
        
        console.log("Check-in required fields:", {
            patientName,
            patientCategory,
            gender,
            mrn,
            allFieldsFilled: !!(patientName && patientCategory && gender && mrn)
        });
        
        if (!patientName || !patientCategory || !gender || !mrn) {
            alert('Please fill in all required patient information:\n\n' +
                  (!patientName ? '- Patient Name\n' : '') +
                  (!patientCategory ? '- Patient Category\n' : '') +
                  (!gender ? '- Gender\n' : '') +
                  (!mrn ? '- MRN\n' : ''));
            return false;
        }
        
        return true;
    }
    
    // For other statuses, no additional pre-validation needed
    return true;
}

// Initialize the page when loaded
document.addEventListener('DOMContentLoaded', function() {
    // Log hidden field values for debugging
    console.log('is_crib value:', document.getElementById('is_crib').value);
    console.log('ward_type value:', document.getElementById('ward_type').value);
    
    // Initialize the form based on the selected status
    const currentStatus = document.getElementById('status').value;
    togglePatientFields(currentStatus);
    
    // Handle form submission with validation
    const form = document.getElementById('bedStatusForm');
    form.addEventListener('submit', function(event) {
        // Prevent default form submission first
        event.preventDefault();
        
        console.log('Form is being submitted', {
            status: document.getElementById('status').value,
            patientName: document.getElementById('patient_name')?.value,
            formAction: this.action,
            formMethod: document.querySelector('input[name="_method"]')?.value || 'POST'
        });
        
        // Run our special check-in pre-validation first
        if (!preValidateCheckIn()) {
            console.log('Pre-validation failed for check-in');
            return false;
        }
        
        // Run the main validation
        const isValid = validateForm();
        if (isValid) {
            console.log('Form is valid, preparing to submit');
            
            // Create a helper function to manage the _method input
            const setFormMethod = (methodValue) => {
                console.log('Setting form method to:', methodValue);
                const methodInput = document.querySelector('input[name="_method"]');
                if (methodInput) {
                    console.log('_method input found, updating value');
                    methodInput.value = methodValue;
                } else {
                    // Create a hidden input for method if it doesn't exist
                    console.log('_method input not found, creating new one');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_method';
                    input.value = methodValue;
                    this.appendChild(input);
                }
            };
            
            // Get the current status value
            const statusValue = document.getElementById('status').value;
            console.log('Current status for submission:', statusValue);
            
            // Check if it's a bassinet transfer (from nursery crib to maternity ward bassinet)
            const isCrib = document.getElementById('is_crib').value === '1';
            const isNurseryWard = document.getElementById('ward_type').value === 'nursery';
            
            // Get destination ward info
            const destinationWardId = document.getElementById('transfer_ward_id').value;
            const destinationRoomId = document.getElementById('transfer_room_id').value;
            
            // Create a map of ward IDs to their nursery status
            const wardNurseryData = {};
            @foreach($wards as $ward)
                wardNurseryData[{{ $ward->id }}] = {{ $ward->is_nursery ? 'true' : 'false' }};
            @endforeach
            
            // Check if selected ward is maternity
            const isMaternityWard = !wardNurseryData[destinationWardId];
            
            // Check if bassinet is selected for nursery-to-maternity transfer
            const bassinetContainer = document.getElementById('transfer_bassinet_container');
            const bassinetSelected = !bassinetContainer.classList.contains('hidden') &&
                                    document.getElementById('destination_bassinet_id').value;
            
            if (statusValue === 'Transfer-out' && isCrib && isNurseryWard && isMaternityWard && bassinetSelected) {
                // This is a bassinet transfer - change form action
                console.log('Submitting as a bassinet transfer');
                this.action = "{{ route('beds.transfer-to-maternity', $bed) }}";
                
                // Change method from PUT to POST for the transfer-to-maternity route
                setFormMethod('POST');
                
                // Set flag for dashboard refresh on nursery-to-maternity transfer
                localStorage.setItem('bedtrack_transfer_refresh', 'true');
                
                // Validate mother fields
                const motherName = document.getElementById('mother_name').value.trim();
                const motherMrn = document.getElementById('mother_mrn').value.trim();
                
                if (!motherName || !motherMrn) {
                    if (!confirm('Mother information is incomplete. Continue anyway?')) {
                        return false;
                    }
                }
            } else {
                // Reset to normal update action
                console.log('Using normal update action for status:', statusValue);
                this.action = "{{ route('beds.update-status', $bed) }}";
                
                // Ensure method is PUT for normal status updates
                setFormMethod('PUT');
            }
            
            console.log('Final form submission details:', {
                action: this.action,
                method: document.querySelector('input[name="_method"]')?.value || 'POST',
                status: statusValue
            });
            
            // Submit the form programmatically
            console.log('Submitting form now...');

            // Log all form data for debugging
            const formData = new FormData(this);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            console.log('Form data being submitted:', formDataObj);

            this.submit();
            console.log('Form submitted');
            
            // If this is a transfer operation, set a flag in local storage for dashboard refresh
            if (statusValue === 'Transfer-out' || statusValue === 'Transfer-in') {
                localStorage.setItem('bedtrack_transfer_refresh', 'true');
            }
        } else {
            console.log('Form submission prevented due to validation errors');
        }
    });
    
    // Check if the current status is housekeeping and setup auto-refresh
    if ('{{ $bed->status }}' === 'Housekeeping' && '{{ $bed->housekeeping_started_at }}') {
        setupHousekeepingAutoRefresh();
    }
});

// Function to check if housekeeping is complete and refresh the page
function setupHousekeepingAutoRefresh() {
    @if($bed->status === 'Housekeeping' && $bed->housekeeping_started_at)
        @php
            $isTerminalCleaning = str_contains($bed->housekeeping_remarks ?? '', 'Terminal');
            $completionTime = $isTerminalCleaning 
                ? $bed->housekeeping_started_at->addHours(2) 
                : $bed->housekeeping_started_at->addHour();
            $now = \Carbon\Carbon::now();
            $remainingSeconds = max(0, $now->diffInSeconds($completionTime, false));
        @endphp
        
        // If cleaning is already complete, refresh the page once after a short delay
        if ({{ $remainingSeconds <= 0 ? 'true' : 'false' }}) {
            setTimeout(function() {
                window.location.reload();
            }, 5000); // 5 seconds delay
        } else {
            // Otherwise, set a timer to refresh when cleaning completes
            console.log('Housekeeping in progress, {{ $remainingSeconds }} seconds remaining');
            setTimeout(function() {
                window.location.reload();
            }, {{ $remainingSeconds * 1000 + 2000 }}); // Convert to milliseconds and add 2 second buffer
        }
    @endif
}

// Function to quickly select housekeeping status from available bed
function selectHousekeeping() {
    console.log("Selecting housekeeping status");
    
    // Set the status dropdown to housekeeping
    const statusDropdown = document.getElementById('status');
    if (statusDropdown) {
        statusDropdown.value = 'Housekeeping';
        
        // Trigger the change event to update the form
        togglePatientFields('Housekeeping');
        
        // Scroll to the housekeeping section
        const housekeepingSection = document.getElementById('housekeepingSection');
        if (housekeepingSection) {
            // Ensure it's visible
            housekeepingSection.classList.remove('hidden');
            
            // Set the remarks to Special Maintenance
            const remarksSelect = document.getElementById('housekeeping_remarks');
            if (remarksSelect) {
                remarksSelect.value = 'Special Maintenance (Manual) - Requires staff check';
            }
            
            // Flash the housekeeping section to draw attention
            housekeepingSection.classList.add('bg-blue-100');
            setTimeout(function() {
                housekeepingSection.classList.remove('bg-blue-100');
                housekeepingSection.classList.add('bg-gray-50');
            }, 1000);
            
            // Scroll to section
            setTimeout(function() {
                housekeepingSection.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }
}
</script>
@endsection