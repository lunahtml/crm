import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

async function initEcho() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }

        const res = await fetch('/api/current-user', {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        if (!res.ok) throw new Error(`API error: ${res.status}`);

        const data = await res.json();
        const userId = Number(data.id);

        if (!userId) {
            console.error('No valid user ID');
            return;
        }

        console.log('User authenticated:', { userId, name: data.name, email: data.email });

        window.Laravel = {
            userId: userId,
            user: data
        };

        const echoInstance = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            wsHost: 'ws-eu.pusher.com',
            wsPort: 443,
            wssPort: 443,
            forceTLS: true,
            encrypted: true,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            logToConsole: true,

            // Важно: используем правильный эндпоинт
            authEndpoint: '/broadcasting/auth', // или '/broadcasting/auth' без /api
            // authEndpoint: '/api/broadcasting/auth', // если добавите /api позже

            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            },

            authCallback: (data, callback) => {
                console.log('Auth response:', data);
                callback(false, data);
            }
        });

        window.Echo = echoInstance;

        echoInstance.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Connected to Pusher');

            const channel = echoInstance.private(`user.${userId}`);

            channel.listen('.task.assigned', (e) => {
                console.log('📨 Task assigned event:', e);

                if (Notification.permission === 'granted') {
                    new Notification('Новая задача!', {
                        body: `${e.title}${e.project ? ` в проекте ${e.project}` : ''}`,
                        icon: '/favicon.ico'
                    });
                }
            });

            channel.subscribe(() => {
                console.log(`✅ Subscribed to user.${userId}`);
            });
        });

        echoInstance.connector.pusher.connection.bind('error', (error) => {
            console.error('❌ Pusher connection error:', error);
        });

    } catch (e) {
        console.error('❌ Echo initialization error:', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEcho);
} else {
    initEcho();
}

window.Echo.connector.pusher.connection.bind('message', (data) => {
    console.log('📨 Pusher message received:', data);
});