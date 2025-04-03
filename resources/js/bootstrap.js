import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

window.Pusher = Pusher;

try {
    const appKey = import.meta.env.VITE_PUSHER_APP_KEY;
    const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;
    
    console.log('Initializing Echo with key:', appKey, 'cluster:', cluster);
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: appKey,
        cluster: cluster,
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        csrfToken: token ? token.content : '',
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': token ? token.content : '',
            },
        },
        // Add additional options for CORS and reliability
        wsHost: `ws-${cluster}.pusher.com`,
        wsPort: 443,
        wssPort: 443,
        httpHost: `sockjs-${cluster}.pusher.com`,
        httpPort: 80,
        httpsPort: 443,
        // Disable stats to reduce requests
        enableStats: false,
    });
    
    // Log subscription status for debugging
    const originalPrivate = window.Echo.private;
    window.Echo.private = function(channel) {
        console.log('Subscribing to private channel through Echo:', channel);
        const subscription = originalPrivate.call(this, channel);
        
        // Add hook to check channel subscription status
        const originalListen = subscription.listen;
        subscription.listen = function(event, callback) {
            console.log('Setting up listener for event:', event, 'on channel:', channel);
            return originalListen.call(this, event, callback);
        };
        
        return subscription;
    };
    
    // Add the same for public channels if not already defined
    if (typeof window.Echo.channel === 'function') {
        const originalChannel = window.Echo.channel;
        window.Echo.channel = function(channel) {
            console.log('Subscribing to public channel through Echo:', channel);
            const subscription = originalChannel.call(this, channel);
            
            // Add hook to check channel subscription status
            const originalListen = subscription.listen;
            subscription.listen = function(event, callback) {
                console.log('Setting up listener for event:', event, 'on channel:', channel);
                return originalListen.call(this, event, callback);
            };
            
            return subscription;
        };
    } else {
        // Add channel method if it doesn't exist
        window.Echo.channel = function(channel) {
            console.log('Using custom channel() implementation for:', channel);
            // Just use private channel since it's what we have
            return this.private(channel);
        };
    }
    
    console.log('Echo initialized successfully');
} catch (error) {
    console.error('Failed to initialize Echo:', error);
}
