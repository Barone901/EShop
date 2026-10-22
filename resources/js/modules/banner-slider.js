/**
 * Banner Slider Module
 * Automatické posúvanie bannerov s možnosťou manuálneho ovládania
 */

export default class BannerSlider {
    constructor(options = {}) {
        this.container = document.querySelector('#banner-slider');
        if (!this.container) return;

        this.slidesContainer = this.container.querySelector('.banner-slides');
        this.slides = this.container.querySelectorAll('.banner-slide');
        this.dots = this.container.querySelectorAll('.banner-dot');
        this.prevBtn = this.container.querySelector('.banner-prev');
        this.nextBtn = this.container.querySelector('.banner-next');

        this.currentIndex = 0;
        this.totalSlides = this.slides.length;
        this.autoplayInterval = options.interval || 5000; // 5 sekúnd default
        this.autoplayTimer = null;
        this.isHovered = false;

        if (this.totalSlides > 1) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        this.startAutoplay();
    }

    bindEvents() {
        // Navigation buttons
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                this.prev();
                this.resetAutoplay();
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                this.next();
                this.resetAutoplay();
            });
        }

        // Dots navigation
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                this.goToSlide(index);
                this.resetAutoplay();
            });
        });

        // Pause on hover
        this.container.addEventListener('mouseenter', () => {
            this.isHovered = true;
            this.stopAutoplay();
        });

        this.container.addEventListener('mouseleave', () => {
            this.isHovered = false;
            this.startAutoplay();
        });

        // Touch/Swipe support
        let touchStartX = 0;
        let touchEndX = 0;

        this.slidesContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.slidesContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe(touchStartX, touchEndX);
        }, { passive: true });

        // Keyboard navigation
        this.container.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.prev();
                this.resetAutoplay();
            } else if (e.key === 'ArrowRight') {
                this.next();
                this.resetAutoplay();
            }
        });
    }

    handleSwipe(startX, endX) {
        const threshold = 50;
        const diff = startX - endX;

        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                this.next();
            } else {
                this.prev();
            }
            this.resetAutoplay();
        }
    }

    goToSlide(index) {
        if (index < 0) {
            this.currentIndex = this.totalSlides - 1;
        } else if (index >= this.totalSlides) {
            this.currentIndex = 0;
        } else {
            this.currentIndex = index;
        }

        this.updateSlider();
    }

    next() {
        this.goToSlide(this.currentIndex + 1);
    }

    prev() {
        this.goToSlide(this.currentIndex - 1);
    }

    updateSlider() {
        // Move slides
        const translateX = -this.currentIndex * 100;
        this.slidesContainer.style.transform = `translateX(${translateX}%)`;

        // Aktivna bodka sa styluje cez [aria-current="true"] (app.css)
        this.dots.forEach((dot, index) => {
            dot.setAttribute('aria-current', index === this.currentIndex ? 'true' : 'false');
        });
    }

    startAutoplay() {
        if (this.isHovered || this.totalSlides <= 1) return;

        this.stopAutoplay();
        this.autoplayTimer = setInterval(() => {
            this.next();
        }, this.autoplayInterval);
    }

    stopAutoplay() {
        if (this.autoplayTimer) {
            clearInterval(this.autoplayTimer);
            this.autoplayTimer = null;
        }
    }

    resetAutoplay() {
        this.stopAutoplay();
        if (!this.isHovered) {
            this.startAutoplay();
        }
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    new BannerSlider({
        interval: 5000 // 5 sec. between slides
    });
});
