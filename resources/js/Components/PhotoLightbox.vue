<template>
  <teleport to="body">
    <div
      v-if="index !== null && photos.length"
      class="photo-lightbox"
      @click.self="close"
      data-lenis-prevent
    >
      <button class="lightbox-close" @click="close" aria-label="Close photo">
        <i class="fa-solid fa-xmark"></i>
      </button>

      <button v-if="photos.length > 1" class="lightbox-arrow" @click.stop="step(-1)" aria-label="Previous photo">
        <i class="fa-solid fa-chevron-left"></i>
      </button>

      <figure class="lightbox-figure" @click.stop>
        <img :src="shot.image" :alt="shot.title || 'Gallery photo'" class="lightbox-img">
        <figcaption v-if="shot.title || shot.caption || photos.length > 1" class="lightbox-caption">
          <strong v-if="shot.title">{{ shot.title }}</strong>
          <span v-if="shot.caption">{{ shot.caption }}</span>
          <em v-if="photos.length > 1" class="lightbox-count">{{ index + 1 }} / {{ photos.length }}</em>
        </figcaption>
      </figure>

      <button v-if="photos.length > 1" class="lightbox-arrow" @click.stop="step(1)" aria-label="Next photo">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    </div>
  </teleport>
</template>

<script setup>
import { computed, watch, onMounted, onUnmounted } from 'vue';

/**
 * Full-screen photo viewer shared by every gallery on the site.
 *
 * `index` is the photo being shown, or null while closed — the parent owns it
 * so it can decide what opening the viewer means for its own state (pausing a
 * carousel, say).
 */
const props = defineProps({
  photos: { type: Array, default: () => [] },
  index: { type: Number, default: null },
});

const emit = defineEmits(['update:index', 'close']);

const shot = computed(() => props.photos[props.index] || {});

function close() {
  emit('update:index', null);
  emit('close');
}

function step(direction) {
  const total = props.photos.length;
  if (!total || props.index === null) return;
  emit('update:index', (props.index + direction + total) % total);
}

function onKey(e) {
  if (props.index === null) return;
  if (e.key === 'Escape') close();
  else if (e.key === 'ArrowRight') step(1);
  else if (e.key === 'ArrowLeft') step(-1);
}

// The page behind must not scroll while the viewer is up.
watch(() => props.index, value => {
  document.body.style.overflow = value === null ? '' : 'hidden';
});

onMounted(() => window.addEventListener('keydown', onKey));

onUnmounted(() => {
  window.removeEventListener('keydown', onKey);
  document.body.style.overflow = '';
});
</script>

<style scoped>
.photo-lightbox {
  position: fixed;
  inset: 0;
  z-index: 3000;
  background: rgba(4, 12, 24, 0.92);
  -webkit-backdrop-filter: blur(6px);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  padding: 32px;
  animation: lightboxIn 0.25s var(--ease-entrance);
}

@keyframes lightboxIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.lightbox-figure {
  margin: 0;
  max-width: min(1100px, 100%);
  max-height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  animation: lightboxZoom 0.3s var(--ease-entrance);
}

@keyframes lightboxZoom {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}

.lightbox-img {
  max-width: 100%;
  max-height: calc(100vh - 170px);
  object-fit: contain;
  border-radius: 14px;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5);
}

.lightbox-caption {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  text-align: center;
  color: #FFFFFF;
}

.lightbox-caption strong {
  font-family: var(--font-heading);
  font-size: 17px;
  font-weight: 800;
}

.lightbox-caption span {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
}

.lightbox-count {
  font-style: normal;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--primary-orange);
  letter-spacing: 0.5px;
}

.lightbox-close,
.lightbox-arrow {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.3);
  background: rgba(255, 255, 255, 0.12);
  color: #FFFFFF;
  font-size: 15px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s var(--ease-interact), border-color 0.2s var(--ease-interact);
}

.lightbox-close:hover,
.lightbox-arrow:hover {
  background: var(--primary-orange);
  border-color: var(--primary-orange);
}

.lightbox-close {
  position: absolute;
  top: 22px;
  right: 24px;
}

@media (max-width: 768px) {
  .photo-lightbox {
    padding: 16px;
    gap: 8px;
  }

  .lightbox-close,
  .lightbox-arrow {
    width: 36px;
    height: 36px;
    font-size: 13px;
  }

  .lightbox-close {
    top: 14px;
    right: 14px;
  }

  .lightbox-img {
    max-height: calc(100vh - 200px);
  }

  .lightbox-caption strong {
    font-size: 15px;
  }

  .lightbox-caption span {
    font-size: 12px;
  }
}
</style>
