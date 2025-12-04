// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');

    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // 5 segundos
    });
});

// Smooth scroll para navegación
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
