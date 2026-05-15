/**
 * Login page flash messages (session + validation).
 * Uses global showToastmessage from crm/common/common.js when available.
 */
import '../crm/common/common.js';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('login-flash-data');
    if (! el?.dataset?.payload) {
        return;
    }

    let payload;
    try {
        payload = JSON.parse(el.dataset.payload);
    } catch {
        return;
    }

    const notify = (message, type = 'success') => {
        if (! message) {
            return;
        }
        if (typeof window.showToastmessage === 'function') {
            window.showToastmessage(message, type);
            return;
        }
        if (typeof window.Toastify === 'function') {
            window.Toastify({
                text: message,
                duration: type === 'error' ? 5000 : 4000,
                close: true,
                gravity: 'top',
                position: 'right',
                backgroundColor: type === 'error' ? 'red' : 'green',
            }).showToast();
        }
    };

    if (payload.success) {
        notify(payload.success, 'success');
    }
    if (payload.error) {
        notify(payload.error, 'error');
    }
    (payload.errors || []).forEach((message) => notify(message, 'error'));
});
