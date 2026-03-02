import Echo from 'laravel-echo';

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

        window.Laravel = { userId };

        const echoInstance = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],

            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }
        });

        window.Echo = echoInstance;

        // Ждем подключения через событие Echo
        echoInstance.connector.pusher.connection.bind('connected', function () {
            console.log('✅ Connected to Reverb');

            const channel = echoInstance.private(`user.${userId}`);

            channel.listen('.task.assigned', (e) => {
                console.log('📨 Task assigned:', e);

                // Триггерим обновление Livewire компонентов
                if (window.Livewire) {
                    Livewire.dispatch('notificationReceived');
                }

                // Браузерное уведомление
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

        echoInstance.connector.pusher.connection.bind('error', function (error) {
            console.error('❌ Reverb connection error:', error);
        });

    } catch (e) {
        console.error('❌ Echo initialization error:', e);
    }
}

// Запускаем после загрузки страницы
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEcho);
} else {
    initEcho();
}