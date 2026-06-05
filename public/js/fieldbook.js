// FieldBook — Vanilla JS Utilities

// Format VND
function formatVND(n) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(n);
}

// Countdown timer
function startCountdown(elementId, seconds) {
    const el = document.getElementById(elementId);
    if (!el) return;
    let remaining = seconds;
    const interval = setInterval(() => {
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        el.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        if (remaining <= 0) { clearInterval(interval); el.textContent = 'Hết hạn'; }
        remaining--;
    }, 1000);
}

// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebar) sidebar.classList.toggle('show');
}

// Toast notification
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
function createToastContainer() {
    const c = document.createElement('div');
    c.id = 'toast-container';
    c.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;max-width:360px;';
    document.body.appendChild(c);
    return c;
}

// Services calculator
function updateTotal(basePrice) {
    const checks = document.querySelectorAll('.service-check');
    let extras = 0;
    checks.forEach(c => { if (c.checked) extras += parseInt(c.dataset.price || 0); });
    const total = basePrice + extras;
    const el = document.getElementById('total-price');
    if (el) el.textContent = formatVND(total);
    const elExtras = document.getElementById('extras-price');
    if (elExtras) elExtras.textContent = formatVND(extras);
}
