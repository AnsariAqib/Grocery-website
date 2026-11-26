import { ref, onMounted, onUnmounted } from 'vue';

/**
 * Number of masonry columns for the category card stacks.
 *
 * The category pages split their cards into independent column arrays so that
 * expanding one card only pushes the cards below it in the same column. That
 * split happens in JS, so the column count has to be reactive — a CSS-only
 * media query would leave four DOM columns squeezed into two grid tracks and
 * scramble the reading order on a phone.
 *
 * Breakpoints mirror the ones in the page stylesheets: 1024px and 768px.
 */
export function useColumnCount({ desktop = 4, tablet = 3, phone = 2 } = {}) {
  const resolve = () => {
    if (typeof window === 'undefined') return desktop;
    if (window.matchMedia('(max-width: 768px)').matches) return phone;
    if (window.matchMedia('(max-width: 1024px)').matches) return tablet;
    return desktop;
  };

  const columnCount = ref(resolve());
  const update = () => { columnCount.value = resolve(); };

  onMounted(() => {
    update();
    window.addEventListener('resize', update, { passive: true });
    window.addEventListener('orientationchange', update);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', update);
    window.removeEventListener('orientationchange', update);
  });

  return { columnCount };
}

/**
 * Deal `items` round-robin into `count` column arrays.
 */
export function splitIntoColumns(items, count) {
  const cols = Array.from({ length: Math.max(1, count) }, () => []);
  (items || []).forEach((item, index) => {
    cols[index % cols.length].push(item);
  });

  return cols;
}
