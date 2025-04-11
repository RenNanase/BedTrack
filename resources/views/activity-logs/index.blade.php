@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Activity Logs</h1>
                <p class="text-sm text-gray-500">Track all system activities and changes</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-primary/5">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activities</h2>
                    <div class="flex space-x-2">
                        <select id="filter-action" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                            <option value="">All Actions</option>
                            <option value="register">Register</option>
                            <option value="transfer">Transfer</option>
                            <option value="discharge">Discharge</option>
                            <option value="block">Block</option>
                            <option value="unblock">Unblock</option>
                        </select>
                        <select id="filter-ward" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                            <option value="">All Wards</option>
                            @foreach($wards as $ward)
                                <option value="{{ $ward->id }}">{{ $ward->ward_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-4">
                @if(count($activityLogs) > 0)
                <div class="relative" id="activity-container">
                    @include('partials.activity-logs', ['activityLogs' => $activityLogs])

                    @if($hasMoreLogs)
                    <div class="text-center mt-6">
                        <button id="load-more-btn"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors"
                            data-offset="5">
                            Load More
                        </button>
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p>No activity logs found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const filterAction = document.getElementById('filter-action');
    const filterWard = document.getElementById('filter-ward');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const offset = parseInt(this.getAttribute('data-offset'));
            const limit = 5;
            const action = filterAction.value;
            const ward = filterWard.value;

            // Show loading state
            this.innerHTML = 'Loading...';
            this.disabled = true;

            // Make AJAX request to load more logs
            fetch(`/BedTrack/public/activity-logs/load-more?offset=${offset}&limit=${limit}&action=${action}&ward=${ward}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Append new logs to the container
                const activityContainer = document.getElementById('activity-container');
                activityContainer.insertAdjacentHTML('beforeend', data.html);

                // Update button state
                this.innerHTML = 'Load More';
                this.disabled = false;

                // Update offset for next request
                this.setAttribute('data-offset', offset + limit);

                // Hide button if no more logs
                if (!data.hasMore) {
                    this.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading more logs:', error);
                this.innerHTML = 'Error loading more logs. Try again.';
                this.disabled = false;
            });
        });
    }

    // Add event listeners for filters
    [filterAction, filterWard].forEach(filter => {
        filter.addEventListener('change', function() {
            const action = filterAction.value;
            const ward = filterWard.value;
            
            // Reset offset
            if (loadMoreBtn) {
                loadMoreBtn.setAttribute('data-offset', '5');
                loadMoreBtn.style.display = 'block';
            }

            // Make AJAX request to filter logs
            fetch(`/BedTrack/public/activity-logs/load-more?offset=0&limit=5&action=${action}&ward=${ward}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const activityContainer = document.getElementById('activity-container');
                activityContainer.innerHTML = data.html;
            })
            .catch(error => {
                console.error('Error filtering logs:', error);
            });
        });
    });
});
</script>
@endsection 