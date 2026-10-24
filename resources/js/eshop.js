/**
 * Eshop - inicializácia modulov
 */
import Cart from './modules/cart';
import Checkout from './modules/checkout';
import PageActions from './modules/page-actions';
import Search from './modules/search';
import Toast from './modules/toast';
import Services from './services';

const Eshop = {
    init() {
        Cart.init();
        Search.init();
        Checkout.init();
        PageActions.init();
    },
};

document.addEventListener('DOMContentLoaded', () => {
    Eshop.init();
});

// Verejné API modulov (napr. pre ladenie v konzole)
window.Eshop = {
    Toast,
    Cart,
    Search,
    Services,
};

export default Eshop;
