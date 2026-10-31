/**
 * Cart Module
 *
 * Správa nákupného košíka - pridávanie, odoberanie, aktualizácia.
 * Otváranie dropdownu košíka v hlavičke rieši menu.js.
 *
 * [AI] Upravené s pomocou AI (Claude): limit množstva, obnova košíka po chybe, spoločné šablóny.
 */

import CartService from '../services/cartService';
import Toast from './toast';
import escapeHtml from '../utils/escape-html';
import { delegate } from '../utils/dom';
import { icon, productImage } from '../utils/templates';

const DROPDOWN_ITEMS_LIMIT = 4;

// Rovnaký limit ako validácia v Api\CartController
const MAX_QUANTITY = 99;

const toInt = (value) => parseInt(value, 10);

const Cart = {
    countElement: null,
    dropdownContent: null,
    urls: { cart: '', checkout: '', home: '' },

    init() {
        this.countElement = document.getElementById('cart-count');
        this.dropdownContent = document.getElementById('cart-dropdown-content');

        // URL adresy generuje Blade cez route() (data atribúty na #cart-wrapper v hlavičke)
        const { cartUrl, checkoutUrl, homeUrl } = document.getElementById('cart-wrapper')?.dataset ?? {};
        this.urls = { cart: cartUrl, checkout: checkoutUrl, home: homeUrl };

        this.bindEvents();
        this.refresh();
    },

    bindEvents() {
        delegate('click', '[data-add-to-cart]', (e, btn) => {
            e.preventDefault();
            this.add(btn.dataset.addToCart, this.resolveQuantity(btn));
        });

        delegate('click', '[data-update-cart]', (e, btn) => {
            e.preventDefault();
            this.update(btn.dataset.updateCart, btn.dataset.quantity);
        });

        delegate('click', '[data-remove-from-cart]', (e, btn) => {
            e.preventDefault();
            this.remove(btn.dataset.removeFromCart);
        });

        delegate('click', '[data-clear-cart]', (e) => {
            e.preventDefault();
            this.clear();
        });

        delegate('change', '[data-cart-quantity]', (e, input) => {
            this.update(input.dataset.cartQuantity, toInt(input.value) || 0);
        });
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

    /**
     * Spoločné spracovanie odpovede pre akcie nad košíkom.
     */
    async handleAction(request) {
        try {
            const data = await request;
            this.updateUI(data.cart);
            Toast.success(data.message);
        } catch (error) {
            Toast.error(error.message);
            // Neplatná hodnota (napr. množstvo nad limit) by inak ostala v inpute
            this.refresh();
        }
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
                    ${icon('cart', 'w-12 h-12 mx-auto mb-3 text-gray-300', 1.5)}
                    <p class="text-sm">Your cart is empty</p>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.slice(0, DROPDOWN_ITEMS_LIMIT).map((item) => `
            <div class="flex gap-3 p-3 hover:bg-gray-50">
                <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    ${productImage(item)}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${escapeHtml(item.url)}" class="text-sm font-medium hover:underline line-clamp-1">${escapeHtml(item.name)}</a>
                    <div class="text-xs text-gray-500">${item.quantity}× ${escapeHtml(item.formatted_price)}</div>
                    <div class="text-sm font-semibold">${escapeHtml(item.formatted_subtotal)}</div>
                </div>
                <button type="button" data-remove-from-cart="${item.id}" class="text-gray-400 hover:text-red-500 p-1" aria-label="Remove ${escapeHtml(item.name)}">
                    ${icon('close')}
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
                    <a href="${this.urls.checkout}" class="flex-1 text-center py-2 px-3 bg-black text-white rounded-lg text-sm hover:opacity-90 transition-opacity">
                        Checkout
                    </a>
                </div>
            </div>
        `;
    },

    renderCartPage(cart, container) {
        if (cart.items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-16">
                    ${icon('cart', 'w-20 h-20 mx-auto mb-4 text-gray-300', 1.5)}
                    <h2 class="text-xl font-semibold mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Add some items to your cart and come back.</p>
                    <a href="${this.urls.home}" class="inline-flex items-center gap-2 bg-black text-white px-6 py-3 rounded-lg hover:opacity-90 transition-opacity">
                        ${icon('arrowLeft', 'w-5 h-5')}
                        Continue Shopping
                    </a>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.map((item) => `
            <div class="flex gap-4 p-4 bg-white rounded-xl border">
                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    ${productImage(item)}
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
                                max="${MAX_QUANTITY}"
                                data-cart-quantity="${item.id}"
                                class="w-12 h-8 text-center text-sm border-x focus:outline-none"
                                aria-label="Quantity"
                            />
                            <button
                                type="button"
                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                data-update-cart="${item.id}"
                                data-quantity="${item.quantity + 1}"
                                aria-label="Increase quantity"
                                ${item.quantity >= MAX_QUANTITY ? 'disabled' : ''}
                            >+</button>
                        </div>
                        <button
                            type="button"
                            data-remove-from-cart="${item.id}"
                            class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1"
                        >
                            ${icon('trash')}
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
                            ${icon('trash')}
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
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span>${escapeHtml(cart.formatted_shipping_cost)}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span>${escapeHtml(cart.formatted_grand_total)}</span>
                            </div>
                        </div>
                        <a href="${this.urls.checkout}" class="mt-6 w-full block text-center bg-black text-white py-3 px-4 rounded-lg hover:opacity-90 transition-opacity">
                            Proceed to Checkout
                        </a>
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
