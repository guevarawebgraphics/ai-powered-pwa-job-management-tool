import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Attach Pusher to window (required for Echo to work)
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,  // Use the correct key from .env
    wsHost: import.meta.env.VITE_REVERB_HOST, 
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],  // Allow WebSocket transports
});
