/**
 * Search Module
 *
 * Vyhľadávanie produktov s autocomplete (desktop aj mobilné pole v hlavičke).
 *
 * [AI] Upravené s pomocou AI (Claude): rušenie zastaraných requestov (výsledky starého dotazu neprepíšu nový).
 *
 * [AI] Upravené s pomocou AI (Claude): redizajn (Tailwind triedy), onEscape() a byId() zo spoločných utils.
 */

import ProductService from '../services/product-service';
import { escapeHtml } from '../utils/escape-html';
import { byId, onEscape, onOutsideClick } from '../utils/dom';
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
            <div class="p-4 text-sm text-subtle">
                No results found for this search.
            </div>
        `;
    }

    return products.map((product) => `
        <a href="${escapeHtml(product.url)}" class="flex items-center gap-3 px-4 py-3 border-b border-line last:border-b-0 hover:bg-paper hover:text-brand-600 transition-colors">
            <div class="size-12 bg-tile border border-line rounded-xs overflow-hidden shrink-0">
                ${productImage(product)}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-medium text-sm truncate">${escapeHtml(product.name)}</div>
                <div class="eyebrow">${escapeHtml(product.category)}</div>
            </div>
            <div class="num text-sm text-text">${escapeHtml(product.formatted_price)}</div>
        </a>
    `).join('');
};

const Search = {
    init() {
        INSTANCES.forEach(([inputId, resultsId]) => {
            const input = byId(inputId);
            const results = byId(resultsId);

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

        onEscape(hide);
        onOutsideClick([input, results], hide);
    },
};

export default Search;
