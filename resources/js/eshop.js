/**
 * Eshop - inicializácia všetkých modulov (jediný vstupný bod pre vlastný JS).
 */
import Menu from './modules/menu';
import Cart from './modules/cart';
import Search from './modules/search';
import QuickView from './modules/quickview';
import FormValidator from './modules/form-validator';
import Checkout from './modules/checkout';
import PageActions from './modules/page-actions';
import BannerSlider from './modules/banner-slider';
import Toast from './modules/toast';
import Services from './services';

const Eshop = {
    init() {
        Menu.init();
        Cart.init();
        Search.init();
        QuickView.init();
        FormValidator.init();
        Checkout.init();
        PageActions.init();
        BannerSlider.init();
    },
};

document.addEventListener('DOMContentLoaded', () => {
    Eshop.init();
});

// Verejné API modulov (napr. pre ladenie v konzole)
window.Eshop = {
    Toast,
    Menu,
    Cart,
    Search,
    QuickView,
    FormValidator,
    BannerSlider,
    Services,
};

export default Eshop;
