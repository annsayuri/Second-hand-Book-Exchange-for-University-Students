// BookBridge Main JavaScript

// Auto hide alerts after 4 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 4000);
});

// Confirm before delete
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}