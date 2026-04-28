// AlumniConnect - Main JavaScript

// Auto-dismiss alerts after 4 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert.auto-dismiss');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

// Password confirmation check
function checkPasswordMatch() {
    const pw = document.getElementById('password');
    const pw2 = document.getElementById('password2');
    const hint = document.getElementById('pw-match-hint');
    if (!pw || !pw2) return;
    if (pw2.value.length > 0) {
        if (pw.value === pw2.value) {
            hint.textContent = '✓ Passwords match';
            hint.className = 'form-text text-success';
        } else {
            hint.textContent = '✗ Passwords do not match';
            hint.className = 'form-text text-danger';
        }
    }
}

// Confirm delete
function confirmDelete(msg) {
    return confirm(msg || 'Are you sure you want to delete this?');
}
