<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Nursery Cribs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('room-management.store-nursery-cribs', $ward) }}" class="space-y-6">
                        @csrf

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Cribs to Nursery Room</h3>
                            <p class="text-sm text-gray-600 mb-4">Current capacity: {{ $ward->rooms->first()->capacity }} cribs</p>
                        </div>

                        <div>
                            <label for="number_of_cribs" class="block text-sm font-medium text-gray-700">Number of Cribs to Add</label>
                            <input type="number" name="number_of_cribs" id="number_of_cribs" min="1" max="50"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                required>
                            <p class="mt-1 text-sm text-gray-500">Enter the number of cribs you want to add to the nursery room.</p>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                                Add Cribs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
