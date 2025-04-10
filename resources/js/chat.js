let currentReplyTo = null;

function setupReplyButton(messageId) {
    const replyButton = document.querySelector(`#reply-button-${messageId}`);
    if (replyButton) {
        replyButton.addEventListener('click', () => {
            const messageElement = document.querySelector(`#message-${messageId}`);
            const messageText = messageElement.querySelector('.message-text').textContent;
            const userName = messageElement.querySelector('.message-sender').textContent;
            
            replyToMessage(messageId, userName, messageText);
        });
    }
}

function replyToMessage(messageId, userName, messageText) {
    currentReplyTo = messageId;
    document.getElementById('reply-to-id').value = messageId;
    document.getElementById('reply-to-name').textContent = userName;
    document.getElementById('reply-to-message').textContent = messageText;
    document.getElementById('reply-indicator').classList.remove('hidden');
    document.getElementById('message-input').focus();
}

function clearReply() {
    currentReplyTo = null;
    document.getElementById('reply-to-id').value = '';
    document.getElementById('reply-to-name').textContent = '';
    document.getElementById('reply-to-message').textContent = '';
    document.getElementById('reply-indicator').classList.add('hidden');
}

document.getElementById('cancel-reply').addEventListener('click', clearReply);

// Handle form submission
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;

    const formData = new FormData(this);
    formData.append('message', message);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    if (currentReplyTo) {
        formData.append('reply_to_id', currentReplyTo);
    }

    fetch('{{ route("chat-messages.store") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            clearReply();
            appendMessage(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

function appendMessage(message) {
    const messagesContainer = document.getElementById('messages');
    const messageElement = document.createElement('div');
    messageElement.className = `message ${message.user_id === userId ? 'sent' : 'received'}`;
    messageElement.id = `message-${message.id}`;
    
    let replyHtml = '';
    if (message.reply_to) {
        replyHtml = `
            <div class="message-reply">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="reply-to">Replying to ${message.reply_to.user.name}: ${message.reply_to.message}</span>
                </div>
            </div>
        `;
    }

    messageElement.innerHTML = `
        <div class="message-content">
            <div class="message-header">
                <span class="message-sender">${message.user.name}</span>
                <span class="message-time">${new Date(message.created_at).toLocaleTimeString()}</span>
            </div>
            ${replyHtml}
            <div class="message-text">${message.message}</div>
        </div>
        <button id="reply-button-${message.id}" class="reply-button" onclick="replyToMessage(${message.id}, '${message.user.name}', '${message.message}')">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Reply
        </button>
    `;

    messagesContainer.appendChild(messageElement);
    setupReplyButton(message.id);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Initialize reply buttons for existing messages
document.querySelectorAll('.reply-button').forEach(button => {
    const messageId = button.id.split('-')[2];
    setupReplyButton(messageId);
}); 