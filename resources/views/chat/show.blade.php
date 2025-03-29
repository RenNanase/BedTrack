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
        <div id="chat-messages" class="h-[500px] overflow-y-auto p-4">
            @foreach($messages as $message)
                <div class="message-container border-b border-gray-100 py-3 last:border-0">
                    <div class="flex items-start space-x-3">
                        <!-- User Avatar/Initials -->
                        <div class="w-8 h-8 rounded-full {{ $message->user_id === Auth::id() ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center font-semibold">
                            {{ substr($message->user->name, 0, 1) }}
                        </div>

                        <div class="flex-1">
                            <!-- User Info and Timestamp -->
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-gray-900">{{ $message->user->name }}</span>
                                <span class="text-xs text-gray-500">{{ $message->created_at->setTimezone('Asia/Singapore')->format('h:i A') }}</span>
                            </div>

                            <!-- Message Content -->
                            <div class="{{ $message->user_id === Auth::id() ? 'bg-blue-50' : 'bg-gray-50' }} rounded-lg px-4 py-2">
                                {{ $message->message }}
                            </div>
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
                       class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Type your message...">
                <button type="submit"
                        class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
            const currentTime = new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: 'Asia/Singapore'
            });
            const messageHtml = `
                <div class="message-container border-b border-gray-100 py-3 last:border-0">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-semibold">
                            ${data.user.name.charAt(0)}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-gray-900">${data.user.name}</span>
                                <span class="text-xs text-gray-500">${currentTime}</span>
                            </div>
                            <div class="bg-blue-50 rounded-lg px-4 py-2">
                                ${data.message}
                            </div>
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
                const currentTime = new Date().toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: true,
                    timeZone: 'Asia/Singapore'
                });
                const messageHtml = `
                    <div class="message-container border-b border-gray-100 py-3 last:border-0">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-semibold">
                                ${message.user.name.charAt(0)}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900">${message.user.name}</span>
                                    <span class="text-xs text-gray-500">${currentTime}</span>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-2">
                                    ${message.message}
                                </div>
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
