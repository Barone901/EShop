/**
 * Quick View Module
 *
 * Rýchly náhľad produktu v modálnom okne.
 * Kostra okna je v layoute (layouts/partials/quick-view-modal), obsah vyrenderuje server
 * (Blade komponent x-shop.quick-view). Modul len otvára/zatvára okno a vkladá HTML.
 *
 * [AI] Upravené s pomocou AI (Claude).
 */

import ProductService from '../services/product-service';
import Toast from './toast';
import { byId, delegate, onEscape } from '../utils/dom';

const QuickView = {
    modal: null,
    content: null,
    loading: null,
    closeButton: null,
    // Prvok, ktorý mal fokus pred otvorením (po zatvorení sa naň vráti)
    previousFocus: null,

    init() {
        this.modal = byId('quick-view-modal');
        if (!this.modal) return;

        this.content = byId('quick-view-content');
        this.loading = byId('quick-view-loading');
        this.closeButton = this.modal.querySelector('button[data-close-modal]');

        this.modal.querySelectorAll('[data-close-modal]').forEach((el) => {
            el.addEventListener('click', () => this.close());
        });

        delegate('click', '[data-quick-view]', (e, btn) => {
            e.preventDefault();
            this.open(btn.dataset.quickView);
        });

        onEscape(() => {
            if (this.isOpen()) this.close();
        });
    },

    isOpen() {
        return !this.modal.classList.contains('hidden');
    },

    setLoading(isLoading) {
        this.loading.classList.toggle('hidden', !isLoading);
        this.content.classList.toggle('hidden', isLoading);
    },

    async open(productSlug) {
        this.previousFocus = document.activeElement;
        this.setLoading(true);
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        this.closeButton.focus();

        try {
            const { html } = await ProductService.getDetail(productSlug);
            // Modal mohol byť medzitým zatvorený
            if (this.isOpen()) {
                this.content.innerHTML = html;
                this.setLoading(false);
            }
        } catch {
            Toast.error('Failed to load product.');
            this.close();
        }
    },

    close() {
        this.modal.classList.add('hidden');
        this.content.replaceChildren();
        document.body.style.overflow = '';
        this.previousFocus?.focus?.();
        this.previousFocus = null;
    },
};

export default QuickView;
