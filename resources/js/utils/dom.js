/**
 * Zdieľané document listenery.
 *
 * Každý typ udalosti má na document iba jeden listener, moduly si doň len
 * registrujú svoje handlery (delegované akcie, Escape, klik mimo prvku).
 *
 * [AI] Vygenerované s pomocou AI (Claude).
 */
const handlers = {};

const listen = (type, handler) => {
    if (!handlers[type]) {
        handlers[type] = [];
        document.addEventListener(type, (e) => handlers[type].forEach((fn) => fn(e)));
    }

    handlers[type].push(handler);
};

/**
 * Delegovaná udalosť: handler dostane event a najbližší prvok zodpovedajúci selektoru.
 */
export const delegate = (type, selector, handler) => {
    listen(type, (e) => {
        const target = e.target instanceof Element ? e.target.closest(selector) : null;
        if (target) handler(e, target);
    });
};

export const onEscape = (handler) => {
    listen('keydown', (e) => {
        if (e.key === 'Escape') handler(e);
    });
};

/**
 * Zavolá handler pri kliknutí mimo všetkých zadaných prvkov.
 */
export const onOutsideClick = (elements, handler) => {
    listen('click', (e) => {
        if (!elements.some((el) => el?.contains(e.target))) handler(e);
    });
};
