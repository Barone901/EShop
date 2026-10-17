/**
 * Eshop - inicializácia modulov
 */
import Cart from './modules/cart';
import PageActions from './modules/page-actions';
import Services from './services';

const Eshop = {
    init() {
        Cart.init();
        PageActions.init();
    },
};

document.addEventListener('DOMContentLoaded', () => {
    Eshop.init();
});

// Verejné API modulov (napr. pre ladenie v konzole)
window.Eshop = {
    Cart,
    Services,
};

export default Eshop;
