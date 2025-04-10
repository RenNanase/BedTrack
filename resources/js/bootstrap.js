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
    const appId = import.meta.env.VITE_PUSHER_APP_ID || '1965625';
    
    console.log('Initializing Echo with key:', appKey, 'cluster:', cluster, 'appId:', appId);
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: appKey,
        cluster: cluster,
        forceTLS: true,
        wsHost: 'ws-' + cluster + '.pusher.com',
        wsPort: 443,
        wssPort: 443,
        authEndpoint: window.location.origin + '/broadcasting/auth',
        csrfToken: token ? token.content : '',
        auth: {
            headers: {
                'X-CSRF-TOKEN': token ? token.content : '',
            },
        }
    });
    
    console.log('Echo initialized successfully');
} catch (e) {
    console.error('Error initializing Echo:', e);
}
