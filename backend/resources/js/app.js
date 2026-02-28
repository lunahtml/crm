//backend/resources/js/app.js
import './bootstrap';
import './echo';

console.log('app.js loaded, window.Laravel.userId =', window.Laravel?.userId);

// Проверяем CSRF токен
console.log('CSRF token =', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

// Если понадобится дополнительная информация о пользователе
console.log('User info:', {
    id: window.Laravel.userId,
    name: window.Laravel.userName,
    email: window.Laravel.userEmail,
});