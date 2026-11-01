/**
 * Spoločné HTML šablóny pre moduly, ktoré vykresľujú obsah cez innerHTML.
 * Triedy zodpovedajú Blade komponentom (x-button, x-ui.badge, x-shop.stock-badge, x-ui.loading),
 * aby rovnaký prvok vyzeral rovnako v Blade aj v JS.
 *
 * [AI] Vygenerované s pomocou AI (Claude).
 *
 * [AI] Upravené s pomocou AI (Claude): buttonClass(), stockBadge(), spinner() podľa Blade komponentov.
 */
import { escapeHtml } from './escape-html';

const ICON_PATHS = {
    cart: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
    trash: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    close: 'M6 18L18 6M6 6l12 12',
    arrowLeft: 'M10 19l-7-7m0 0l7-7m-7 7h18',
};

export const icon = (name, className = 'size-4', strokeWidth = 2) => `
    <svg class="${className}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="square" stroke-width="${strokeWidth}" d="${ICON_PATHS[name]}"/>
    </svg>
`;

// Rovnaké triedy ako x-button (resources/views/components/button.blade.php)
const BUTTON_BASE = 'inline-flex items-center justify-center gap-2 rounded-xs border font-display font-semibold uppercase tracking-wider transition-colors duration-150';
const BUTTON_SIZES = {
    md: 'min-h-11 px-5 text-base',
    lg: 'min-h-12 px-6 text-lg',
};
const BUTTON_VARIANTS = {
    primary: 'border-transparent bg-brand-600 text-white hover:bg-brand-700',
    secondary: 'border-line-strong bg-white text-text hover:border-text',
};

export const buttonClass = (variant = 'primary', size = 'md', extra = '') =>
    `${BUTTON_BASE} ${BUTTON_SIZES[size]} ${BUTTON_VARIANTS[variant]} ${extra}`.trim();

// Rovnaké stavy ako x-shop.stock-badge (vypredané / posledné kusy / skladom)
const BADGE_BASE = 'inline-flex items-center gap-1.5 rounded-xs px-2 py-1 font-mono text-xs font-medium uppercase tracking-wider';
const BADGE_VARIANTS = {
    ok: 'bg-ok-50 text-ok-700',
    warn: 'bg-warn-50 text-warn-700',
    danger: 'bg-danger-50 text-danger-700',
};

const badge = (variant, text) =>
    `<span class="${BADGE_BASE} ${BADGE_VARIANTS[variant]}"><span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>${escapeHtml(text)}</span>`;

export const stockBadge = (product) => {
    if (!product.in_stock) return badge('danger', 'Out of Stock');
    if (product.low_stock) return badge('warn', `Last items: ${product.stock}`);

    return badge('ok', `In Stock: ${product.stock}`);
};

// Rovnaký spinner ako x-ui.loading
export const spinner = (className = 'size-8') =>
    `<div class="${className} animate-spin rounded-full border-2 border-line border-t-brand-600 motion-reduce:animate-none" aria-hidden="true"></div>`;

/**
 * Obrázok produktu; bez obrázka sa vráti fallback (predvolene prázdna sivá plocha kontajnera).
 */
export const productImage = (product, fallback = '') => (product.image_url
    ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name)}" class="size-full object-contain p-1 mix-blend-multiply">`
    : fallback);
