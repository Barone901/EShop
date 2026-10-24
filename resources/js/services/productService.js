/**
 * ProductService
 * - API volania pre produkty
 */
import ApiService from './api';

const ProductService = {
    /**
     * Vyhľadávanie produktov.
     */
    search(query) {
        return ApiService.get('/products/search', { q: query });
    },

    /**
     * Získa detail produktu.
     */
    getDetail(slug) {
        return ApiService.get(`/products/${encodeURIComponent(slug)}`);
    },
};

export default ProductService;
