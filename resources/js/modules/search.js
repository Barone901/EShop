/**
 * Search Module
 *
 * Vyhľadávanie produktov s autocomplete (desktop aj mobilné pole v hlavičke).
 *
 * [AI] Upravené s pomocou AI (Claude): rušenie zastaraných requestov (výsledky starého dotazu neprepíšu nový).
 */

import ProductService from '../services/productService';
import escapeHtml from '../utils/escape-html';
import { onOutsideClick } from '../utils/dom';
import { productImage } from '../utils/templates';

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
                ${productImage(product)}
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
        let controller = null;

        const show = () => results.classList.remove('hidden');
        const hide = () => results.classList.add('hidden');

        const search = async (query) => {
            controller = new AbortController();

            try {
                const products = await ProductService.search(query, { signal: controller.signal });
                results.innerHTML = renderResults(products);
                show();
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Search error:', error);
                }
            }
        };

        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            // Výsledok staršieho dotazu sa už nesmie vykresliť
            controller?.abort();

            const query = input.value.trim();

            if (query.length < MIN_QUERY_LENGTH) {
                hide();
                return;
            }

            debounceTimer = setTimeout(() => search(query), DEBOUNCE_MS);
        });

        input.addEventListener('focus', () => {
            if (input.value.trim().length >= MIN_QUERY_LENGTH && results.innerHTML.trim()) {
                show();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') hide();
        });

        onOutsideClick([input, results], hide);
    },
};

export default Search;
