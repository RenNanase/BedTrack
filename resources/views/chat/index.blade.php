@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Global Chat</h1>

        <!-- All Members -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">All Members</h2>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                    Total: {{ $users->count() + 1 }} members
                </span>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-600">You ({{ auth()->user()->ward->name ?? 'No Ward' }})</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($users as $user)
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $user->role }} ({{ $user->ward->name ?? 'No Ward' }})</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Chat -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold chat-name" data-chat-id="{{ $globalChat->id }}">{{ $globalChat->name }}</h2>
                        <button class="edit-name-btn text-blue-500 hover:text-blue-700" data-chat-id="{{ $globalChat->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <a href="{{ route('chat.show', $globalChat) }}" class="block text-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        Enter Global Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-name-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const chatId = this.dataset.chatId;
                const nameElement = document.querySelector(`.chat-name[data-chat-id="${chatId}"]`);
                const currentName = nameElement.textContent;

                const newName = prompt('Enter new chat name:', currentName);
                if (newName && newName !== currentName) {
                    fetch(`/chat/${chatId}/name`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ name: newName })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            nameElement.textContent = data.name;
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });
</script>
@endpush
@endsection
