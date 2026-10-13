/**
 * Menu modul
 * - mobilne menu
 * - account dropdown (desktop)
 * - mobile profile dropdown
 * - zatvaranie ostatnych menu pri otvoreni jedneho
 */
const TRANSITION_MS = 200;

const showAnimated = (el) => {
    el.classList.remove('hidden');
    setTimeout(() => {
        el.classList.remove('opacity-0', 'translate-y-2');
        el.classList.add('opacity-100', 'translate-y-0');
    }, 10);
};

const hideAnimated = (el) => {
    el.classList.add('opacity-0', 'translate-y-2');
    el.classList.remove('opacity-100', 'translate-y-0');
    setTimeout(() => el.classList.add('hidden'), TRANSITION_MS);
};

/**
 * Vytvori dropdown ovladany tlacidlom (klik = prepnut, Escape = zavriet).
 * Ak je zadany wrapperId, dropdown sa zavrie aj kliknutim mimo neho.
 * Vrati funkciu close() alebo null, ak prvky na stranke nie su.
 */
const createDropdown = ({ btnId, dropdownId, arrowId, wrapperId = null, onOpen }) => {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    const arrow = document.getElementById(arrowId);

    if (!btn || !dropdown) return null;

    const isOpen = () => btn.getAttribute('aria-expanded') === 'true';

    const open = () => {
        onOpen();
        btn.setAttribute('aria-expanded', 'true');
        arrow?.classList.add('rotate-180');
        showAnimated(dropdown);
    };

    const close = () => {
        if (!isOpen()) return;

        btn.setAttribute('aria-expanded', 'false');
        arrow?.classList.remove('rotate-180');
        hideAnimated(dropdown);
    };

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (isOpen()) close();
        else open();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });

    if (wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        document.addEventListener('click', (e) => {
            if (wrapper && !wrapper.contains(e.target)) close();
        });
    }

    return close;
};

const Menu = {
    closeAccount: null,
    closeMobileProfile: null,

    init() {
        this.closeAccount = createDropdown({
            btnId: 'account-btn',
            dropdownId: 'account-dropdown',
            arrowId: 'account-arrow',
            onOpen: () => this.closeOthers('account'),
        });

        this.closeMobileProfile = createDropdown({
            btnId: 'mobile-profile-btn',
            dropdownId: 'mobile-profile-dropdown',
            arrowId: 'mobile-profile-arrow',
            wrapperId: 'mobile-profile-wrapper',
            onOpen: () => this.closeOthers('mobile-profile'),
        });

        this.initMobileMenu();
    },

    initMobileMenu() {
        const btn = document.getElementById('mobile-menu-btn');
        const nav = document.getElementById('mobile-nav');
        if (!btn || !nav) return;

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            this.closeOthers('mobile-nav');

            nav.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', nav.classList.contains('hidden') ? 'false' : 'true');
        });

        // Klik mimo menu ho zavrie
        document.addEventListener('click', (e) => {
            if (!nav.classList.contains('hidden') && !nav.contains(e.target) && !btn.contains(e.target)) {
                this.closeMobileNav();
            }
        });
    },

    /**
     * Zavrie vsetky ostatne menu/dropdowny okrem prave otvaraneho.
     */
    closeOthers(except) {
        if (except !== 'account') this.closeAccount?.();
        if (except !== 'mobile-profile') this.closeMobileProfile?.();
        if (except !== 'mobile-nav') this.closeMobileNav();
    },

    closeMobileNav() {
        const nav = document.getElementById('mobile-nav');
        if (!nav) return;

        nav.classList.add('hidden');
        document.getElementById('mobile-menu-btn')?.setAttribute('aria-expanded', 'false');
    },
};

export default Menu;
