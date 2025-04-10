<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pusher Debug</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
</head>
<body>
    <h1>Pusher Debug</h1>
    <div id="debug-output" style="white-space: pre; background: #eee; padding: 20px; font-family: monospace;"></div>

    <script>
        const log = (message) => {
            console.log(message);
            const debugOutput = document.getElementById('debug-output');
            debugOutput.textContent += message + "\n";
        };

        // Get Pusher configuration values
        const appKey = '{{ env('PUSHER_APP_KEY') }}';
        const appId = '{{ env('PUSHER_APP_ID') }}';
        const cluster = '{{ env('PUSHER_APP_CLUSTER') }}';
        
        log(`Pusher Debug`);
        log(`App Key: ${appKey}`);
        log(`App ID: ${appId}`);
        log(`Cluster: ${cluster}`);
        
        // Default connection options
        const defaultOptions = {
            cluster: cluster,
            forceTLS: true
        };
        
        // Custom connection options
        const customOptions = {
            cluster: cluster,
            forceTLS: true,
            wsHost: `ws-${cluster}.pusher.com`,
            wsPath: '/app',
            wsPort: 443,
            wssPort: 443
        };
        
        log(`\nDefault Connection URL will be:`);
        log(`wss://ws-${cluster}.pusher.com:443/app/${appKey}`);
        
        let socket = null;
        
        function connectWithOptions(options, name) {
            log(`\nTrying to connect with ${name} options...`);
            
            try {
                // Create a custom WebSocket directly to check connection
                const protocol = options.forceTLS ? 'wss' : 'ws';
                const host = options.wsHost || `ws-${options.cluster}.pusher.com`;
                const port = options.forceTLS ? (options.wssPort || 443) : (options.wsPort || 80);
                const path = options.wsPath || '/app';
                
                // Construct the WebSocket URL with parameters
                const url = `${protocol}://${host}:${port}${path}?protocol=7&client=js&version=8.2.0&key=${appKey}`;
                
                log(`Connecting to: ${url}`);
                
                // Close previous connection if exists
                if (socket) {
                    socket.close();
                }
                
                // Open a direct WebSocket connection
                socket = new WebSocket(url);
                
                socket.onopen = function() {
                    log(`✅ Socket connection successful with ${name} options!`);
                };
                
                socket.onerror = function(error) {
                    log(`❌ Socket error with ${name} options: ${JSON.stringify(error)}`);
                };
                
                socket.onclose = function(event) {
                    log(`Socket closed with ${name} options. Code: ${event.code}, Reason: ${event.reason}`);
                };
                
                // Try a Pusher connection
                const pusher = new Pusher(appKey, options);
                
                pusher.connection.bind('connected', () => {
                    log(`✅ Pusher connected successfully with ${name} options!`);
                });
                
                pusher.connection.bind('error', (err) => {
                    log(`❌ Pusher connection error with ${name} options: ${JSON.stringify(err)}`);
                });
            } catch (error) {
                log(`Error creating connection with ${name} options: ${error.message}`);
            }
        }
        
        // Wait for page to load before connecting
        window.addEventListener('load', () => {
            // Try connection with default options
            setTimeout(() => connectWithOptions(defaultOptions, 'default'), 1000);
            
            // Try connection with custom options
            setTimeout(() => connectWithOptions(customOptions, 'custom'), 3000);
        });
    </script>
</body>
</html> 