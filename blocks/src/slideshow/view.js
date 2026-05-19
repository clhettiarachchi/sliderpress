/**
 * SliderPress -- Slideshow block front-end initialisation.
 *
 * Loaded as viewScript -- executed once per page, not once per block instance.
 * Each .sp-slideshow element gets its own independent Swiper instance.
 *
 * @package SliderPress
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, A11y } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/a11y';
import 'swiper/css/pagination';

/**
 * Live region update
 *
 * Announces the current slide position to screen readers on every slide change.
 *
 * @param {HTMLElement} container The .sp-slideshow element.
 * @param {number}      index     Zero-based real slide index.
 * @param {number}      total     Total number of slides.
 */
function updateLiveRegion(container, index, total) {
    const region = container.querySelector('.sp-live-region');

    if (region) {
        region.textContent = `Slide ${index + 1} of ${total}`;
    }
}

/**
 * Single instance initialisation
 *
 * Reads configuration from data-* attributes and creates a Swiper instance
 * scoped to the given element. Called once per .sp-slideshow on the page.
 *
 * @param {HTMLElement} el The .sp-slideshow element.
 */
function initSlideshow(el) {
    const { transition, autoplay, interval, arrows, dots } = el.dataset;
    const slideCount = el.querySelectorAll('.sp-slide-wrap').length;

    new Swiper(el, {
        modules: [Navigation, Pagination, Autoplay, A11y],

        effect: transition || 'slide',
        loop: slideCount > 1,

        a11y: { enabled: true },

        navigation: arrows === 'true' ? {
            prevEl: el.querySelector('.sp-arrow--prev'),
            nextEl: el.querySelector('.sp-arrow--next'),
        } : false,

        pagination: dots === 'true' ? {
            el: el.querySelector('.sp-dots'),
            bulletClass: 'sp-dot swiper-pagination-bullet',
            bulletActiveClass: 'is-active',
            clickable: true,
        } : false,

        autoplay: autoplay === 'true' ? {
            delay: parseInt(interval, 10) || 5000,
            disableOnInteraction: false,
        } : false,

        on: {
            slideChange(swiper) {
                updateLiveRegion(el, swiper.realIndex, slideCount);
            },
        },
    });
}

/**
 * Page initialisation
 *
 * viewScript may run after DOMContentLoaded has already fired (e.g. when
 * loaded via a module script tag), so we check readyState before deferring.
 */
function init() {
    document.querySelectorAll('.sp-slideshow').forEach(initSlideshow);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}