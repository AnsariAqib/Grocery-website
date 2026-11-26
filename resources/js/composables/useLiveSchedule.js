import { computed, onMounted, onUnmounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Live publish/expire filtering for scheduled content.
 *
 * The controllers already drop anything outside its window, but that decision
 * is only as fresh as the last page load — an offers page left open past an
 * expiry kept showing the card. Every scheduled record therefore also carries
 * `publishesAt` / `expiresAt` as ISO-8601 with offset, and this ticker
 * re-evaluates them in place.
 *
 * Visitor clocks are not trusted: `serverTime` (shared by
 * HandleInertiaRequests) is compared against the browser clock once at mount,
 * and the difference is carried into every later comparison. A device running
 * days behind therefore still retires an offer at the right moment.
 */
const TICK_MS = 15000;

export function useServerClock(intervalMs = TICK_MS) {
  const page = usePage();

  // Positive when the browser clock runs behind the server's.
  const drift = ref(0);
  const now = ref(Date.now());
  let timer = null;

  const sync = () => {
    now.value = Date.now() + drift.value;
  };

  onMounted(() => {
    const server = Date.parse(page.props.serverTime ?? '');
    if (!Number.isNaN(server)) drift.value = server - Date.now();
    sync();

    timer = setInterval(sync, intervalMs);
    // A backgrounded tab throttles the interval, so re-check on the way back
    // rather than showing a stale card for however long the throttle lasted.
    document.addEventListener('visibilitychange', sync);
  });

  onUnmounted(() => {
    if (timer) clearInterval(timer);
    document.removeEventListener('visibilitychange', sync);
  });

  return now;
}

/** True while `nowMs` sits inside the record's window. Blank ends are open. */
export function isWithinSchedule(item, nowMs) {
  if (!item) return false;

  const start = Date.parse(item.publishesAt ?? '');
  if (!Number.isNaN(start) && nowMs < start) return false;

  const end = Date.parse(item.expiresAt ?? '');
  if (!Number.isNaN(end) && nowMs >= end) return false;

  return true;
}

/**
 * A ref-like list of the records still inside their window, kept in sync by
 * one shared ticker. `source` may be an array or any getter returning one.
 */
export function useLiveItems(source) {
  const now = useServerClock();
  const read = typeof source === 'function' ? source : () => source;

  return computed(() => (read() ?? []).filter(item => isWithinSchedule(item, now.value)));
}

/**
 * Every page reveals `.reveal*` elements through an IntersectionObserver bound
 * once in onMounted, so a card that only appears later — when its publish time
 * passes — would sit at the entrance animation's opacity 0 and never show.
 * Call this after such an insert to put the fresh nodes in their end state.
 */
export function activateReveals(root) {
  root?.querySelectorAll?.('.reveal:not(.active), .reveal-left:not(.active), .reveal-right:not(.active), .reveal-scale:not(.active)')
    .forEach(el => el.classList.add('active'));
}
