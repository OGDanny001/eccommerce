    </main>
    <script>
        // Global notification function for Admin
        function showNotification(message, isError = false) {
            const notification = document.createElement('div');
            notification.className = 'toast-notification';
            notification.style.backgroundColor = isError ? '#dc2626' : 'var(--primary-color)';
            notification.innerHTML = `<i class="fas fa-${isError ? 'exclamation-circle' : 'check-circle'}"></i> ${message}`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
