@extends('layouts.app')

@section('content')
<div class="py-6 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Super Admin Dashboard</h1>
                <p class="text-sm text-gray-500">Manage system-wide settings and users</p>
            </div>
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Management Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">User Management</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors">
                            Edit Users
                        </a>
                        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors">
                            Add User
                        </a>
                    </div>
                </div>
                <div class="space-y-4">
                    <p class="text-gray-600">Manage system users, their roles, and ward assignments.</p>
                </div>
            </div>

            <!-- Ward Management Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Ward Management</h2>
                    <a href="{{ route('super-admin.ward-management') }}" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors">
                        Manage Wards
                    </a>
                </div>
                <div class="space-y-4">
                    <p class="text-gray-600">Manage hospital wards and their configurations.</p>
                    <a href="{{ route('super-admin.ward-management') }}" class="block text-primary hover:text-primary/80">
                        View All Wards →
                    </a>
                </div>
            </div>

            <!-- Bed Management Card -->
            {{-- <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Bed Management</h2>
                    <a href="{{ route('super-admin.ward-management') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                        Manage Beds
                    </a>
                </div>
                <div class="space-y-4">
                    <p class="text-gray-600">Manage hospital beds and their assignments.</p>
                    <a href="{{ route('super-admin.ward-management') }}" class="block text-primary hover:text-primary/80">
                        View All Beds →
                    </a>
                </div>
            </div> --}}
        </div>

        <!-- Quick User Edit Section -->
        <div class="mt-8 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Quick User Edit</h2>
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors">
                    View All Users
                </a>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="mb-4">Enter the name of the user you want to edit:</p>
                <form action="{{ route('users.index') }}" method="GET" class="flex items-end space-x-4" id="quickEditForm">
                    <div class="flex-1">
                        <label for="user_name" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" id="user_name" name="user_name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter username" required>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-500 transition-colors">
                        Find User
                    </button>
                </form>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h2>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4">
                    <div class="space-y-4">
                        @forelse($recentActivities as $activity)
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                                <div>
                                    <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('quickEditForm');
        const userNameInput = document.getElementById('user_name');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const userName = userNameInput.value;
            if (userName) {
                window.location.href = "{{ url('users') }}?search=" + encodeURIComponent(userName);
            }
        });
    });
</script>
@endpush
