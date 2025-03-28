@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-primary p-6">
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="px-8 pt-8 pb-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">BedTrack</h2>
            <p class="text-center text-gray-500 mb-8">Bed Management System</p>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded bg-red-50 border border-red-200">
                    <ul class="list-disc pl-5 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div class="mb-6">
                    <label for="ward_id" class="block text-sm font-medium text-gray-700 mb-2">Select Ward <span class="text-xs text-gray-500">(Optional for admin)</span></label>
                    <select id="ward_id" name="ward_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select a ward...</option>
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}">{{ $ward->ward_name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Admin users can access all wards from the admin dashboard.</p>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full bg-accent hover:bg-accent/90 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                        Sign In
                    </button>
                </div>
            </form>
        </div>

        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-center">
            <p class="text-xs text-gray-500">© {{ date('Y') }} BedTrack. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection
