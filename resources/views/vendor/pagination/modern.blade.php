@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 py-3">
        <div class="text-sm text-gray-500">
            {{ __('Showing') }} {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} {{ __('of') }} {{ $paginator->total() }} {{ __('users') }}
        </div>
        
        <div class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 cursor-not-allowed" aria-disabled="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="flex items-center justify-center w-9 h-9 rounded-full text-gray-700 hover:bg-primary/10 hover:text-primary transition-colors" rel="prev">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="flex items-center justify-center w-9 h-9" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="flex items-center justify-center w-9 h-9 rounded-full bg-primary text-white font-medium" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex items-center justify-center w-9 h-9 rounded-full text-gray-700 hover:bg-primary/10 hover:text-primary transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="flex items-center justify-center w-9 h-9 rounded-full text-gray-700 hover:bg-primary/10 hover:text-primary transition-colors" rel="next">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 cursor-not-allowed" aria-disabled="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif 