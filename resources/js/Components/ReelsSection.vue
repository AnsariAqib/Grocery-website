<template>
  <div v-if="reels.length" class="reels-block">
    <div class="section-header center stagger">
      <span class="section-badge reveal"><i class="fa-brands fa-youtube"></i> Reels &amp; Shorts</span>
      <h2 class="section-title reveal">Latest Reels & Store Updates</h2>
      <p class="section-subtitle reveal">
        New arrivals, offer announcements and a look inside our stores — play them right here.
      </p>
    </div>

    <div class="reels-grid stagger">
      <div v-for="reel in reels" :key="reel.id" class="reel-card reveal">
        <div
          class="reel-frame"
          @mouseenter="startPreview(reel)"
          @mouseleave="stopPreview(reel)"
        >
          <!-- Nothing is requested from YouTube until the viewer hovers or
               clicks: a player weighs roughly a megabyte, so embedding several
               up front would cost more than the rest of the page put together.
               The iframe is created inside the event handler, which is why its
               autoplay is allowed. -->
          <iframe
            v-if="playingId === reel.id"
            :src="reel.embedUrl"
            class="reel-iframe"
            :title="reel.title || 'Reel'"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
          ></iframe>

          <template v-else>
            <!-- pointer-events are off so the overlay below keeps receiving the
                 hover and click; an iframe would otherwise swallow both. -->
            <iframe
              v-if="previewId === reel.id"
              :src="reel.previewUrl"
              class="reel-iframe reel-iframe-preview"
              :title="`${reel.title || 'Reel'} preview`"
              frameborder="0"
              allow="autoplay; encrypted-media"
              referrerpolicy="strict-origin-when-cross-origin"
              tabindex="-1"
              aria-hidden="true"
            ></iframe>

            <component
              :is="reel.embedUrl ? 'button' : 'a'"
              v-bind="reel.embedUrl
                ? { type: 'button' }
                : { href: reel.permalink || '#', target: '_blank', rel: 'noopener' }"
              class="reel-poster"
              :class="{ 'is-previewing': previewId === reel.id }"
              :aria-label="`Play reel${reel.title ? ': ' + reel.title : ''}`"
              @click="reel.embedUrl && play(reel.id)"
            >
              <img
                v-if="reel.cover && previewId !== reel.id"
                :src="covers[reel.id] || reel.cover"
                :alt="reel.title || 'Reel'"
                class="reel-cover"
                loading="lazy"
                @error="useFallbackCover(reel)"
              >
              <div v-else-if="previewId !== reel.id" class="reel-cover reel-cover-placeholder">
                <i class="fa-brands fa-youtube"></i>
              </div>

              <span class="reel-scrim"></span>
              <span class="reel-play"><i class="fa-solid fa-play"></i></span>
              <span v-if="previewId === reel.id" class="reel-sound-hint">
                <i class="fa-solid fa-volume-xmark"></i> Click for sound
              </span>
            </component>
          </template>
        </div>

        <div v-if="reel.title || reel.caption" class="reel-meta">
          <h3 v-if="reel.title" class="reel-title">{{ reel.title }}</h3>
          <p v-if="reel.caption" class="reel-caption">{{ reel.caption }}</p>
        </div>

        <a v-if="reel.permalink" :href="reel.permalink" target="_blank" rel="noopener" class="reel-link">
          <i class="fa-brands fa-youtube"></i> Watch on YouTube
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onUnmounted, ref } from 'vue';

defineProps({
  reels: {
    type: Array,
    default: () => []
  }
});

// Only ever one of each, so hovering a second card cannot leave two players
// running at once.
const playingId = ref(null);
const previewId = ref(null);
// reel id => the cover that actually loaded, once maxresdefault has 404'd.
const covers = ref({});

// Hovering costs a player download, so a pointer merely crossing the grid must
// not trigger three of them. Nothing loads until the pointer settles.
const HOVER_DELAY = 350;
let hoverTimer = null;

function canPreview() {
  return window.matchMedia?.('(hover: hover)').matches
    && !window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
}

function startPreview(reel) {
  if (!reel.previewUrl || playingId.value === reel.id || !canPreview()) return;

  clearTimeout(hoverTimer);
  hoverTimer = setTimeout(() => {
    previewId.value = reel.id;
  }, HOVER_DELAY);
}

function stopPreview(reel) {
  clearTimeout(hoverTimer);

  if (previewId.value === reel.id) {
    previewId.value = null;
  }
}

