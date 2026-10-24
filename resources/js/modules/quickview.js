/**
 * Quick View Module
 *
 * Rýchly náhľad produktu v modálnom okne
 */

import ProductService from '../services/productService';
import Toast from './toast';
import escapeHtml from '../utils/escape-html';

const QuickView = {
    modal: null,

    init() {
        this.createModal();
        this.bindEvents();
    },

    bindEvents() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-quick-view]');
            if (btn) {
                e.preventDefault();
                this.open(btn.dataset.quickView);
            }
        });
    },

    createModal() {
        this.modal = document.createElement('div');
        this.modal.id = 'quick-view-modal';
        this.modal.className = 'fixed inset-0 z-50 hidden';
        this.modal.innerHTML = `
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-close-modal></div>
            <div role="dialog" aria-modal="true" class="absolute inset-4 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden">
                <button type="button" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100" data-close-modal aria-label="Close">
                    ✕
                </button>
                <div id="quick-view-content" class="p-6">
                    <div class="flex items-center justify-center h-40">
                        <div class="animate-spin w-8 h-8 border-2 border-gray-300 border-t-black rounded-full"></div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(this.modal);

        // Close handlers
        this.modal.querySelectorAll('[data-close-modal]').forEach((el) => {
            el.addEventListener('click', () => this.close());
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.close();
        });
    },

    async open(productSlug) {
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        try {
            const product = await ProductService.getDetail(productSlug);
            this.render(product);
        } catch {
            Toast.error('Failed to load product.');
            this.close();
        }
    },

    render(product) {
        const content = document.getElementById('quick-view-content');
        content.innerHTML = `
            <div class="grid md:grid-cols-2 gap-6">
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                    ${product.image_url
                        ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name)}" class="w-full h-full object-cover">`
                        : '<div class="w-full h-full flex items-center justify-center text-gray-400">No image</div>'
                    }
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">${escapeHtml(product.category?.name)}</div>
                    <h2 class="text-2xl font-bold mb-2">${escapeHtml(product.name)}</h2>
                    <div class="text-2xl font-bold mb-4">${escapeHtml(product.formatted_price)}</div>

                    <div class="mb-4">
                        ${product.in_stock
                            ? `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-50 text-green-700">In Stock: ${escapeHtml(product.stock)}</span>`
                            : '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-red-700">Out of Stock</span>'
                        }
                    </div>

                    <p class="text-gray-600 text-sm mb-6 line-clamp-4">${escapeHtml(product.description)}</p>

                    <div class="flex gap-3">
                        ${product.in_stock
                            ? `<button type="button" data-add-to-cart="${product.id}" class="flex-1 bg-black text-white px-4 py-3 rounded-lg hover:opacity-90 transition-opacity">
                                Add to Cart
                               </button>`
                            : ''
                        }
                        <a href="${escapeHtml(product.url)}" class="px-4 py-3 border rounded-lg hover:bg-gray-50 transition-colors">
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
    },
};

export default QuickView;

