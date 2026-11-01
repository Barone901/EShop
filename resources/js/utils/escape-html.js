/**
 * Escapuje hodnotu pred vložením do HTML šablóny (innerHTML).
 */
const HTML_ENTITIES = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
};

export const escapeHtml = (value) => {
    if (value === null || value === undefined) return '';

    return String(value).replace(/[&<>"']/g, (char) => HTML_ENTITIES[char]);
};
