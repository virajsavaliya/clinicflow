/**
 * Global app base JS (layout script moved out of Blade)
 */

(function() {
    // Configure Axios with CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken && window.axios) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }

    // Global notification system
    window.showNotification = function(message, type = 'info', duration = 4000) {
        if (!message) return;
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
            max-width: 400px;
            font-weight: 500;
            font-size: 0.875rem;
        `;

        notification.innerHTML = `
            <span style="font-size: 1.25rem;">${icons[type] || icons.info}</span>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; color: white; font-size: 1.25rem; cursor: pointer; padding: 0;">&times;</button>
        `;

        document.body.appendChild(notification);

        if (duration > 0) {
            setTimeout(() => notification.remove(), duration);
        }
    };

    // Alias for compatibility
    window.showGlobalNotification = window.showNotification;

    // Animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    // Flash messages (from JSON script tag)
    const flashEl = document.getElementById('app-flash-data');
    if (flashEl) {
        try {
            const flash = JSON.parse(flashEl.textContent || '{}');
            if (flash.success) window.showNotification(flash.success, 'success');
            if (flash.error) window.showNotification(flash.error, 'error');
            if (flash.validation) window.showNotification(flash.validation, 'error');
        } catch (e) {
            console.warn('Failed to parse flash data', e);
        }
    }
})();
