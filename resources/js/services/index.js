/**
 * Centrálny export pre všetky API services.
 */
import ApiService from './api';
import ProductService from './product-service';
import CartService from './cart-service';

export default {
    Api: ApiService,
    Product: ProductService,
    Cart: CartService,
};
