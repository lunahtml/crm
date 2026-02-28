import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

console.log('echo.js loaded - API mode');

window.initializeEcho = async function () {
    try {
        const res = await fetch('/api/current-user', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        console.log('API status:', res.status);

        if (!res.ok) throw new Error(`API not ok: ${res.status}`);

        const data = await res.json();
        console.log('API data:', data);

        const userId = Number(data?.id || 0);
        window.Laravel = window.Laravel || {};
        window.Laravel.userId = userId;

        console.log('UserId из API:', window.Laravel.userId);

        if (userId > 0) {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: import.meta.env.VITE_PUSHER_APP_KEY,
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
                forceTLS: true,
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                }
            });

            console.log('Echo создан');

            window.Echo.private(`user.${userId}`)
                .listen('.task.assigned', e => {
                    console.log('✅ New task assigned:', e);
                });
        } else {
            console.warn('❌ userId = 0, Echo не инициализирован');
        }

    } catch (err) {
        console.error('API error:', err); // так будет видно реальное сообщение ошибки
    }
};