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
                <form action="{{ route('beds.update-patient', $bed) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                        <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name', $bed->patient_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('patient_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="patient_info" class="block text-sm font-medium text-gray-700 mb-2">Patient Information</label>
                        <textarea name="patient_info" id="patient_info" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">{{ old('patient_info', $bed->patient_info) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Include relevant details like patient ID, diagnosis, notes, etc.</p>
                        @error('patient_info')
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
@endsection
