<template>
  <Teleport to="body">
    <Transition name="promo-popup">
      <div
        v-if="isOpen"
        class="promo-popup-overlay"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="`promo-popup-headline-${popup.id}`"
        @click.self="close"
        data-lenis-prevent
      >
        <div class="promo-popup" :class="{ 'has-media': hasImage }" ref="dialogRef">
          <button class="promo-popup-close" type="button" aria-label="Close offer" @click="close">
            <i class="fa-solid fa-xmark"></i>
          </button>

          <!-- Media panel. Sits beside the copy on desktop and above it on
               phones; drops out entirely when no image is uploaded, and the
               card narrows to a single centred column. -->
          <div v-if="hasImage" class="promo-popup-media">
            <img :src="popup.image" :alt="popup.headline" class="promo-popup-image">
            <span class="promo-popup-media-scrim" aria-hidden="true"></span>
          </div>

          <div class="promo-popup-body">
            <span class="promo-popup-glow" aria-hidden="true"></span>

            <div class="promo-popup-copy">
              <span v-if="popup.eyebrow" class="promo-popup-eyebrow">
                <i class="fa-solid fa-bolt"></i>{{ popup.eyebrow }}
              </span>
              <h2 class="promo-popup-headline" :id="`promo-popup-headline-${popup.id}`">{{ popup.headline }}</h2>
              <span class="promo-popup-rule" aria-hidden="true"></span>
              <p v-if="popup.body" class="promo-popup-text">{{ popup.body }}</p>
            </div>

            <div v-if="popup.ctaText" class="promo-popup-actions">
              <button v-if="popup.ctaOpensEnquiry" type="button" class="promo-popup-cta" @click="claim">
                {{ popup.ctaText }} <i class="fa-solid fa-arrow-right"></i>
              </button>
              <Link v-else-if="isInternal" :href="popup.ctaUrl || '#'" class="promo-popup-cta" @click="close">
                {{ popup.ctaText }} <i class="fa-solid fa-arrow-right"></i>
              </Link>
              <a v-else :href="popup.ctaUrl" target="_blank" rel="noopener" class="promo-popup-cta" @click="close">
                {{ popup.ctaText }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </a>

              <button type="button" class="promo-popup-dismiss" @click="close">No thanks</button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { isWithinSchedule, useServerClock } from '@/composables/useLiveSchedule';

/**
 * The scheduled interstitial. The server has already picked at most one popup
 * for this page (HandleInertiaRequests -> PromoPopup::forPage), so this
 * component only decides *when* to open it and whether this visitor has
 * already had their turn.
 */
const emit = defineEmits(['openEnquiry']);

const page = usePage();
const now = useServerClock();

const popup = computed(() => page.props.promoPopup ?? null);
const isOpen = ref(false);
const dialogRef = ref(null);
let timer = null;

const isInternal = computed(() => {
  const url = popup.value?.ctaUrl ?? '';
  return url.startsWith('/');
});

// Drives the two layouts: a wide split card with the image, a narrower centred
// one without. An image-less popup in the wide frame just looks unfinished.
const hasImage = computed(() => Boolean(popup.value?.image));

// Frequency is enforced client-side because it is a per-visitor fact, not a
// per-record one. once_per_session uses sessionStorage (dies with the tab);
// the rest use localStorage so they outlive it.
const storageKey = computed(() => `jt_popup_${popup.value?.id}`);

function store(frequency) {
  return frequency === 'once_per_session' ? window.sessionStorage : window.localStorage;
}

function alreadySeen() {
  const { frequency } = popup.value;
  if (frequency === 'always') return false;

  try {
    // once_per_visit is deliberately in-memory only: a reload is a new visit,
    // navigating between pages within the SPA is not.
    if (frequency === 'once_per_visit') return window.__jtPopupsSeen?.has(popup.value.id) ?? false;

    const seen = store(frequency).getItem(storageKey.value);
    if (!seen) return false;
    if (frequency !== 'once_per_day') return true;

    return Date.now() - Number(seen) < 24 * 60 * 60 * 1000;
  } catch {
    // Private mode or blocked storage — better to show it than to crash.
    return false;
  }
}

function markSeen() {
  const { frequency } = popup.value;
  if (frequency === 'always') return;

  try {
    if (frequency === 'once_per_visit') {
      window.__jtPopupsSeen = window.__jtPopupsSeen ?? new Set();
      window.__jtPopupsSeen.add(popup.value.id);
      return;
    }

    store(frequency).setItem(storageKey.value, String(Date.now()));
  } catch {
    // Nothing to do — it will simply show again.
  }
}

function open() {
  // Re-check the window at the moment it would open: the tab may have sat
  // here long enough for the campaign to end.
  if (!popup.value || !isWithinSchedule(popup.value, now.value)) return;

  isOpen.value = true;
  markSeen();
  document.body.style.overflow = 'hidden';
}

function close() {
  isOpen.value = false;
  document.body.style.overflow = '';
}

function claim() {
  close();
  emit('openEnquiry');
}

function onKeydown(event) {
  if (event.key === 'Escape' && isOpen.value) close();
}

onMounted(() => {
  if (!popup.value || alreadySeen() || !isWithinSchedule(popup.value, now.value)) return;

  const delay = Math.max(0, Number(popup.value.delaySeconds) || 0) * 1000;
  timer = setTimeout(open, delay);
  document.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
  if (timer) clearTimeout(timer);
  document.removeEventListener('keydown', onKeydown);
  document.body.style.overflow = '';
});
</script>

<style scoped>
/* ============================================================ overlay + frame */
.promo-popup-overlay {
  position: fixed;
  inset: 0;
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
  background: rgba(6, 20, 39, 0.74);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  overscroll-behavior: contain;
}

.promo-popup {
  position: relative;
  width: 100%;
  max-width: 520px;
  max-height: min(92vh, 760px);
  overflow-y: auto;
  overscroll-behavior: contain;
  background: var(--bg-card);
  border-radius: var(--radius-xl);
  box-shadow: 0 30px 80px rgba(6, 20, 39, 0.42);
  scrollbar-width: thin;
  scrollbar-color: rgba(228, 101, 46, 0.35) transparent;
}

/* With artwork the card becomes a wide two-panel piece: image left, offer
   right. Without one it stays the narrow single column above. */
.promo-popup.has-media {
  max-width: 880px;
  display: grid;
  grid-template-columns: minmax(0, 0.92fr) minmax(0, 1fr);
  align-items: stretch;
  overflow: hidden;
}

/* ============================================================ media panel */
.promo-popup-media {
  position: relative;
  min-height: 100%;
  background: var(--dark-navy);
}

.promo-popup-image {
  display: block;
  width: 100%;
  height: 100%;
  min-height: 340px;
  object-fit: cover;
}

/* Keeps the seam between photo and card from reading as a hard cut. */
.promo-popup-media-scrim {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(105deg, rgba(6, 20, 39, 0.28) 0%, rgba(6, 20, 39, 0) 45%);
}

/* ============================================================ copy panel */
.promo-popup-body {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 26px;
  padding: 48px 44px;
  overflow: hidden;
}

.promo-popup:not(.has-media) .promo-popup-body {
  padding: 48px 40px 40px;
  text-align: center;
  align-items: center;
}

/* Warm bloom behind the copy, echoing the section backdrops on the site. */
.promo-popup-glow {
  position: absolute;
  top: -120px;
  right: -110px;
  width: 300px;
  height: 300px;
  border-radius: 50%;
  pointer-events: none;
  background: radial-gradient(circle, rgba(228, 101, 46, 0.13) 0%, rgba(228, 101, 46, 0) 70%);
}

.promo-popup-copy {
  position: relative;
}

.promo-popup-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 7px 14px;
  margin-bottom: 16px;
  border-radius: 999px;
  background: var(--primary-orange-light);
  color: var(--primary-orange);
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.promo-popup-eyebrow i {
  font-size: 10px;
}

.promo-popup-headline {
  font-family: var(--font-heading);
  font-size: 32px;
  font-weight: 800;
  line-height: 1.18;
  letter-spacing: -0.02em;
  color: var(--dark-navy);
  margin: 0;
}

.promo-popup-rule {
  display: block;
  width: 54px;
  height: 3px;
  margin: 18px 0;
  border-radius: 999px;
  background: linear-gradient(90deg, var(--primary-orange) 0%, rgba(228, 101, 46, 0.15) 100%);
}

.promo-popup:not(.has-media) .promo-popup-rule {
  margin-left: auto;
  margin-right: auto;
}

.promo-popup-text {
  font-size: 15px;
  line-height: 1.7;
  color: var(--text-muted);
  margin: 0;
}

/* ============================================================ actions */
.promo-popup-actions {
  position: relative;
  display: flex;
  align-items: center;
  gap: 18px;
}

.promo-popup:not(.has-media) .promo-popup-actions {
  flex-direction: column;
  gap: 12px;
  width: 100%;
}

.promo-popup-cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  padding: 15px 30px;
  border: none;
  border-radius: var(--radius-sm);
  background: var(--primary-orange);
  color: #FFFFFF;
  font-family: var(--font-main);
  font-size: 14.5px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  cursor: pointer;
  box-shadow: var(--shadow-orange);
  transition: background 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
}

