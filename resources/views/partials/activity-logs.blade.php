<!-- Timeline line -->
<div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200"></div>

<!-- Timeline items -->
<div class="space-y-6 relative">
    @foreach ($activityLogs as $log)
        @include('partials._activity_log_item', ['log' => $log])
    @endforeach
</div>
