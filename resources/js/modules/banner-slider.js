/**
 * Banner Slider Module
 *
 * Automatické posúvanie bannerov s možnosťou manuálneho ovládania
 * (šípky, bodky, klávesnica, swipe). Pri hoveri sa autoplay pozastaví.
 *
 * [AI] Upravené s pomocou AI (Claude): prepísané z triedy na modul s init() ako ostatné moduly.
 */

const AUTOPLAY_INTERVAL_MS = 5000;
const SWIPE_THRESHOLD_PX = 50;

const BannerSlider = {
    container: null,
    slidesContainer: null,
    dots: [],
    currentIndex: 0,
    totalSlides: 0,
    autoplayTimer: null,
    isHovered: false,

    init() {
        this.container = document.getElementById('banner-slider');
        if (!this.container) return;

        this.slidesContainer = this.container.querySelector('.banner-slides');
        this.dots = this.container.querySelectorAll('.banner-dot');
        this.totalSlides = this.container.querySelectorAll('.banner-slide').length;

        if (this.totalSlides <= 1) return;

        this.bindEvents();
        this.startAutoplay();
    },

    bindEvents() {
        this.container.querySelector('.banner-prev')?.addEventListener('click', () => this.navigate(this.currentIndex - 1));
        this.container.querySelector('.banner-next')?.addEventListener('click', () => this.navigate(this.currentIndex + 1));

        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.navigate(index));
        });

        // Pozastavenie pri hoveri
        this.container.addEventListener('mouseenter', () => {
            this.isHovered = true;
            this.stopAutoplay();
        });

        this.container.addEventListener('mouseleave', () => {
            this.isHovered = false;
            this.startAutoplay();
        });

        // Swipe na dotykových zariadeniach
        let touchStartX = 0;

        this.slidesContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.slidesContainer.addEventListener('touchend', (e) => {
            const diff = touchStartX - e.changedTouches[0].screenX;

            if (Math.abs(diff) > SWIPE_THRESHOLD_PX) {
                this.navigate(this.currentIndex + (diff > 0 ? 1 : -1));
            }
        }, { passive: true });

        // Ovládanie klávesnicou
        this.container.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.navigate(this.currentIndex - 1);
            else if (e.key === 'ArrowRight') this.navigate(this.currentIndex + 1);
        });
    },

    /**
     * Manuálny posun – po ňom sa interval autoplay začne počítať odznova.
     */
    navigate(index) {
        this.goToSlide(index);
        this.startAutoplay();
    },

    goToSlide(index) {
        // Za posledným slidom nasleduje prvý a naopak
        this.currentIndex = (index + this.totalSlides) % this.totalSlides;

        this.slidesContainer.style.transform = `translateX(${-this.currentIndex * 100}%)`;

        // Aktívna bodka sa štýluje cez [aria-current="true"] (app.css)
        this.dots.forEach((dot, i) => {
            dot.setAttribute('aria-current', i === this.currentIndex ? 'true' : 'false');
        });
    },

    startAutoplay() {
        this.stopAutoplay();
        if (this.isHovered) return;

        this.autoplayTimer = setInterval(() => this.goToSlide(this.currentIndex + 1), AUTOPLAY_INTERVAL_MS);
    },

    stopAutoplay() {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
    },
};

export default BannerSlider;
