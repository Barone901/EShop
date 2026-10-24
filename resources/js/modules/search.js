/**
 * Search Module
 *
 * Vyhľadávanie produktov s autocomplete (desktop aj mobilné pole v headeri)
 */

import ProductService from '../services/productService';
import escapeHtml from '../utils/escape-html';

const MIN_QUERY_LENGTH = 2;
const DEBOUNCE_MS = 300;

// Dvojice [input, kontajner na výsledky]
const INSTANCES = [
    ['search-input', 'search-results'],
    ['search-input-mobile', 'search-results-mobile'],
];

const renderResults = (products) => {
    if (products.length === 0) {
        return `
            <div class="p-4 text-sm text-gray-500">
                No results found for this search.
            </div>
        `;
    }

    return products.map((product) => `
        <a href="${escapeHtml(product.url)}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors">
            <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                ${product.image_url
                    ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name)}" class="w-full h-full object-cover">`
                    : ''
                }
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-medium text-sm truncate">${escapeHtml(product.name)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(product.category)}</div>
            </div>
            <div class="text-sm font-bold">${escapeHtml(product.formatted_price)}</div>
        </a>
    `).join('');
};

const Search = {
    init() {
        INSTANCES.forEach(([inputId, resultsId]) => {
            const input = document.getElementById(inputId);
            const results = document.getElementById(resultsId);

            if (input && results) {
                this.bind(input, results);
            }
        });
    },

    bind(input, results) {
        let debounceTimer = null;

        const show = () => results.classList.remove('hidden');
        const hide = () => results.classList.add('hidden');

        const search = async (query) => {
            if (query.length < MIN_QUERY_LENGTH) {
                hide();
                return;
            }

            try {
                const products = await ProductService.search(query);
                results.innerHTML = renderResults(products);
                show();
            } catch (error) {
                console.error('Search error:', error);
            }
        };

        input.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => search(e.target.value), DEBOUNCE_MS);
        });

        input.addEventListener('focus', () => {
            if (input.value.length >= MIN_QUERY_LENGTH) {
                show();
            }
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                hide();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                hide();
            }
        });
    },
};

export default Search;
