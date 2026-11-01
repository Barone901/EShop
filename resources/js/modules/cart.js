/**
 * Cart Module
 *
 * Správa nákupného košíka - pridávanie, odoberanie, aktualizácia.
 * Otváranie dropdownu košíka v hlavičke rieši menu.js.
 *
 * [AI] Upravené s pomocou AI (Claude): limit množstva, obnova košíka po chybe, spoločné šablóny.
 *
 * [AI] Upravené s pomocou AI (Claude): redizajn (Tailwind triedy), buttonClass() a byId() zo spoločných utils.
 */

import CartService from '../services/cart-service';
import Toast from './toast';
import { escapeHtml } from '../utils/escape-html';
import { byId, delegate } from '../utils/dom';
import { buttonClass, icon, productImage } from '../utils/templates';

const DROPDOWN_ITEMS_LIMIT = 4;

// Rovnaký limit ako na serveri (config/shop.php, max_cart_quantity)
const MAX_QUANTITY = 99;

const toInt = (value) => parseInt(value, 10);

const Cart = {
    countElement: null,
    dropdownContent: null,
    urls: { cart: '', checkout: '', home: '' },

    init() {
        this.countElement = byId('cart-count');
        this.dropdownContent = byId('cart-dropdown-content');

        // URL adresy generuje Blade cez route() (data atribúty na #cart-wrapper v hlavičke)
        const { cartUrl, checkoutUrl, homeUrl } = byId('cart-wrapper')?.dataset ?? {};
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

        const cartPageContent = byId('cart-page-content');
        if (cartPageContent) {
            this.renderCartPage(cart, cartPageContent);
        }
    },

    renderDropdown(cart) {
        if (cart.items.length === 0) {
            this.dropdownContent.innerHTML = `
                <div class="p-6 text-center text-subtle">
                    ${icon('cart', 'size-12 mx-auto mb-3 text-line-strong', 1.5)}
                    <p class="text-sm">Your cart is empty</p>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.slice(0, DROPDOWN_ITEMS_LIMIT).map((item) => `
            <div class="flex gap-3 px-4 py-3 hover:bg-paper">
                <div class="size-14 bg-tile border border-line rounded-xs overflow-hidden shrink-0">
                    ${productImage(item)}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${escapeHtml(item.url)}" class="text-sm font-medium hover:text-brand-600 line-clamp-1">${escapeHtml(item.name)}</a>
                    <div class="num text-xs text-subtle">${item.quantity}× ${escapeHtml(item.formatted_price)}</div>
                    <div class="num text-sm">${escapeHtml(item.formatted_subtotal)}</div>
                </div>
                <button type="button" data-remove-from-cart="${item.id}" class="self-start grid place-items-center size-9 rounded-xs text-subtle hover:bg-danger-50 hover:text-danger-700" aria-label="Remove ${escapeHtml(item.name)}">
                    ${icon('close')}
                </button>
            </div>
        `).join('');

        const moreItems = cart.items.length > DROPDOWN_ITEMS_LIMIT ? `
            <div class="px-4 py-2 text-xs text-subtle text-center">
                + ${cart.items.length - DROPDOWN_ITEMS_LIMIT} more items
            </div>
        ` : '';

        this.dropdownContent.innerHTML = `
            <div class="max-h-72 overflow-y-auto divide-y divide-line">
                ${itemsHtml}
                ${moreItems}
            </div>
            <div class="p-4 border-t border-line bg-paper">
                <div class="flex justify-between items-baseline mb-3">
                    <span class="text-sm text-muted">Total:</span>
                    <span class="num text-xl">${escapeHtml(cart.formatted_total)}</span>
                </div>
                <div class="flex gap-2">
                    <a href="${this.urls.cart}" class="${buttonClass('secondary', 'md', 'flex-1')}">
                        View Cart
                    </a>
                    <a href="${this.urls.checkout}" class="${buttonClass('primary', 'md', 'flex-1')}">
                        Checkout
                    </a>
                </div>
            </div>
        `;
    },

    renderCartPage(cart, container) {
        if (cart.items.length === 0) {
            container.innerHTML = `
                <div class="card text-center px-6 py-16">
                    ${icon('cart', 'size-16 mx-auto mb-4 text-line-strong', 1.5)}
                    <h2 class="text-2xl mb-2">Your cart is empty</h2>
                    <p class="text-muted mb-6">Add some items to your cart and come back.</p>
                    <a href="${this.urls.home}" class="${buttonClass('primary')}">
                        ${icon('arrowLeft', 'size-5')}
                        Continue Shopping
                    </a>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.map((item) => `
            <div class="card flex gap-4 p-4">
                <div class="size-20 sm:size-24 bg-tile border border-line rounded-xs overflow-hidden shrink-0">
                    ${productImage(item)}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${escapeHtml(item.url)}" class="font-semibold leading-snug hover:text-brand-600">${escapeHtml(item.name)}</a>
                    <div class="num text-sm text-subtle mt-1">Price: ${escapeHtml(item.formatted_price)}</div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-3">
                        <div class="flex items-center border border-line-strong rounded-xs bg-white">
                            <button
                                type="button"
                                class="size-11 flex items-center justify-center text-lg hover:bg-tile transition-colors"
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
                                class="num w-14 h-11 text-center text-sm border-0 border-x border-line-strong rounded-none"
                                aria-label="Quantity"
                            />
                            <button
                                type="button"
                                class="size-11 flex items-center justify-center text-lg hover:bg-tile transition-colors disabled:text-line-strong disabled:cursor-not-allowed disabled:hover:bg-transparent"
                                data-update-cart="${item.id}"
                                data-quantity="${item.quantity + 1}"
                                aria-label="Increase quantity"
                                ${item.quantity >= MAX_QUANTITY ? 'disabled' : ''}
                            >+</button>
                        </div>
                        <button
                            type="button"
                            data-remove-from-cart="${item.id}"
                            class="inline-flex items-center gap-1.5 min-h-11 px-2 -ml-2 text-sm text-muted rounded-xs hover:text-danger-700 hover:bg-danger-50"
                        >
                            ${icon('trash')}
                            Remove
                        </button>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="num text-lg">${escapeHtml(item.formatted_subtotal)}</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = `
            <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="lg:col-span-2 space-y-3">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl">Items in Cart (${cart.count})</h2>
                        <button type="button" data-clear-cart class="inline-flex items-center gap-1.5 min-h-11 px-3 text-sm text-muted rounded-xs hover:text-danger-700 hover:bg-danger-50">
                            ${icon('trash')}
                            Clear Cart
                        </button>
                    </div>
                    ${itemsHtml}
                </div>
                <div class="lg:col-span-1">
                    <div class="card border-t-3 border-t-brand-600 p-6 lg:sticky lg:top-36">
                        <h3 class="text-xl mb-4">Order Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted">Subtotal</span>
                                <span class="num">${escapeHtml(cart.formatted_total)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted">Shipping</span>
                                <span class="num">${escapeHtml(cart.formatted_shipping_cost)}</span>
                            </div>
                            <div class="border-t border-line pt-3 flex justify-between items-baseline">
                                <span class="font-display text-lg font-semibold uppercase tracking-wide">Total</span>
                                <span class="num text-2xl">${escapeHtml(cart.formatted_grand_total)}</span>
                            </div>
                        </div>
                        <a href="${this.urls.checkout}" class="${buttonClass('primary', 'lg', 'mt-6 w-full')}">
                            Proceed to Checkout
                        </a>
                        <a href="${this.urls.home}" class="${buttonClass('secondary', 'md', 'mt-3 w-full')}">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        `;
    },
};

export default Cart;
