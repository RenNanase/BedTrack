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
                <div class="message-container border-b border-gray-100 py-3 last:border-0" data-message-id="{{ $message->id }}">
                    <div class="flex items-start space-x-3">
                        <!-- User Avatar/Initials -->
                        <div class="w-8 h-8 rounded-full {{ $message->user_id === Auth::id() ? 'bg-[#D8A48F] text-[#666749]' : 'bg-[#EFE8CE] text-[#666749]' }} flex items-center justify-center font-semibold">
                            {{ substr($message->user->name, 0, 1) }}
                        </div>

                        <div class="flex-1">
                            <!-- User Info and Timestamp -->
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-gray-900">{{ $message->user->name }}</span>
                                <span class="text-xs text-gray-500">{{ $message->created_at->setTimezone('Asia/Singapore')->format('h:i A') }}</span>
                            </div>

                            <!-- Reply Reference (if this message is a reply) -->
                            @if($message->reply_to_id)
                                <div class="reply-reference mb-1 text-xs text-gray-500">
                                    <div class="flex items-center pl-2 border-l-2 border-gray-300 cursor-pointer hover:bg-gray-100 rounded" onclick="scrollToMessage('{{ $message->reply_to_id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 transform rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span>
                                            Replying to 
                                            <span class="font-medium">
                                                {{ $message->replyTo->user->name }}
                                            </span>
                                            <span class="text-gray-600">"{{ $message->replyTo->message }}"</span>
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Message Content -->
                            <div class="{{ $message->user_id === Auth::id() ? 'bg-[#D8A48F] text-[#666749]' : 'bg-[#EFE8CE] text-[#666749]' }} rounded-lg px-4 py-2">
                                {{ $message->message }}
                            </div>

                            <!-- Reply Button -->
                            <div class="mt-1 flex justify-end">
                                <button type="button" class="text-xs text-gray-500 hover:text-gray-700" onclick="replyToMessage('{{ $message->id }}', '{{ $message->user->name }}', `{{ $message->message }}`)">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Reply
                                    </span>
                                </button>
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
            <!-- Reply Preview Container -->
            <div id="reply-container" class="mb-2 px-3 py-2 bg-gray-100 rounded-md hidden">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 mb-1">Replying to <span id="reply-to-name" class="font-semibold"></span></div>
                        <div id="reply-text" class="text-sm text-gray-700 truncate"></div>
                    </div>
                    <button type="button" onclick="cancelReply()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <input type="hidden" name="reply_to_id" id="reply-to-id" value="">
            </div>
            
            <form id="message-form" class="flex space-x-4">
                <input type="hidden" name="chat_room_id" value="{{ $chatRoom->id }}">
                <input type="text"
                       name="message"
                       id="message-input"
                       class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#A3A380]"
                       placeholder="Type your message...">
                <button type="submit"
                        class="bg-[#A3A380] text-white px-6 py-2 rounded-lg hover:bg-[#D8A48F] focus:outline-none focus:ring-2 focus:ring-[#A3A380]">
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

