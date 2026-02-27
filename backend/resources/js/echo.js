console.log('echo.js loaded');
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Запрашиваем разрешение на уведомления
if (Notification.permission !== 'granted') {
    Notification.requestPermission();
}

// Функция для инициализации с ID пользователя
window.initializeEcho = function (userId) {
    if (!userId) return;

    window.Echo.private(`user.${userId}`)
        .listen('.task.assigned', (e) => {
            console.log('New task assigned:', e);

            // Показываем уведомление
            if (Notification.permission === 'granted') {
                new Notification('Новая задача!', {
                    body: `${e.title} в проекте ${e.project}`,
                    icon: '/favicon.ico'
                });
            }

            // Можно добавить toast-уведомление
            if (window.toast) {
                window.toast.success(`Новая задача: ${e.title}`);
            }
        });
}