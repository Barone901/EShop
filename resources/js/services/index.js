/**
 * Centrálny export pre všetky API services.
 *
 * [AI] Upravené s pomocou AI (Claude): importy po premenovaní súborov na kebab-case.
 */
import ApiService from './api';
import ProductService from './product-service';
import CartService from './cart-service';

export default {
    Api: ApiService,
    Product: ProductService,
    Cart: CartService,
};
