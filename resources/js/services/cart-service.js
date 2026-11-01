/**
 * CartService
 * - API volania pre košík
 *
 * [AI] Upravené s pomocou AI (Claude): súbor premenovaný z cartService.js (kebab-case).
 */
import ApiService from './api';

const CartService = {
    getCart() {
        return ApiService.get('/cart');
    },

    addProduct(productId, quantity = 1) {
        return ApiService.post('/cart/add', {
            product_id: productId,
            quantity,
        });
    },

    updateQuantity(productId, quantity) {
        return ApiService.post('/cart/update', {
            product_id: productId,
            quantity,
        });
    },

    removeProduct(productId) {
        return ApiService.post('/cart/remove', {
            product_id: productId,
        });
    },

    clear() {
        return ApiService.post('/cart/clear');
    },
};

export default CartService;
