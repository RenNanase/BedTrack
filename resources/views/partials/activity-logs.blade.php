<!-- Timeline line -->
<div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200"></div>

<!-- Timeline items -->
<div class="space-y-6 relative">
    @foreach ($activityLogs as $log)
    <div class="ml-10 relative">
        <!-- Timeline dot -->
        <div
            class="absolute -left-10 mt-1.5 w-6 h-6 rounded-full flex items-center justify-center
                {{ $log->action == 'Discharged Patient' ? 'bg-blue-100 text-blue-600' :
                   ($log->action == 'Updated Bed Status' ? 'bg-yellow-100 text-yellow-600' :
                    ($log->action == 'Updated Patient Info' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600')) }}">
            @if($log->action == 'Discharged Patient')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            @elseif($log->action == 'Updated Bed Status')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            @elseif($log->action == 'Updated Patient Info')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            @endif
        </div>

        <!-- Content -->
        <div class="mb-1 flex justify-between">
            <h3 class="text-sm font-medium text-gray-900">{{ $log->action }}</h3>
            <time class="text-xs text-gray-500" title="{{ $log->created_at }}">{{
                $log->created_at->diffForHumans() }}</time>
        </div>
        <p class="text-sm text-gray-700">{{ $log->description }}</p>
        <p class="text-xs text-gray-500 mt-1">By {{ $log->user_name }}</p>
    </div>
    @endforeach
</div>
