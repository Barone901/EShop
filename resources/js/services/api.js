/**
 * ApiService
 * - jednotný wrapper pre všetky API volania (/api/...)
 * - CSRF sa berie z meta tagu
 */
const API_BASE_URL = '/api';

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

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

        // Server môže vrátiť prázdne body
        const text = await response.text();
        const data = text ? JSON.parse(text) : null;

        if (!response.ok) {
            const error = new Error(data?.message || 'Nastala chyba');
            error.status = response.status;
            error.data = data;
            throw error;
        }

        return data;
    },

    get(endpoint, params = {}) {
        const query = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                query.append(key, value);
            }
        });

        const queryString = query.toString();

        return this.request(queryString ? `${endpoint}?${queryString}` : endpoint, { method: 'GET' });
    },

    post(endpoint, data = {}) {
        return this.request(endpoint, { method: 'POST', body: JSON.stringify(data) });
    },
};

export default ApiService;
