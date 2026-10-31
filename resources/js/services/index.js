/**
 * Centrálny export pre všetky API services.
 */
import ApiService from './api';
import ProductService from './productService';
import CartService from './cartService';

export default {
    Api: ApiService,
    Product: ProductService,
    Cart: CartService,
};
