/**
 * Spoločné HTML šablóny pre moduly, ktoré vykresľujú obsah cez innerHTML.
 *
 * [AI] Vygenerované s pomocou AI (Claude).
 */
import escapeHtml from './escape-html';

const ICON_PATHS = {
    cart: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
    trash: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    close: 'M6 18L18 6M6 6l12 12',
    arrowLeft: 'M10 19l-7-7m0 0l7-7m-7 7h18',
};

export const icon = (name, className = 'w-4 h-4', strokeWidth = 2) => `
    <svg class="${className}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="${strokeWidth}" d="${ICON_PATHS[name]}"/>
    </svg>
`;

/**
 * Obrázok produktu; bez obrázka sa vráti fallback (predvolene prázdna sivá plocha kontajnera).
 */
export const productImage = (product, fallback = '') => (product.image_url
    ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name)}" class="w-full h-full object-cover">`
    : fallback);
