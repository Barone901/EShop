/**
 * Escapuje hodnotu pred vložením do HTML šablóny (innerHTML).
 *
 * [AI] Upravené s pomocou AI (Claude): pomenovaný export ako ostatné utils.
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
