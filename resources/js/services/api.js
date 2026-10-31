/**
 * ApiService
 * - jednotný wrapper pre všetky API volania (/api/...)
 * - CSRF sa berie z meta tagu
 *
 * [AI] Upravené s pomocou AI (Claude): odolnosť voči odpovediam, ktoré nie sú JSON, hláška pri 419, AbortSignal.
 */
const API_BASE_URL = '/api';

const DEFAULT_ERROR_MESSAGE = 'Something went wrong. Please try again.';
const SESSION_EXPIRED_MESSAGE = 'Your session has expired. Please refresh the page.';

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

/**
 * Prázdne body vráti ako null, neplatný JSON (napr. HTML chybová stránka) ako undefined.
 */
const parseJson = (text) => {
    if (!text) return null;

    try {
        return JSON.parse(text);
    } catch {
        return undefined;
    }
};

const ApiService = {
    async request(endpoint, options = {}) {
        const url = endpoint.startsWith('http')
            ? endpoint
            : `${API_BASE_URL}${endpoint}`;

        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
                ...options.headers,
            },
        });

        const data = parseJson(await response.text());

        if (!response.ok || data === undefined) {
            const message = response.status === 419
                ? SESSION_EXPIRED_MESSAGE
                : data?.message || DEFAULT_ERROR_MESSAGE;

            const error = new Error(message);
            error.status = response.status;
            error.data = data;
            throw error;
        }

        return data;
    },

    get(endpoint, params = {}, options = {}) {
        const query = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                query.append(key, value);
            }
        });

        const queryString = query.toString();

        return this.request(queryString ? `${endpoint}?${queryString}` : endpoint, { ...options, method: 'GET' });
    },

    post(endpoint, data = {}) {
        return this.request(endpoint, { method: 'POST', body: JSON.stringify(data) });
    },
};

export default ApiService;
