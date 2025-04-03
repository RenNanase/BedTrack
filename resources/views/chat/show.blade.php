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

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="px-4 py-2 text-sm text-gray-500 italic hidden"></div>

        <!-- Message Input -->
        <div class="border-t p-4">
            <form id="message-form" class="flex space-x-4">
                <input type="hidden" name="chat_room_id" value="{{ $chatRoom->id }}">
                <input type="text"
                       name="message"
                       id="message-input"
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

<!-- Connection Status -->
<div id="debug-status" class="fixed bottom-5 right-5 p-2 text-xs text-white bg-black bg-opacity-70 rounded z-50">
    Initializing...
</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Global variables
    const chatRoomId = {{ $chatRoom->id }};
    const userId = {{ Auth::id() }};
    const username = "{{ Auth::user()->name }}";
    const debugStatus = document.getElementById('debug-status');
    const typingIndicator = document.getElementById('typing-indicator');
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');
    const typingUsers = {};
    let typingTimer;
    let isTyping = false;
    let pusher;
    let channel;
    
    // Helper functions
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function setDebugStatus(message) {
        if (debugStatus) {
            debugStatus.textContent = message;
        }
        console.log(message);
    }
    
    // Initialize pusher right away
    function initPusher() {
        try {
            // Initialize Pusher directly
            pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true,
                enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'],
                disableStats: true,
                // Debug CORS issues
                authEndpoint: '{{ url('/broadcasting/auth') }}',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                }
            });
            
            setDebugStatus('Pusher initialized directly - ' + new Date().toLocaleTimeString());
            
            // Connection status events
            pusher.connection.bind('connected', function() {
                setDebugStatus('Connected to chat server - ' + new Date().toLocaleTimeString());
                
                // Only subscribe to the channel after we're connected to avoid race conditions
                try {
                    // Subscribe to the channel
                    channel = pusher.subscribe('chat.{{ $chatRoom->id }}');
                    
                    // Set up event listeners
                    channel.bind('new-message', function(data) {
                        setDebugStatus('Message event received - ' + new Date().toLocaleTimeString());
                        console.log('Message data:', data);
                        handleNewMessage(data);
                    });
                    
                    channel.bind('user-typing', function(data) {
                        setDebugStatus('Typing event received - ' + new Date().toLocaleTimeString());
                        console.log('Typing data:', data);
                        handleTyping(data);
                    });
                    
                    // Log channel subscription success
                    channel.bind('pusher:subscription_succeeded', function() {
                        setDebugStatus('Successfully subscribed to channel - ' + new Date().toLocaleTimeString());
                    });
                    
                    // Log channel subscription error
                    channel.bind('pusher:subscription_error', function(status) {
                        setDebugStatus('Failed to subscribe to channel: ' + status + ' - ' + new Date().toLocaleTimeString());
                    });
                    
                } catch (channelError) {
                    setDebugStatus('Error subscribing to channel: ' + channelError.message);
                    console.error('Channel subscription error:', channelError);
                }
            });
            
            pusher.connection.bind('disconnected', function() {
                setDebugStatus('Disconnected from chat server - ' + new Date().toLocaleTimeString());
            });
            
            pusher.connection.bind('error', function(err) {
                setDebugStatus('Connection error: ' + err.message + ' - ' + new Date().toLocaleTimeString());
                console.error('Pusher connection error:', err);
                
                // Try to reconnect after error
                setTimeout(function() {
                    setDebugStatus('Attempting to reconnect...');
                    pusher.connect();
                }, 3000);
            });
            
            return true;
        } catch (error) {
            setDebugStatus('Error initializing Pusher: ' + error.message);
            console.error('Pusher initialization error:', error);
            return false;
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initPusher();
        scrollToBottom();
    });
    
    // Handle new messages
    function handleNewMessage(data) {
        setDebugStatus('Message received at ' + new Date().toLocaleTimeString());
        console.log('Received message:', data);
        
        const message = data.message;
        
        // Only add the message if it's from another user
        if (message.user_id !== userId) {
            const currentTime = new Date(message.created_at).toLocaleTimeString('en-US', {
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
            
            // Play notification sound (optional)
            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.play();
            } catch (e) {
                console.log('Sound notification not supported');
            }
            
            // Hide typing indicator when message is received
            if (typingUsers[message.user_id]) {
                delete typingUsers[message.user_id];
                updateTypingIndicator();
            }
        }
    }
    
    // Handle typing events
    function handleTyping(data) {
        setDebugStatus('Typing event received at ' + new Date().toLocaleTimeString());
        console.log('Typing event received:', data);
        
        // Ignore if it's the current user
        if (data.user_id === userId) return;
        
        // Update typing users
        if (data.is_typing) {
            typingUsers[data.user_id] = data.username;
        } else {
            delete typingUsers[data.user_id];
        }
        
        updateTypingIndicator();
    }
    
    // Update typing indicator
    function updateTypingIndicator() {
        const users = Object.values(typingUsers);
        
        if (users.length === 0) {
            typingIndicator.classList.add('hidden');
            typingIndicator.textContent = '';
        } else if (users.length === 1) {
            typingIndicator.classList.remove('hidden');
            typingIndicator.textContent = `${users[0]} is typing...`;
        } else if (users.length === 2) {
            typingIndicator.classList.remove('hidden');
            typingIndicator.textContent = `${users[0]} and ${users[1]} are typing...`;
        } else {
            typingIndicator.classList.remove('hidden');
            typingIndicator.textContent = `${users.length} people are typing...`;
        }
    }
    
    // Send typing status
    function sendTypingStatus(status) {
        if (isTyping !== status) {
            isTyping = status;
            
            fetch('{{ route("chat.typing") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    chat_room_id: chatRoomId,
                    is_typing: status
                })
            })
            .catch(error => {
                console.error('Error sending typing status:', error);
            });
        }
    }
    
    // Handle input typing events
    messageInput.addEventListener('keydown', function() {
        sendTypingStatus(true);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            sendTypingStatus(false);
        }, 3000);
    });
    
    // Handle message form submission
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        // Clear typing status when sending a message
        sendTypingStatus(false);
        clearTimeout(typingTimer);
        
        fetch('{{ route("chat.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                chat_room_id: form.querySelector('input[name="chat_room_id"]').value
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            messageInput.value = '';
            
            // Add the message to the chat immediately
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
            
            setDebugStatus('Message sent at ' + new Date().toLocaleTimeString());
        })
        .catch(error => {
            console.error('Error:', error);
            setDebugStatus('Error sending message: ' + error.message);
            alert('Failed to send message. Please try again.');
        });
    });
</script>
@endpush
@endsection
