@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-gray-100 px-4 py-3 border-b">
            <h2 class="text-xl font-semibold">
                @if($chatRoom->type === 'ward')
                    {{ $chatRoom->ward->name }}
                @else
                    {{ $chatRoom->name }}
                @endif
            </h2>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="h-[500px] overflow-y-auto p-4 space-y-4">
            @foreach($messages as $message)
                <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%] {{ $message->user_id === Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} rounded-lg px-4 py-2">
                        <div class="text-sm font-semibold mb-1">
                            {{ $message->user->name }}
                        </div>
                        <div class="text-sm">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Input -->
        <div class="border-t p-4">
            <form id="message-form" class="flex space-x-4">
                <input type="hidden" name="chat_room_id" value="{{ $chatRoom->id }}">
                <input type="text"
                       name="message"
                       class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Type your message...">
                <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scroll to bottom on page load
    function scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Scroll to bottom when new messages arrive
    window.addEventListener('load', scrollToBottom);

    // Handle message form submission
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const messageInput = form.querySelector('input[name="message"]');
        const message = messageInput.value.trim();

        if (!message) return;

        fetch('{{ route("chat.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                chat_room_id: form.querySelector('input[name="chat_room_id"]').value
            })
        })
        .then(response => response.json())
        .then(data => {
            messageInput.value = '';
            // Add the message to the chat immediately
            const chatMessages = document.getElementById('chat-messages');
            const messageHtml = `
                <div class="flex justify-end">
                    <div class="max-w-[70%] bg-blue-500 text-white rounded-lg px-4 py-2">
                        <div class="text-sm font-semibold mb-1">
                            ${data.user.name}
                        </div>
                        <div class="text-sm">
                            ${data.message}
                        </div>
                    </div>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        })
        .catch(error => console.error('Error:', error));
    });

    // Listen for new messages
    window.Echo.private('chat.{{ $chatRoom->id }}')
        .listen('NewChatMessage', (e) => {
            const message = e.message;
            const chatMessages = document.getElementById('chat-messages');

            // Only add the message if it's from another user
            if (message.user_id !== {{ Auth::id() }}) {
                const messageHtml = `
                    <div class="flex justify-start">
                        <div class="max-w-[70%] bg-gray-200 text-gray-800 rounded-lg px-4 py-2">
                            <div class="text-sm font-semibold mb-1">
                                ${message.user.name}
                            </div>
                            <div class="text-sm">
                                ${message.message}
                            </div>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                scrollToBottom();
            }
        });
</script>
@endpush
@endsection
