import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.MIX_REVERB_APP_KEY,
    wsHost: 'terra-crm.ru',
    wsPort: 443,           // or leave it out if using wss on 443
    wssPort: 443,          // ensure TLS is used on port 443
    forceTLS: true,
    disableStats: true,
});