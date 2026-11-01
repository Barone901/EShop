/**
 * Menu Module
 *
 * Všetky rozbaľovacie prvky v hlavičke:
 * - account dropdown (desktop) a mobile profile dropdown – klik na tlačidlo
 * - mobilné menu – klik na tlačidlo
 * - dropdown košíka – hover nad #cart-wrapper (tlačidlo je odkaz na stránku košíka)
 *
 * Otvorenie jedného prvku zatvorí ostatné, Escape a klik mimo prvku ho zatvorí.
 * Stav otvorenia drží aria-expanded na tlačidle.
 *
 * [AI] Upravené s pomocou AI (Claude).
 */
import { showAnimated, hideAnimated } from '../utils/transition';
import { byId, onEscape, onOutsideClick } from '../utils/dom';

const CART_HOVER_HIDE_DELAY_MS = 150;

const showInstant = (el) => el.classList.remove('hidden');
const hideInstant = (el) => el.classList.add('hidden');

/**
 * Vytvorí dropdown naviazaný na tlačidlo. Vráti null, ak prvky na stránke nie sú.
 */
const createDropdown = ({ btn, dropdown, arrow = null, show = showAnimated, hide = hideAnimated, onOpen }) => {
    if (!btn || !dropdown) return null;

    const isOpen = () => btn.getAttribute('aria-expanded') === 'true';

    const open = () => {
        if (isOpen()) return;

        onOpen();
        btn.setAttribute('aria-expanded', 'true');
        arrow?.classList.add('rotate-180');
        show(dropdown);
    };

    const close = () => {
        if (!isOpen()) return;

        btn.setAttribute('aria-expanded', 'false');
        arrow?.classList.remove('rotate-180');
        hide(dropdown);
    };

    onEscape(close);
    onOutsideClick([btn, dropdown], close);

    return { btn, open, close, toggle: () => (isOpen() ? close() : open()) };
};

const Menu = {
    dropdowns: {},

    init() {
        this.initClickDropdown('account', {
            btn: byId('account-btn'),
            dropdown: byId('account-dropdown'),
            arrow: byId('account-arrow'),
        });

        this.initClickDropdown('mobile-profile', {
            btn: byId('mobile-profile-btn'),
            dropdown: byId('mobile-profile-dropdown'),
            arrow: byId('mobile-profile-arrow'),
        });

        this.initClickDropdown('mobile-nav', {
            btn: byId('mobile-menu-btn'),
            dropdown: byId('mobile-nav'),
            show: showInstant,
            hide: hideInstant,
        });

        this.initCartDropdown(byId('cart-wrapper'), {
            btn: byId('cart-btn'),
            dropdown: byId('cart-dropdown'),
        });
    },

    register(name, options) {
        const dropdown = createDropdown({ ...options, onOpen: () => this.closeOthers(name) });
        if (dropdown) this.dropdowns[name] = dropdown;

        return dropdown;
    },

    initClickDropdown(name, options) {
        const dropdown = this.register(name, options);
        if (!dropdown) return;

        dropdown.btn.addEventListener('click', (e) => {
            e.preventDefault();
            dropdown.toggle();
        });
    },

    initCartDropdown(wrapper, options) {
        const dropdown = wrapper && this.register('cart', options);
        if (!dropdown) return;

        // Oneskorené skrytie, aby sa dalo prejsť myšou z tlačidla do dropdownu
        let hideTimer = null;

        wrapper.addEventListener('mouseenter', () => {
            clearTimeout(hideTimer);
            dropdown.open();
        });

        wrapper.addEventListener('mouseleave', () => {
            hideTimer = setTimeout(dropdown.close, CART_HOVER_HIDE_DELAY_MS);
        });
    },

    /**
     * Zatvorí všetky dropdowny okrem práve otváraného.
     */
    closeOthers(except) {
        Object.entries(this.dropdowns).forEach(([name, dropdown]) => {
            if (name !== except) dropdown.close();
        });
    },
};

export default Menu;
