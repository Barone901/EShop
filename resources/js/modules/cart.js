/**
 * Cart Module
 *
 * Správa nákupného košíka - pridávanie, odoberanie, aktualizácia.
 * Modul obsahuje len správanie (udalosti, volania API). HTML košíka vytvára server
 * z Blade komponentov a posiela ho v JSON (html.dropdown, html.page) - JS ho len vloží.
 * Otváranie dropdownu košíka v hlavičke rieši menu.js.
 *
 * [AI] Upravené s pomocou AI (Claude).
 */

import CartService from '../services/cart-service';
import Toast from './toast';
import { byId, delegate } from '../utils/dom';

const toInt = (value) => parseInt(value, 10);

const Cart = {
    countElement: null,
    dropdownContent: null,

    init() {
        this.countElement = byId('cart-count');
        this.dropdownContent = byId('cart-dropdown-content');

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
            this.updateUI(data.cart, data.html);
            Toast.success(data.message);
        } catch (error) {
            Toast.error(error.message);
            // Neplatna hodnota (napr. mnozstvo nad limit) by inak ostala v inpute
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
            this.updateUI(data, data.html);
        } catch (error) {
            console.error('Failed to refresh cart:', error);
        }
    },

    /**
     * Prekreslí košík: počet v hlavičke z dát, náhľad a stránku košíka z HTML,
     * ktoré vyrenderoval server (Blade komponenty x-shop.cart-dropdown a x-shop.cart-page).
     */
    updateUI(cart, html) {
        if (this.countElement) {
            this.countElement.textContent = cart.count;
            this.countElement.classList.toggle('hidden', cart.count === 0);
        }

        if (this.dropdownContent) {
            this.dropdownContent.innerHTML = html.dropdown;
        }

        const cartPageContent = byId('cart-page-content');
        if (cartPageContent) {
            cartPageContent.innerHTML = html.page;
        }
    },
};

export default Cart;
