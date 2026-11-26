import Lenis from 'lenis';
import { router } from '@inertiajs/vue3';

let lenisInstance = null;
let rafId = null;
let isInitialized = false;

export function getLenis() {
  return lenisInstance;
}

export function scrollTo(target, options = {}) {
  if (lenisInstance) {
    lenisInstance.scrollTo(target, options);
  } else {
    if (typeof target === 'string') {
      const el = document.querySelector(target);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    } else if (typeof target === 'number') {
      window.scrollTo({ top: target, behavior: 'smooth' });
    } else if (target instanceof HTMLElement) {
      target.scrollIntoView({ behavior: 'smooth' });
    }
  }
}

/**
 * Inject only the Lenis styles we actually need — crucially WITHOUT the
 * `overflow: clip` rule from the stock lenis.css that kills position: sticky.
 */
function injectLenisStyles() {
  if (document.getElementById('lenis-custom-styles')) return;
  const style = document.createElement('style');
  style.id = 'lenis-custom-styles';
  style.textContent = `
    html.lenis, html.lenis body { height: auto; }
    .lenis [data-lenis-prevent],
    .lenis [data-lenis-prevent-wheel],
    .lenis [data-lenis-prevent-touch] { overscroll-behavior: contain; }
    .lenis.lenis-smooth iframe { pointer-events: none; }
  `;
  document.head.appendChild(style);
}

/**
 * Site-wide high-performance smooth scrolling using Lenis.
 *
 * We disable autoToggle so Lenis never sets overflow: clip on <html>, which
 * would break position: sticky (used by the B2B Order Process rail, the
 * sticky header, etc.).
 */
export function useSmoothScroll() {
  if (typeof window === 'undefined') return () => {};

  if (isInitialized && lenisInstance) {
    return () => {};
  }

  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');

  if (motion.matches) {
    return () => {};
  }

  try {
    injectLenisStyles();

    const lenis = new Lenis({
      duration: 1.1,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: 'vertical',
      gestureOrientation: 'vertical',
      smoothWheel: true,
      wheelMultiplier: 1.0,
      touchMultiplier: 1.2,
      infinite: false,
      autoResize: true,
      syncTouch: false,
      autoToggle: false,   // ← prevents Lenis from setting overflow:clip on html
    });

    lenisInstance = lenis;
    isInitialized = true;

    // Continuous RAF loop
    function update(time) {
      if (lenisInstance) {
        lenisInstance.raf(time);
        rafId = requestAnimationFrame(update);
      }
    }
    rafId = requestAnimationFrame(update);

    // Sync Inertia page navigation
    const unbindStart = router.on('start', () => {
      if (lenisInstance) {
        lenisInstance.stop();
      }
    });

    const unbindNavigate = router.on('navigate', () => {
      if (lenisInstance) {
        lenisInstance.start();
        requestAnimationFrame(() => {
          lenisInstance.scrollTo(0, { immediate: true });
          lenisInstance.resize();
        });
      }
    });

    // Resize handling
    const onResize = () => {
      if (lenisInstance) {
        lenisInstance.resize();
      }
    };
    window.addEventListener('resize', onResize, { passive: true });

    // Smooth scroll for anchor links (#href)
    const onAnchorClick = (e) => {
      const link = e.target.closest('a[href^="#"]');
      if (!link) return;
      const href = link.getAttribute('href');
      if (href && href !== '#' && href.startsWith('#')) {
        const targetEl = document.querySelector(href);
        if (targetEl) {
          e.preventDefault();
          scrollTo(targetEl, { offset: -80 });
        }
      }
    };
    document.addEventListener('click', onAnchorClick);

    return () => {
      if (rafId) cancelAnimationFrame(rafId);
      unbindStart();
      unbindNavigate();
      window.removeEventListener('resize', onResize);
      document.removeEventListener('click', onAnchorClick);
      if (lenisInstance) {
        lenisInstance.destroy();
        lenisInstance = null;
      }
      isInitialized = false;
    };
  } catch (err) {
    console.error('Failed to initialize Lenis smooth scroll:', err);
    return () => {};
  }
}
