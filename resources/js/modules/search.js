/**
 * Search Module
 *
 * Vyhľadávanie produktov s autocomplete (desktop aj mobilné pole v hlavičke).
 * Výsledky vyrenderuje server (Blade komponent x-shop.search-results), modul HTML len vloží.
 *
 * [AI] Upravené s pomocou AI (Claude).
 */

import ProductService from '../services/product-service';
import { byId, onEscape, onOutsideClick } from '../utils/dom';

const MIN_QUERY_LENGTH = 2;
const DEBOUNCE_MS = 300;

// Dvojice [input, kontajner na výsledky]
const INSTANCES = [
    ['search-input', 'search-results'],
    ['search-input-mobile', 'search-results-mobile'],
];

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
                const { html } = await ProductService.search(query, { signal: controller.signal });
                results.innerHTML = html;
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
