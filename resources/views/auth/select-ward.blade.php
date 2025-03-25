@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-secondary p-6">
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="px-8 pt-8 pb-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Select a Ward</h2>
            <p class="text-center text-gray-500 mb-8">Choose the ward you want to work with</p>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded bg-red-50 border border-red-200">
                    <ul class="list-disc pl-5 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('select.ward') }}">
                @csrf

                <div class="mb-6">
                    <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Select Ward</label>
                    <select id="ward_id" name="ward_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select a ward...</option>
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}">{{ $ward->ward_name }} - {{ $ward->room_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
