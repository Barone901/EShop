/**
 * Toast Module
 *
 * Zobrazuje notifikácie (success, error, info).
 *
 * [AI] Upravené s pomocou AI (Claude).
 */
import { escapeHtml } from '../utils/escape-html';

const TYPES = {
    success: { bgColor: 'bg-ok-700', icon: '✓' },
    error: { bgColor: 'bg-danger-700', icon: '✗' },
    info: { bgColor: 'bg-ink-900', icon: 'ℹ' },
};

const ANIMATION_MS = 300;

const Toast = {
    container: null,

    init() {
        if (this.container) return;

        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.className = 'fixed bottom-4 left-4 right-4 z-[200] flex flex-col items-end gap-2 sm:left-auto';
        document.body.appendChild(this.container);
    },

    show(message, type = 'success', duration = 3000) {
        this.init();

        const { bgColor, icon } = TYPES[type] ?? TYPES.info;
        const toast = document.createElement('div');

        toast.className = `${bgColor} text-white text-sm font-medium px-4 py-3 rounded-xs border-l-3 border-white/40 shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <span aria-hidden="true">${icon}</span>
                <span>${escapeHtml(message)}</span>
            </div>
        `;

        this.container.appendChild(toast);

        // Animácia zobrazenia
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        // Animácia skrytia a odstránenie
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), ANIMATION_MS);
        }, duration);
    },

    success(message) {
        this.show(message, 'success');
    },

    error(message) {
        this.show(message, 'error');
    },

    info(message) {
        this.show(message, 'info');
    },
};

export default Toast;
