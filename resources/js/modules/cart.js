/**
 * Cart Module
 *
 * Správa nákupného košíka - pridávanie, odoberanie, aktualizácia
 */

import CartService from '../services/cartService';
import escapeHtml from '../utils/escape-html';

const DROPDOWN_ITEMS_LIMIT = 4;

const CART_ICON_PATH = 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z';
const TRASH_ICON_PATH = 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16';

const toInt = (value) => parseInt(value, 10);

// Bez obrazka ostava prazdna siva plocha (rovnako ako vo vysledkoch vyhladavania)
const renderImage = (item) => (item.image_url
    ? `<img src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover">`
    : '');

const Cart = {
    countElement: null,
    dropdownElement: null,
    dropdownContent: null,
    urls: { cart: '', home: '' },

    init() {
        this.countElement = document.getElementById('cart-count');
        this.dropdownElement = document.getElementById('cart-dropdown');
        this.dropdownContent = document.getElementById('cart-dropdown-content');

        // URL adresy generuje Blade cez route() (data atributy na #cart-wrapper v headeri)
        const { cartUrl, homeUrl } = document.getElementById('cart-wrapper')?.dataset ?? {};
        this.urls = { cart: cartUrl, home: homeUrl };

        this.bindEvents();
        this.refresh();
    },

    bindEvents() {
        document.addEventListener('click', (e) => {
            const addBtn = e.target.closest('[data-add-to-cart]');
            if (addBtn) {
                e.preventDefault();
                this.add(addBtn.dataset.addToCart, this.resolveQuantity(addBtn));
            }

            const updateBtn = e.target.closest('[data-update-cart]');
            if (updateBtn) {
                e.preventDefault();
                this.update(updateBtn.dataset.updateCart, updateBtn.dataset.quantity);
            }

            const removeBtn = e.target.closest('[data-remove-from-cart]');
            if (removeBtn) {
                e.preventDefault();
                this.remove(removeBtn.dataset.removeFromCart);
            }

            const clearBtn = e.target.closest('[data-clear-cart]');
            if (clearBtn) {
                e.preventDefault();
                this.clear();
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.matches('[data-cart-quantity]')) {
                const productId = e.target.dataset.cartQuantity;
                const quantity = toInt(e.target.value) || 0;
                this.update(productId, quantity);
            }
        });

        this.initDropdown();
    },

    /**
     * Množstvo sa berie z inputu (data-quantity-input="#selector"),
     * alebo z data-quantity, inak 1.
     */
    resolveQuantity(button) {
        const { quantityInput, quantity } = button.dataset;

        if (quantityInput) {
            const input = document.querySelector(quantityInput);
            if (input) return input.value;
        }

        return quantity || 1;
    },

    initDropdown() {
        const cartWrapper = document.getElementById('cart-wrapper');

        if (!cartWrapper || !this.dropdownElement) return;

        // Pozn.: menu.js zatvara tento dropdown pri otvoreni ineho menu alebo kliknuti mimo

        let hideTimeout;

        const showDropdown = () => {
            clearTimeout(hideTimeout);
            this.dropdownElement.classList.remove('hidden', 'opacity-0', 'translate-y-2');
            this.dropdownElement.classList.add('opacity-100', 'translate-y-0');
        };

        const hideDropdown = () => {
            hideTimeout = setTimeout(() => {
                this.dropdownElement.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    this.dropdownElement.classList.add('hidden');
                }, 200);
            }, 150);
        };

        cartWrapper.addEventListener('mouseenter', showDropdown);
        cartWrapper.addEventListener('mouseleave', hideDropdown);

        this.dropdownElement.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
        this.dropdownElement.addEventListener('mouseleave', hideDropdown);
    },

    /**
     * Spoločné spracovanie odpovede pre akcie nad košíkom.
     */
    async handleAction(request) {
        const data = await request;
        this.updateUI(data.cart);
    },

    add(productId, quantity = 1) {
        return this.handleAction(CartService.addProduct(toInt(productId), toInt(quantity)));
    },

    update(productId, quantity) {
        return this.handleAction(CartService.updateQuantity(toInt(productId), toInt(quantity)));
    },

    remove(productId) {
        return this.handleAction(CartService.removeProduct(toInt(productId)));
    },

    clear() {
        return this.handleAction(CartService.clear());
    },

    async refresh() {
        try {
            const data = await CartService.getCart();
            this.updateUI(data);
        } catch (error) {
            console.error('Failed to refresh cart:', error);
        }
    },

    updateUI(cart) {
        if (this.countElement) {
            this.countElement.textContent = cart.count;
            this.countElement.classList.toggle('hidden', cart.count === 0);
        }

        if (this.dropdownContent) {
            this.renderDropdown(cart);
        }

        const cartPageContent = document.getElementById('cart-page-content');
        if (cartPageContent) {
            this.renderCartPage(cart, cartPageContent);
        }
    },

    renderDropdown(cart) {
        if (cart.items.length === 0) {
            this.dropdownContent.innerHTML = `
                <div class="p-6 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${CART_ICON_PATH}"/>
                    </svg>
                    <p class="text-sm">Your cart is empty</p>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.slice(0, DROPDOWN_ITEMS_LIMIT).map((item) => `
            <div class="flex gap-3 p-3 hover:bg-gray-50">
                <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    ${renderImage(item)}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${escapeHtml(item.url)}" class="text-sm font-medium hover:underline line-clamp-1">${escapeHtml(item.name)}</a>
                    <div class="text-xs text-gray-500">${item.quantity}× ${escapeHtml(item.formatted_price)}</div>
                    <div class="text-sm font-semibold">${escapeHtml(item.formatted_subtotal)}</div>
                </div>
                <button type="button" data-remove-from-cart="${item.id}" class="text-gray-400 hover:text-red-500 p-1" aria-label="Remove ${escapeHtml(item.name)}">
                    <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `).join('');

        const moreItems = cart.items.length > DROPDOWN_ITEMS_LIMIT ? `
            <div class="px-3 py-2 text-xs text-gray-500 text-center border-t">
                + ${cart.items.length - DROPDOWN_ITEMS_LIMIT} more items
            </div>
        ` : '';

        this.dropdownContent.innerHTML = `
            <div class="max-h-72 overflow-y-auto divide-y">
                ${itemsHtml}
                ${moreItems}
            </div>
            <div class="p-3 border-t bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-600">Total:</span>
                    <span class="text-lg font-bold">${escapeHtml(cart.formatted_total)}</span>
                </div>
                <div class="flex gap-2">
                    <a href="${this.urls.cart}" class="flex-1 text-center py-2 px-3 border rounded-lg text-sm hover:bg-white transition-colors">
                        View Cart
                    </a>
                </div>
            </div>
        `;
    },

    renderCartPage(cart, container) {
        if (cart.items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-16">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${CART_ICON_PATH}"/>
                    </svg>
                    <h2 class="text-xl font-semibold mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Add some items to your cart and come back.</p>
                    <a href="${this.urls.home}" class="inline-flex items-center gap-2 bg-black text-white px-6 py-3 rounded-lg hover:opacity-90 transition-opacity">
                        <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.map((item) => `
            <div class="flex gap-4 p-4 bg-white rounded-xl border">
                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    ${renderImage(item)}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${escapeHtml(item.url)}" class="font-semibold hover:underline">${escapeHtml(item.name)}</a>
                    <div class="text-sm text-gray-500 mt-1">Price: ${escapeHtml(item.formatted_price)}</div>
                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex items-center border rounded-lg">
                            <button
                                type="button"
                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                data-update-cart="${item.id}"
                                data-quantity="${item.quantity - 1}"
                                aria-label="Decrease quantity"
                            >−</button>
                            <input
                                type="number"
                                value="${item.quantity}"
                                min="0"
                                max="99"
                                data-cart-quantity="${item.id}"
                                class="w-12 h-8 text-center text-sm border-x focus:outline-none"
                                aria-label="Quantity"
                            />
                            <button
                                type="button"
                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                data-update-cart="${item.id}"
                                data-quantity="${item.quantity + 1}"
                                aria-label="Increase quantity"
                            >+</button>
                        </div>
                        <button
                            type="button"
                            data-remove-from-cart="${item.id}"
                            class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${TRASH_ICON_PATH}"/>
                            </svg>
                            Remove
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold">${escapeHtml(item.formatted_subtotal)}</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = `
            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Items in Cart (${cart.count})</h2>
                        <button type="button" data-clear-cart class="text-sm text-red-50 hover:text-white flex items-center gap-1">
                            <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${TRASH_ICON_PATH}"/>
                            </svg>
                            Clear Cart
                        </button>
                    </div>
                    ${itemsHtml}
                </div>
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border p-6 sticky top-32">
                        <h3 class="font-semibold mb-4">Order Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>${escapeHtml(cart.formatted_total)}</span>
                            </div>
                        </div>
                        <a href="${this.urls.home}" class="mt-3 w-full block text-center border py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        `;
    },
};

export default Cart;
