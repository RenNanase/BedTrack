<div class="ml-10 relative">
    <!-- Timeline dot -->
    <div class="absolute -left-10 mt-1.5 w-6 h-6 rounded-full flex items-center justify-center
        @if(isset($log->action) && $log->action == 'Discharged Patient') bg-blue-100 text-blue-600
        @elseif(isset($log->action) && $log->action == 'Updated Bed Status') bg-yellow-100 text-yellow-600
        @elseif(isset($log->action) && $log->action == 'Updated Patient Info') bg-green-100 text-green-600
        @elseif(isset($log->action) && $log->action == 'Registered Patient') bg-purple-100 text-purple-600
        @elseif(isset($log->description) && (
            str_contains(strtolower($log->description), 'discharged baby') || 
            str_contains(strtolower($log->description), 'discharged from bassinet')
        )) bg-blue-100 text-blue-600
        @elseif(isset($log->description) && (
            str_contains(strtolower($log->description), 'registered baby') || 
            str_contains(strtolower($log->description), 'registered in bassinet')
        )) bg-purple-100 text-purple-600
        @elseif(isset($log->description) && (
            str_contains(strtolower($log->description), 'transferred baby') || 
            str_contains(strtolower($log->description), 'transferred from bassinet') ||
            str_contains(strtolower($log->description), 'transferred to')
        )) bg-yellow-100 text-yellow-600
        @elseif(isset($log->log_name) && $log->log_name == 'default') bg-purple-100 text-purple-600
        @else bg-gray-100 text-gray-600
        @endif">
        @if(isset($log->action) && $log->action == 'Discharged Patient' || (isset($log->description) && (
            str_contains(strtolower($log->description), 'discharged baby') || 
            str_contains(strtolower($log->description), 'discharged from bassinet')
        )))
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        @elseif(isset($log->action) && $log->action == 'Updated Bed Status' || (isset($log->description) && (
            str_contains(strtolower($log->description), 'transferred baby') || 
            str_contains(strtolower($log->description), 'transferred from bassinet') ||
            str_contains(strtolower($log->description), 'transferred to')
        )))
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        @elseif(isset($log->action) && $log->action == 'Updated Patient Info')
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        @elseif(isset($log->action) && $log->action == 'Registered Patient' || 
            (isset($log->description) && (
                str_contains(strtolower($log->description), 'registered baby') || 
                str_contains(strtolower($log->description), 'registered in bassinet')
            )) ||
            (isset($log->log_name) && $log->log_name == 'default')
        )
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
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
        <h3 class="text-sm font-medium text-gray-900">
            @if(isset($log->action))
                {{ $log->action }}
            @elseif(isset($log->description) && (
                str_contains(strtolower($log->description), 'discharged baby') || 
                str_contains(strtolower($log->description), 'discharged from bassinet')
            ))
                Discharged Baby
            @elseif(isset($log->description) && (
                str_contains(strtolower($log->description), 'registered baby') || 
                str_contains(strtolower($log->description), 'registered in bassinet')
            ))
                Registered Baby
            @elseif(isset($log->description) && (
                str_contains(strtolower($log->description), 'transferred baby') || 
                str_contains(strtolower($log->description), 'transferred from bassinet') ||
                str_contains(strtolower($log->description), 'transferred to')
            ))
                Transferred Baby
            @elseif(isset($log->log_name) && $log->log_name == 'default' && isset($log->description))
                @if(str_contains(strtolower($log->description), 'transferred'))
                    Transferred Baby
                @elseif(str_contains(strtolower($log->description), 'discharged'))
                    Discharged Baby
                @elseif(str_contains(strtolower($log->description), 'registered'))
                    Registered Baby
                @elseif(str_contains(strtolower($log->description), 'bassinet'))
                    Bassinet Activity
                @elseif(str_contains(strtolower($log->description), 'baby'))
                    Baby Activity
                @else
                    Bassinet Activity
                @endif
            @else
                Bassinet Activity
            @endif
        </h3>
        <time class="text-xs text-gray-500" title="{{ $log->created_at }}">{{
            $log->created_at->diffForHumans() }}</time>
    </div>
    <p class="text-sm text-gray-700">
        @if(isset($log->description))
            {{ $log->description }}
            
            @if(isset($log->log_name) && $log->log_name == 'default' && isset($log->properties) && is_object($log->properties))
                @php
                    $props = json_decode(json_encode($log->properties), true);
                @endphp
                <span class="block mt-1 text-gray-600">
                    @if(isset($props['baby_name']))
                        <strong>Baby:</strong> {{ $props['baby_name'] }}
                    @endif
                    
                    @if(isset($props['from_bassinet']))
                        <strong>Bassinet:</strong> #{{ $props['from_bassinet'] }}
                    @endif
                    
                    @if(isset($props['attributes']['room']['room_name']))
                        <strong>Room:</strong> {{ $props['attributes']['room']['room_name'] }}
                    @elseif(isset($props['room_name']))
                        <strong>Room:</strong> {{ $props['room_name'] }}
                    @endif
                    
                    @if(isset($props['mother_name']))
                        <br><strong>Mother:</strong> {{ $props['mother_name'] }}
                    @endif
                    
                    @if(isset($props['to_crib']))
                        <strong>To Crib:</strong> #{{ $props['to_crib'] }}
                    @endif
                </span>
            @endif
        @elseif(isset($log->properties) && is_object($log->properties))
            @php
                $props = json_decode(json_encode($log->properties), true);
            @endphp
            
            @if(isset($props['baby_name']))
                Activity for baby {{ $props['baby_name'] }}
                <span class="block mt-1 text-gray-600">
                    @if(isset($props['from_bassinet']))
                        <strong>Bassinet:</strong> #{{ $props['from_bassinet'] }}
                    @endif
                    
                    @if(isset($props['attributes']['room']['room_name']))
                        <strong>Room:</strong> {{ $props['attributes']['room']['room_name'] }}
                    @elseif(isset($props['room_name']))
                        <strong>Room:</strong> {{ $props['room_name'] }}
                    @endif
                    
                    @if(isset($props['mother_name']))
                        <br><strong>Mother:</strong> {{ $props['mother_name'] }}
                    @endif
                </span>
            @elseif(isset($log->subject_type) && str_contains($log->subject_type, 'Bassinet'))
                Bassinet activity
                <span class="block mt-1 text-gray-600">
                    @if(isset($props['attributes']['bassinet_number']))
                        <strong>Bassinet:</strong> #{{ $props['attributes']['bassinet_number'] }}
                    @endif
                    
                    @if(isset($props['attributes']['room']['room_name']))
                        <strong>Room:</strong> {{ $props['attributes']['room']['room_name'] }}
                    @endif
                </span>
            @else
                {{ $log->log_name ?? 'Activity log' }}
                @if(isset($props['subject_type']) && str_contains($props['subject_type'], 'Bassinet'))
                    <span class="block mt-1 text-gray-600">
                        <strong>Bassinet activity</strong>
                    </span>
                @endif
            @endif
        @else
            Activity log
        @endif
    </p>
    <p class="text-xs text-gray-500 mt-1">
        @if(isset($log->user_name))
            By {{ $log->user_name }}
        @elseif(isset($log->causer) && $log->causer && isset($log->causer->name))
            By {{ $log->causer->name }}
        @elseif(isset($log->causer_type) && isset($log->causer_id))
            By User #{{ $log->causer_id }}
        @else
            By System
        @endif
    </p>

    @if($log->log_name === 'default' && isset($log->properties))
        @php
            $properties = is_string($log->properties) ? json_decode($log->properties) : $log->properties;
            $babyName = "";
            $bassinetNumber = "";
            $roomName = "";
            $motherName = "";
            
            // Extract data from properties
            if (isset($properties->attributes)) {
                // Get bassinet number
                if (isset($properties->attributes->bassinet_number)) {
                    $bassinetNumber = $properties->attributes->bassinet_number;
                }
                
                // Get room name
                if (isset($properties->attributes->room) && isset($properties->attributes->room->room_name)) {
                    $roomName = $properties->attributes->room->room_name;
                }
                
                // Try to get baby name
                if (isset($properties->attributes->bassinet_baby_name)) {
                    $babyName = $properties->attributes->bassinet_baby_name;
                } elseif (isset($properties->attributes->baby_name)) {
                    $babyName = $properties->attributes->baby_name;
                }
                
                // Try to get mother's name
                if (isset($properties->attributes->mother_name)) {
                    $motherName = $properties->attributes->mother_name;
                }
            }
        @endphp
        
        <div class="text-gray-700 text-sm mt-1">
            @if(!empty($babyName))
                <div class="ml-1 mb-1"><strong>Baby:</strong> {{ $babyName }}</div>
            @endif
            
            @if(!empty($bassinetNumber))
                <div class="ml-1 mb-1"><strong>Bassinet:</strong> {{ $bassinetNumber }}</div>
            @endif
            
            @if(!empty($roomName))
                <div class="ml-1 mb-1"><strong>Room:</strong> {{ $roomName }}</div>
            @endif
            
            @if(!empty($motherName))
                <div class="ml-1 mb-1"><strong>Mother:</strong> {{ $motherName }}</div>
            @endif
        </div>
    @endif
</div> 