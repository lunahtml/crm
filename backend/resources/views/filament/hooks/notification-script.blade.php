<script>
document.addEventListener('livewire:init', function () {
    if (window.Echo) {
        window.Echo.private(`user.{{ auth()->id() }}`)
            .listen('.task.assigned', (e) => {
                // Триггерим обновление виджета
                Livewire.dispatch('notificationReceived');
            });
    }
});
</script>