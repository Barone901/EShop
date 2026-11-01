/**
 * Quick View Module
 *
 * Rýchly náhľad produktu v modálnom okne.
 *
 * [AI] Upravené s pomocou AI (Claude): reset obsahu pri otvorení, správa fokusu, aria-labelledby.
 *
 * [AI] Upravené s pomocou AI (Claude): redizajn (Tailwind triedy), stockBadge()/buttonClass()/spinner() zo šablón.
 */

import ProductService from '../services/product-service';
import Toast from './toast';
import { escapeHtml } from '../utils/escape-html';
import { delegate, onEscape } from '../utils/dom';
import { buttonClass, productImage, spinner, stockBadge } from '../utils/templates';

const LOADING_HTML = `
    <div class="flex items-center justify-center h-40">
        ${spinner()}
    </div>
`;

const QuickView = {
    modal: null,
    content: null,
    closeButton: null,
    // Prvok, ktorý mal fokus pred otvorením (po zatvorení sa naň vráti)
    previousFocus: null,

    init() {
        this.createModal();

        delegate('click', '[data-quick-view]', (e, btn) => {
            e.preventDefault();
            this.open(btn.dataset.quickView);
        });

        onEscape(() => {
            if (this.isOpen()) this.close();
        });
    },

    createModal() {
        this.modal = document.createElement('div');
        this.modal.id = 'quick-view-modal';
        this.modal.className = 'fixed inset-0 z-[150] hidden';
        this.modal.innerHTML = `
            <div class="absolute inset-0 bg-ink-950/70" data-close-modal></div>
            <div role="dialog" aria-modal="true" aria-label="Product quick view" aria-labelledby="quick-view-title" class="absolute inset-x-4 top-4 bottom-4 overflow-y-auto md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-3xl md:max-h-[90vh] bg-white text-text border-t-3 border-brand-600 rounded-xs shadow-2xl">
                <button type="button" class="absolute top-3 right-3 z-10 size-11 flex items-center justify-center rounded-xs text-muted hover:bg-tile hover:text-text" data-close-modal aria-label="Close">
                    ✕
                </button>
                <div id="quick-view-content" class="p-6"></div>
            </div>
        `;
        document.body.appendChild(this.modal);

        this.content = this.modal.querySelector('#quick-view-content');
        this.closeButton = this.modal.querySelector('button[data-close-modal]');

        this.modal.querySelectorAll('[data-close-modal]').forEach((el) => {
            el.addEventListener('click', () => this.close());
        });
    },

    isOpen() {
        return !this.modal.classList.contains('hidden');
    },

    async open(productSlug) {
        this.previousFocus = document.activeElement;
        this.content.innerHTML = LOADING_HTML;
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        this.closeButton.focus();

        try {
            const product = await ProductService.getDetail(productSlug);
            // Modal mohol byť medzitým zatvorený
            if (this.isOpen()) this.render(product);
        } catch {
            Toast.error('Failed to load product.');
            this.close();
        }
    },

    render(product) {
        this.content.innerHTML = `
            <div class="grid md:grid-cols-2 gap-6">
                <div class="aspect-square bg-tile border border-line rounded-xs overflow-hidden p-4">
                    ${productImage(product, '<div class="size-full flex items-center justify-center text-subtle">No image</div>')}
                </div>
                <div>
                    <div class="eyebrow mb-2">${escapeHtml(product.category?.name)}</div>
                    <h2 id="quick-view-title" class="text-2xl md:text-3xl mb-3 pr-10">${escapeHtml(product.name)}</h2>
                    <div class="num text-3xl mb-4">${escapeHtml(product.formatted_price)}</div>

                    <div class="mb-4">
                        ${stockBadge(product)}
                    </div>

                    <p class="text-muted text-sm mb-6 line-clamp-4">${escapeHtml(product.description)}</p>

                    <div class="flex gap-3">
                        ${product.in_stock
                            ? `<button type="button" data-add-to-cart="${product.id}" class="${buttonClass('primary', 'md', 'flex-1')}">
                                Add to Cart
                               </button>`
                            : ''
                        }
                        <a href="${escapeHtml(product.url)}" class="${buttonClass('secondary')}">
                            Details
                        </a>
                    </div>
                </div>
            </div>
        `;
    },

    close() {
        this.modal.classList.add('hidden');
        document.body.style.overflow = '';
        this.previousFocus?.focus?.();
        this.previousFocus = null;
    },
};

export default QuickView;