<!-- Real Time Chat using Pusher -->
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
    let replyToId = null;
    
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
    
    // Reply functionality
    function replyToMessage(messageId, userName, messageText) {
        // Set the reply ID
        replyToId = messageId;
        
        // Show the reply container
        const replyContainer = document.getElementById('reply-container');
        const replyToName = document.getElementById('reply-to-name');
        const replyText = document.getElementById('reply-text');
        const replyToIdInput = document.getElementById('reply-to-id');
        
        replyToName.textContent = userName;
        
        // Truncate message if it's too long
        if (messageText.length > 60) {
            replyText.textContent = messageText.substring(0, 60) + '...';
        } else {
            replyText.textContent = messageText;
        }
        
        replyToIdInput.value = messageId;
        replyContainer.classList.remove('hidden');
        
        // Focus the input field
        messageInput.focus();
    }
    
    function cancelReply() {
        // Clear the reply ID
        replyToId = null;
        
        // Hide the reply container
        const replyContainer = document.getElementById('reply-container');
        const replyToIdInput = document.getElementById('reply-to-id');
        
        replyToIdInput.value = '';
        replyContainer.classList.add('hidden');
        
        // Focus the input field
        messageInput.focus();
    }
    
    // Initialize pusher right away
    function initPusher() {
        try {
            // Initialize Pusher directly with improved configuration
            pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true,
                wsHost: '{{ env('PUSHER_HOST', 'ws-'.env('PUSHER_APP_CLUSTER').'.pusher.com') }}',
                wsPort: {{ env('PUSHER_PORT', 443) }},
                wssPort: {{ env('PUSHER_PORT', 443) }},
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                },
                authEndpoint: '{{ url('/broadcasting/auth') }}',
                enableLogging: true
            });
            
            setDebugStatus('Pusher initialized directly - ' + new Date().toLocaleTimeString());
            
            // Connection status events
            pusher.connection.bind('connecting', function() {
                setDebugStatus('Connecting to chat server... - ' + new Date().toLocaleTimeString());
            });
            
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
                        console.error('Channel subscription error:', status);
                        
                        // Attempt to resubscribe
                        setTimeout(function() {
                            setDebugStatus('Attempting to resubscribe to channel...');
                            if (channel) {
                                pusher.unsubscribe('chat.{{ $chatRoom->id }}');
                            }
                            channel = pusher.subscribe('chat.{{ $chatRoom->id }}');
                        }, 5000);
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
                setDebugStatus('Connection error: ' + (err.message || JSON.stringify(err)) + ' - ' + new Date().toLocaleTimeString());
                console.error('Pusher connection error:', err);
                
                // Try to reconnect after error with exponential backoff
                setTimeout(function() {
                    setDebugStatus('Attempting to reconnect...');
                    pusher.connect();
                }, 3000);
            });
            
            // Catch all socket errors
            window.addEventListener('error', function(e) {
                if (e.message && e.message.includes('WebSocket')) {
                    console.error('WebSocket error caught:', e);
                    setDebugStatus('WebSocket error: ' + e.message);
                }
            });
            
            return true;
        } catch (error) {
            setDebugStatus('Error initializing Pusher: ' + error.message);
            console.error('Pusher initialization error:', error);
            
            // Attempt to recover by trying again after a delay
            setTimeout(function() {
                setDebugStatus('Retrying Pusher initialization...');
                initPusher();
            }, 5000);
            
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
            
            // Create reply reference HTML if this is a reply
            let replyReferenceHtml = '';
            if (message.reply_to_id) {
                const replyToName = message.reply_to_user ? message.reply_to_user.name : 'Unknown';
                const replyToMessage = message.reply_to_message ? message.reply_to_message : '';
                replyReferenceHtml = `
                    <div class="reply-reference mb-1 text-xs text-gray-500">
                        <div class="flex items-center pl-2 border-l-2 border-gray-300 cursor-pointer hover:bg-gray-100 rounded" onclick="scrollToMessage('${message.reply_to_id}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 transform rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>
                                Replying to 
                                <span class="font-medium">${replyToName}</span>
                                <span class="text-gray-600">"${replyToMessage}"</span>
                            </span>
                        </div>
                    </div>
                `;
            }
            
            const messageHtml = `
                <div class="message-container border-b border-gray-100 py-3 last:border-0" data-message-id="${message.id}">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-[#EFE8CE] text-[#666749] flex items-center justify-center font-semibold">
                            ${message.user.name.charAt(0)}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-gray-900">${message.user.name}</span>
                                <span class="text-xs text-gray-500">${currentTime}</span>
                            </div>
                            ${replyReferenceHtml}
                            <div class="bg-[#EFE8CE] text-[#666749] rounded-lg px-4 py-2">
                                ${message.message}
                            </div>
                            <div class="mt-1 flex justify-end">
                                <button type="button" class="text-xs text-gray-500 hover:text-gray-700" onclick="replyToMessage('${message.id}', '${message.user.name}', \`${message.message}\`)">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Reply
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
            
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

        const formData = new FormData();
        formData.append('message', message);
        formData.append('chat_room_id', form.querySelector('input[name="chat_room_id"]').value);
        formData.append('reply_to_id', document.getElementById('reply-to-id').value);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("chat-messages.store", $chatRoom) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            messageInput.value = '';
            
            // Reset reply after sending
            cancelReply();
            
            // Add the message to the chat immediately
            const currentTime = new Date().toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: 'Asia/Singapore'
            });
            
            // Create reply reference HTML if this is a reply
            let replyReferenceHtml = '';
            if (data.reply_to_id) {
                const replyToName = data.reply_to_user ? data.reply_to_user.name : 'Unknown';
                const replyToMessage = data.reply_to_message ? data.reply_to_message : '';
                replyReferenceHtml = `
                    <div class="reply-reference mb-1 text-xs text-gray-500">
                        <div class="flex items-center pl-2 border-l-2 border-gray-300 cursor-pointer hover:bg-gray-100 rounded" onclick="scrollToMessage('${data.reply_to_id}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 transform rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>
                                Replying to 
                                <span class="font-medium">${replyToName}</span>
                                <span class="text-gray-600">"${replyToMessage}"</span>
                            </span>
                        </div>
                    </div>
                `;
            }
            
            const messageHtml = `
                <div class="message-container border-b border-gray-100 py-3 last:border-0" data-message-id="${data.id}">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-[#D8A48F] text-[#666749] flex items-center justify-center font-semibold">
                            ${data.user.name.charAt(0)}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-gray-900">${data.user.name}</span>
                                <span class="text-xs text-gray-500">${currentTime}</span>
                            </div>
                            ${replyReferenceHtml}
                            <div class="bg-[#D8A48F] text-[#666749] rounded-lg px-4 py-2">
                                ${data.message}
                            </div>
                            <div class="mt-1 flex justify-end">
                                <button type="button" class="text-xs text-gray-500 hover:text-gray-700" onclick="replyToMessage('${data.id}', '${data.user.name}', \`${data.message}\`)">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Reply
                                    </span>
                                </button>
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

    // Add scroll to message function
    function scrollToMessage(messageId) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            // Add highlight class
            messageElement.classList.add('bg-yellow-50');
            
            // Scroll to message
            messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Remove highlight after 2 seconds
            setTimeout(() => {
                messageElement.classList.remove('bg-yellow-50');
            }, 2000);
        }
    }

    // Update the initial messages to include message IDs
    document.querySelectorAll('.message-container').forEach(container => {
        const messageId = container.getAttribute('data-message-id');
        if (messageId) {
            container.setAttribute('data-message-id', messageId);
        }
    });
</script>
@endpush
@endsection
