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
                        <form action="{{ route('beds.update-status', $bed) }}" method="POST" id="bedStatusForm" onsubmit="return validateForm()">
                            @csrf
                            @method('PUT')

                            <div class="mb-4 bg-blue-50 p-3 rounded-md text-sm text-blue-800">
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    Fields marked with <span class="text-red-500 mx-1">*</span> are required when registering a patient.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" onchange="togglePatientFields(this.value)">
                                    <option value="Available" {{ $bed->status == 'Available' ? 'selected' : '' }}>Available</option>
                                    <option value="Booked" {{ $bed->status == 'Booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="Occupied" {{ $bed->status == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="Discharged" {{ $bed->status == 'Discharged' ? 'selected' : '' }}>Discharge Patient</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="flex items-center mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        When you select "Discharge Patient", the patient will be discharged and the bed will automatically become available.
                                    </span>
                                </p>
                            </div>

                            <div id="patient-fields" class="{{ $bed->status == 'Available' ? 'hidden' : '' }}">
                                <div class="mb-4">
                                    <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Patient Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="patient_name" id="patient_name" value="{{ $bed->patient_name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="mb-4">
                                    <label for="patient_category" class="block text-sm font-medium text-gray-700 mb-2">
                                        Patient Category <span class="text-red-500">*</span>
                                    </label>
                                    <select name="patient_category" id="patient_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                        <option value="">Select category...</option>
                                        <option value="Adult" {{ $bed->patient_category == 'Adult' ? 'selected' : '' }}>Adult</option>
                                        <option value="Paediatric" {{ $bed->patient_category == 'Paediatric' ? 'selected' : '' }}>Paediatric</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select name="gender" id="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                        <option value="">Select gender...</option>
                                        <option value="Male" {{ $bed->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $bed->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="mrn" class="block text-sm font-medium text-gray-700 mb-2">
                                        MRN (Medical Record Number) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="mrn" id="mrn" value="{{ $bed->mrn }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">{{ $bed->notes }}</textarea>
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
                @if($bed->status != 'Available')
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
function togglePatientFields(status) {
    const patientFields = document.getElementById('patient-fields');
    if (status === 'Available') {
        patientFields.classList.add('hidden');
    } else {
        patientFields.classList.remove('hidden');
    }
}

function validateForm() {
    const statusValue = document.getElementById('status').value;

    // Skip validation if bed is being marked as Available
    if (statusValue === 'Available') {
        return true;
    }

    // Required fields for patient registration
    const requiredFields = [
        { id: 'patient_name', label: 'Patient Name' },
        { id: 'patient_category', label: 'Patient Category' },
        { id: 'gender', label: 'Gender' },
        { id: 'mrn', label: 'MRN (Medical Record Number)' }
    ];

    let isValid = true;
    let errorMessage = 'Please fill in the following required fields:\n';

    // Check each required field
    requiredFields.forEach(field => {
        const input = document.getElementById(field.id);
        const value = input.value.trim();

        if (!value) {
            errorMessage += `- ${field.label}\n`;
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });

    if (!isValid) {
        alert(errorMessage);
    }

    return isValid;
}
</script>
@endsection