.promo-popup:not(.has-media) .promo-popup-cta {
  width: 100%;
}

.promo-popup-cta:hover {
  background: var(--primary-orange-hover);
  box-shadow: var(--shadow-hover-orange);
  transform: translateY(-2px);
}

.promo-popup-cta i {
  font-size: 12px;
  transition: transform 0.25s ease;
}

.promo-popup-cta:hover i {
  transform: translateX(3px);
}

.promo-popup-dismiss {
  border: none;
  background: none;
  color: var(--text-light);
  font-family: var(--font-main);
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  padding: 4px 2px;
  transition: color 0.2s ease;
}

.promo-popup-dismiss:hover {
  color: var(--text-muted);
  text-decoration: underline;
}

/* ============================================================ close */
.promo-popup-close {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 2;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--border-color);
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.94);
  color: var(--dark-navy);
  font-size: 15px;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.promo-popup-close:hover {
  background: var(--primary-orange);
  border-color: var(--primary-orange);
  color: #FFFFFF;
  transform: rotate(90deg);
}

/* ============================================================ motion */
.promo-popup-enter-active,
.promo-popup-leave-active {
  transition: opacity 0.3s ease;
}

.promo-popup-enter-active .promo-popup,
.promo-popup-leave-active .promo-popup {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.promo-popup-enter-from,
.promo-popup-leave-to {
  opacity: 0;
}

.promo-popup-enter-from .promo-popup,
.promo-popup-leave-to .promo-popup {
  transform: translateY(24px) scale(0.96);
}

@media (prefers-reduced-motion: reduce) {
  .promo-popup-enter-active .promo-popup,
  .promo-popup-leave-active .promo-popup,
  .promo-popup-cta,
  .promo-popup-close {
    transition: none;
  }

  .promo-popup-enter-from .promo-popup,
  .promo-popup-leave-to .promo-popup {
    transform: none;
  }
}

/* ============================================================ responsive */
@media (max-width: 900px) {
  .promo-popup.has-media {
    max-width: 520px;
    grid-template-columns: minmax(0, 1fr);
  }

  /* Stacked, so the photo becomes a banner rather than a full-height panel. */
  .promo-popup-image {
    min-height: 0;
    aspect-ratio: 16 / 9;
    height: auto;
  }

  .promo-popup-media-scrim {
    background: linear-gradient(180deg, rgba(6, 20, 39, 0.22) 0%, rgba(6, 20, 39, 0) 55%);
  }

  .promo-popup-body,
  .promo-popup:not(.has-media) .promo-popup-body {
    padding: 34px 32px 32px;
    gap: 22px;
  }

  .promo-popup-headline {
    font-size: 27px;
  }
}

@media (max-width: 560px) {
  .promo-popup-overlay {
    padding: 16px 14px;
  }

  .promo-popup,
  .promo-popup.has-media {
    max-width: 100%;
    border-radius: var(--radius-lg);
  }

  .promo-popup-body,
  .promo-popup:not(.has-media) .promo-popup-body {
    padding: 26px 22px 24px;
    gap: 20px;
    text-align: center;
    align-items: center;
  }

  .promo-popup-rule {
    margin-left: auto;
    margin-right: auto;
  }

  .promo-popup-headline {
    font-size: 23px;
  }

  .promo-popup-text {
    font-size: 14px;
  }

  .promo-popup-actions {
    flex-direction: column;
    gap: 12px;
    width: 100%;
  }

  .promo-popup-cta {
    width: 100%;
    padding: 14px 24px;
  }

  .promo-popup-close {
    top: 12px;
    right: 12px;
    width: 34px;
    height: 34px;
  }
}
</style>
