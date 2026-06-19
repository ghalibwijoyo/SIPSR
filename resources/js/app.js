// Bootstrap JS
import * as bootstrap from 'bootstrap';

// Make bootstrap available globally
window.bootstrap = bootstrap;

// ─── Sidebar Toggle (Mobile) ───────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const toggler  = document.getElementById('sidebar-toggler');

    function openSidebar() {
        sidebar?.classList.add('show');
        overlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggler?.addEventListener('click', function () {
        if (sidebar?.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay?.addEventListener('click', closeSidebar);

    // ─── Global Form Submit Loading State ──────────────────
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // Jika form valid, disable tombol submit dan tampilkan loading
            if (this.checkValidity()) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Prevent double submit on btn
                    if (submitBtn.classList.contains('is-loading')) {
                        e.preventDefault();
                        return;
                    }
                    submitBtn.classList.add('is-loading');
                }
            }
        });
    });
});
