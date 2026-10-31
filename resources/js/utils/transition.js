/**
 * Animované zobrazenie a skrytie prvku (Tailwind triedy hidden, opacity a translate).
 *
 * Čakajúci časovač prvku sa pri každej zmene zruší, takže rýchle zatvorenie
 * a opätovné otvorenie neskončí skrytým prvkom.
 *
 * [AI] Vygenerované s pomocou AI (Claude).
 */
const TRANSITION_MS = 200;

const timers = new WeakMap();

const schedule = (el, callback, delay) => {
    clearTimeout(timers.get(el));
    timers.set(el, setTimeout(callback, delay));
};

export const showAnimated = (el) => {
    el.classList.remove('hidden');

    // Krátke oneskorenie, aby prehliadač prvok vykreslil pred zmenou opacity (inak sa prechod neprehrá)
    schedule(el, () => {
        el.classList.remove('opacity-0', 'translate-y-2');
        el.classList.add('opacity-100', 'translate-y-0');
    }, 10);
};

export const hideAnimated = (el) => {
    if (el.classList.contains('hidden')) return;

    el.classList.add('opacity-0', 'translate-y-2');
    el.classList.remove('opacity-100', 'translate-y-0');
    schedule(el, () => el.classList.add('hidden'), TRANSITION_MS);
};
