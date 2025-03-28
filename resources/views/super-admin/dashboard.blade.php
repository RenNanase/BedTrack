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
                    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                        Add New User
                    </a>
                </div>
                <div class="space-y-4">
                    <p class="text-gray-600">Manage system users, their roles, and ward assignments.</p>
                    <a href="{{ route('users.index') }}" class="block text-primary hover:text-primary/80">
                        View All Users →
                    </a>
                </div>
            </div>

            <!-- Ward Management Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Ward Management</h2>
                    <a href="{{ route('super-admin.ward-management') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
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
