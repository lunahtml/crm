    <!-- Echo and notifications -->
    <script>
        window.userId = {{ auth()->id() ?? 0 }};
    </script>
@vite(['resources/js/app.js'], 'build')
@vite(['resources/js/app.js'])

    <script>
        if (window.userId) {
            // Ждем загрузки Echo
            setTimeout(() => {
                if (window.initializeEcho) {
                    window.initializeEcho(window.userId);
                }
            }, 1000);
        }
    </script>
</body>
</html>


