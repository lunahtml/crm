console.log('echo.js loaded - API mode');

fetch('/api/current-user', {
    method: 'GET',
    credentials: 'include',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})
    .then(res => {
        console.log('API status:', res.status); // ← добавили дебаг
        if (!res.ok) {
            throw new Error('API not ok: ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        console.log('API data:', data); // ← добавили дебаг всего ответа
        if (data && data.id) {
            window.Laravel = window.Laravel || {};
            window.Laravel.userId = data.id;
            console.log('UserId из API:', window.Laravel.userId);

            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: import.meta.env.VITE_REVERB_APP_KEY,
                wsHost: import.meta.env.VITE_REVERB_HOST,
                wsPort: import.meta.env.VITE_REVERB_PORT,
                forceTLS: false,
                enabledTransports: ['ws'],
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                }
            });

            console.log('Echo создан');

            window.Echo.private(`user.${data.id}`)
                .listen('.task.assigned', (e) => {
                    console.log('✅ New task assigned:', e);
                    if (Notification.permission === 'granted') {
                        new Notification('Новая задача!', {
                            body: `${e.title} в проекте ${e.project || '—'}`,
                            icon: '/favicon.ico'
                        });
                    }
                });
        } else {
            console.error('Нет userId в data:', data);
        }
    })
    .catch(err => {
        console.error('API error:', err.message);
    });