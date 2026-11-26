import { onMounted, onUnmounted } from 'vue';

/**
 * Scroll parallax for decorative elements.
 *
 * The one rule here: this never writes `transform` directly. Each element gets
 * a `--parallax-y` custom property and the stylesheet decides how to fold it in.
 * That is what keeps the existing transforms alive — `.watermark-backdrop` owns
 * a `translateX(-50%)` for its centering and `.hero-bg-img` owns the Ken Burns
 * `scale()` animation; clobbering either from JS would break centering or stop
 * the drift.
 *
 * Elements opt in with `data-parallax="<max offset in px>"`.
 *
 * Only ever moves elements that are absolutely positioned and clipped by an
 * ancestor, so nothing here can shift layout.
 */
export function useParallax(selector = '[data-parallax]') {
  let targets = [];
  let ticking = false;
  let active = false;
  let media = null;

  // Parallax is desktop-only: `.watermark-backdrop` is `display:none` below
  // 768px anyway, and small screens get the least out of it for the most jank.
  function shouldRun() {
    return window.innerWidth >= 768
      && !(media && media.matches);
  }

  function update() {
    ticking = false;
    const viewport = window.innerHeight;
    if (!viewport) return;

    for (const { el, distance } of targets) {
      const rect = el.getBoundingClientRect();

      // -1 while the element is still below the fold, 0 at centre screen,
      // +1 once it has scrolled past the top.
      const progress = 1 - 2 * ((rect.top + rect.height / 2) / viewport);
      const clamped = Math.max(-1, Math.min(1, progress));

      el.style.setProperty('--parallax-y', `${(clamped * distance).toFixed(2)}px`);
    }
  }

  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(update);
  }

  function reset() {
    for (const { el } of targets) {
      el.style.removeProperty('--parallax-y');
    }
  }

  function start() {
    if (active) return;
    active = true;
    window.addEventListener('scroll', onScroll, { passive: true });
    update();
  }

  function stop() {
    if (!active) return;
    active = false;
    window.removeEventListener('scroll', onScroll);
    reset();
  }

  function onResize() {
    if (shouldRun()) {
      start();
      onScroll();
    } else {
      stop();
    }
  }

  onMounted(() => {
    if (!('requestAnimationFrame' in window)) return;

    media = window.matchMedia('(prefers-reduced-motion: reduce)');

    targets = Array.from(document.querySelectorAll(selector)).map(el => ({
      el,
      distance: Number(el.dataset.parallax) || 24,
    }));

    if (!targets.length) return;

    if (shouldRun()) start();
    window.addEventListener('resize', onResize, { passive: true });
    media.addEventListener?.('change', onResize);
  });

  onUnmounted(() => {
    stop();
    window.removeEventListener('resize', onResize);
    media?.removeEventListener?.('change', onResize);
    targets = [];
  });
}
