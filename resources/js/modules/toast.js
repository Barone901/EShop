/**
 * Toast Module
 *
 * Zobrazuje notifikácie (success, error, info).
 * Vzhľad je v Blade (layouts/partials/toasts, <template data-toast="...">) - modul šablónu
 * naklonuje, doplní text cez textContent (bez HTML, bez rizika XSS) a animuje ju.
 *
 * [AI] Upravené s pomocou AI (Claude).
 */

const ANIMATION_MS = 300;

const Toast = {
    show(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        const template = document.querySelector(`template[data-toast="${type}"]`)
            ?? document.querySelector('template[data-toast="info"]');

        if (!container || !template) return;

        const toast = template.content.firstElementChild.cloneNode(true);
        toast.querySelector('[data-toast-message]').textContent = message;
        container.appendChild(toast);

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
