/**
 * Eshop - inicializácia modulov
 */
import Cart from './modules/cart';
import Search from './modules/search';
import QuickView from './modules/quickview';
import FormValidator from './modules/form-validator';
import Checkout from './modules/checkout';
import PageActions from './modules/page-actions';
import Toast from './modules/toast';
import Services from './services';

const Eshop = {
    init() {
        Cart.init();
        Search.init();
        QuickView.init();
        FormValidator.init();
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
    QuickView,
    FormValidator,
    Services,
};

export default Eshop;
