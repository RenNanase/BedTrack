<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nursery Ward') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Room Header -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">Nursery Room</h3>
                        <p class="text-gray-600">Capacity: {{ count($cribs) }} Cribs</p>
                    </div>

                    <!-- Cribs Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                        @foreach($cribs as $crib)
                            <div class="bg-white rounded-lg shadow-lg p-6 border-2 transform transition-all duration-200 hover:scale-105
                                @if($crib->status === 'Available') border-green-500
                                @elseif($crib->status === 'Occupied') border-red-500
                                @elseif($crib->status === 'Booked') border-yellow-500
                                @else border-gray-300 @endif">

                                <div class="flex flex-col h-full">
                                    <!-- Crib Header -->
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-xl font-semibold">{{ $crib->bed_number }}</h3>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            @if($crib->status === 'Available') bg-green-100 text-green-800
                                            @elseif($crib->status === 'Occupied') bg-red-100 text-red-800
                                            @elseif($crib->status === 'Booked') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $crib->status }}
                                        </span>
                                    </div>

                                    <!-- Patient Info -->
                                    @if($crib->patient_name)
                                        <div class="mb-4 flex-grow">
                                            <p class="text-sm text-gray-600">Patient:</p>
                                            <p class="font-medium">{{ $crib->patient_name }}</p>
                                            @if($crib->patient_info)
                                                <p class="text-sm text-gray-500 mt-1">{{ $crib->patient_info }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex-grow"></div>
                                    @endif

                                    <!-- Action Button -->
                                    <div class="mt-4">
                                        <a href="{{ route('beds.show', $crib) }}"
                                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
