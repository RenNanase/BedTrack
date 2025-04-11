<!-- Timeline line -->
<div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200"></div>

<!-- Timeline items -->
<div class="space-y-6 relative">
    @foreach ($activityLogs as $log)
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900">{{ $log->user->name }}</span>
                    <span class="text-sm text-gray-500">•</span>
                    <span class="text-sm text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                <p class="mt-1 text-sm text-gray-700">
                    {{ $log->action }}
                    @if($log->bed)
                        in {{ $log->bed->room->ward->name }} - {{ $log->bed->room->name }} - Bed {{ $log->bed->bed_number }}
                    @endif
                </p>
                @if($log->details)
                    <p class="mt-2 text-sm text-gray-600">{{ $log->details }}</p>
                @endif
            </div>
            <div class="ml-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($log->action === 'Registered')
                        bg-green-100 text-green-800
                    @elseif($log->action === 'Discharged')
                        bg-red-100 text-red-800
                    @elseif($log->action === 'Transferred')
                        bg-blue-100 text-blue-800
                    @elseif($log->action === 'Updated')
                        bg-yellow-100 text-yellow-800
                    @else
                        bg-gray-100 text-gray-800
                    @endif">
                    {{ $log->action }}
                </span>
            </div>
        </div>
    </div>
    @endforeach
</div>