/** Click: the real player, with sound and controls. */
function play(id) {
  clearTimeout(hoverTimer);
  previewId.value = null;
  playingId.value = id;
}

function useFallbackCover(reel) {
  if (reel.coverFallback && covers.value[reel.id] !== reel.coverFallback) {
    covers.value[reel.id] = reel.coverFallback;
  }
}

onUnmounted(() => clearTimeout(hoverTimer));
</script>

<style scoped>
.reels-block {
  margin-bottom: 56px;
}

/* Reels are portrait, so full-width thirds would make each card taller than the
   viewport. Capping the track keeps every card around 250px wide / 440px tall. */
.reels-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 24px;
  max-width: 840px;
  margin: 0 auto;
}

.reel-card {
  display: flex;
  flex-direction: column;
}

/* The 9:16 box is what keeps the poster and the player exactly the same size,
   so pressing play never resizes the grid. */
.reel-frame {
  position: relative;
  aspect-ratio: 9 / 16;
  border-radius: 20px;
  overflow: hidden;
  background: var(--dark-navy, #061427);
  border: 1.5px solid #E2E8F0;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.05);
}

.reel-iframe,
.reel-poster {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

.reel-poster {
  display: block;
  padding: 0;
  background: none;
  cursor: pointer;
  overflow: hidden;
}

/* The preview plays underneath; the overlay stays only to keep receiving the
   hover and the click. */
.reel-iframe-preview {
  pointer-events: none;
}

.reel-poster.is-previewing {
  background: transparent;
}

.reel-cover {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.reel-cover-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(140deg, #061427 0%, #1E293B 55%, #E4652E 160%);
  color: rgba(255, 255, 255, 0.5);
  font-size: 46px;
}

.reel-scrim {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(6, 20, 39, 0.05) 0%, rgba(6, 20, 39, 0.1) 55%, rgba(6, 20, 39, 0.5) 100%);
  transition: opacity 0.3s ease;
}

/* While the muted preview runs, get the dimming and the button out of the way
   so the viewer watches the video rather than the overlay. */
.reel-poster.is-previewing .reel-scrim {
  opacity: 0.3;
}

.reel-poster.is-previewing .reel-play {
  opacity: 0.75;
  transform: translate(-50%, -50%) scale(0.8);
}

.reel-sound-hint {
  position: absolute;
  left: 50%;
  bottom: 14px;
  transform: translateX(-50%);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  background: rgba(6, 20, 39, 0.72);
  color: #FFFFFF;
  font-size: 10.5px;
  font-weight: 700;
  padding: 5px 11px;
  border-radius: 20px;
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

.reel-play {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 54px;
  height: 54px;
  border-radius: 50%;
  background: var(--primary-orange, #E4652E);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  /* Nudged right to optically centre the triangle inside the circle. */
  padding-left: 4px;
  box-shadow: 0 10px 26px rgba(228, 101, 46, 0.4);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
}

.reel-poster:hover .reel-cover {
  transform: scale(1.06);
}

/* `:not(.is-previewing)` matters: previewing only happens while hovering, so
   without it this would tie on specificity with the shrink above and win on
   source order, and the button would grow instead of stepping back. */
.reel-poster:not(.is-previewing):hover .reel-play {
  transform: translate(-50%, -50%) scale(1.1);
}

.reel-poster:focus-visible {
  outline: 3px solid var(--primary-orange, #E4652E);
  outline-offset: 3px;
}

.reel-meta {
  padding: 12px 2px 0;
}

.reel-title {
  font-family: var(--font-heading);
  font-size: 15px;
  font-weight: 800;
  color: var(--dark-navy, #061427);
  margin: 0 0 3px 0;
  line-height: 1.3;
}

.reel-caption {
  font-size: 12px;
  color: #64748B;
  line-height: 1.5;
  margin: 0;
}

.reel-link {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  margin: 10px 2px 0;
  font-size: 12px;
  font-weight: 700;
  color: var(--primary-orange, #E4652E);
  text-decoration: none;
  transition: gap 0.2s ease;
  align-self: flex-start;
}

.reel-link:hover {
  gap: 11px;
}

@media (max-width: 768px) {
  .reels-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    max-width: 420px;
  }
}

/* Still two across on the narrowest phones — a reel is portrait, so one per
   row filled the whole screen with a single card. */
@media (max-width: 420px) {
  .reels-grid {
    gap: 12px;
    max-width: none;
  }
}
</style>
