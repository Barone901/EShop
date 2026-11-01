/**
 * ProductService
 * - API volania pre produkty
 */
import ApiService from './api';

const ProductService = {
    /**
     * Vyhľadávanie produktov (options.signal umožní zrušiť zastaraný request).
     */
    search(query, options = {}) {
        return ApiService.get('/products/search', { q: query }, options);
    },

    /**
     * Získa detail produktu.
     */
    getDetail(slug) {
        return ApiService.get(`/products/${encodeURIComponent(slug)}`);
    },
};

export default ProductService;
