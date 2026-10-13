/**
 * Eshop - inicializácia modulov
 */
import PageActions from './modules/page-actions';

const Eshop = {
    init() {
        PageActions.init();
    },
};

document.addEventListener('DOMContentLoaded', () => {
    Eshop.init();
});

export default Eshop;
