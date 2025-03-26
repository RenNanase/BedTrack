@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-primary/5">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Patient Information</h1>
                    <p class="text-sm text-gray-500">{{ $bed->room->ward->ward_name }} - {{ $bed->room->room_name }} - {{ $bed->bed_number }}</p>
                </div>
                <a href="{{ route('beds.show', $bed) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Back to Bed
                </a>
            </div>

            <div class="p-6">
                <form action="{{ route('beds.update-patient', $bed) }}" method="POST" id="patientForm" onsubmit="return validatePatientForm()">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 bg-blue-50 p-3 rounded-md text-sm text-blue-800 mb-6">
                        <p class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Fields marked with <span class="text-red-500 mx-1">*</span> are required for patient information.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Patient Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name', $bed->patient_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('patient_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="patient_category" class="block text-sm font-medium text-gray-700 mb-2">
                            Patient Category <span class="text-red-500">*</span>
                        </label>
                        <select name="patient_category" id="patient_category" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Select category...</option>
                            <option value="Adult" {{ old('patient_category', $bed->patient_category) == 'Adult' ? 'selected' : '' }}>Adult</option>
                            <option value="Paediatric" {{ old('patient_category', $bed->patient_category) == 'Paediatric' ? 'selected' : '' }}>Paediatric</option>
                        </select>
                        @error('patient_category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" id="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Select gender...</option>
                                <option value="Male" {{ old('gender', $bed->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $bed->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mrn" class="block text-sm font-medium text-gray-700 mb-2">
                                MRN <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="mrn" id="mrn" value="{{ old('mrn', $bed->mrn) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            @error('mrn')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">{{ old('notes', $bed->notes) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Include relevant notes like diagnosis, allergies, etc.</p>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('beds.show', $bed) }}" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-md hover:bg-gray-50 transition-colors mr-3">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validatePatientForm() {
    // Required fields for patient information
    const requiredFields = [
        { id: 'patient_name', label: 'Patient Name' },
        { id: 'patient_category', label: 'Patient Category' },
        { id: 'gender', label: 'Gender' },
        { id: 'mrn', label: 'MRN' }
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
