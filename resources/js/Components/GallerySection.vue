<template>
  <div v-if="gallery.length || videos.length" class="gallery-section reveal stagger">
    <!-- PHOTOS -->
    <template v-if="gallery.length">
      <div class="gallery-header">
        <h2 class="gallery-title">Photo Gallery</h2>
        <p class="gallery-sub">Glimpses of our supermarket and operations</p>
      </div>

      <div class="gallery-grid">
        <button
          v-for="(item, i) in gallery"
          :key="item.id"
          type="button"
          class="gallery-item"
          :aria-label="`Open ${item.title || 'photo'} full size`"
          @click="lightboxIdx = i"
        >
          <img :src="item.image" :alt="item.title" class="gallery-img" loading="lazy" decoding="async">
          <span class="gallery-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
          <div class="gallery-overlay">
            <h4 v-if="item.title" class="gallery-item-title">{{ item.title }}</h4>
            <p v-if="item.caption" class="gallery-item-caption">{{ item.caption }}</p>
          </div>
        </button>
      </div>
    </template>

    <!-- VIDEOS — their own heading, and their own grid, so a visitor is never
         made to guess which tiles will play and which will zoom. -->
    <template v-if="videos.length">
      <div class="gallery-header" :class="{ 'is-second': gallery.length }">
        <h2 class="gallery-title">Video Gallery</h2>
        <p class="gallery-sub">Walkthroughs and highlights from our stores</p>
      </div>

      <div class="gallery-grid">
        <div v-for="video in videos" :key="video.id" class="gallery-item is-video">
          <!-- The iframe is only created once the poster is clicked: nothing
               loads from YouTube, and nothing is set in the browser, until the
               visitor actually asks for the video. -->
          <iframe
            v-if="playingId === video.id"
            :src="video.embedUrl"
            class="gallery-video-frame"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
          ></iframe>

          <button
            v-else
            type="button"
            class="gallery-video-poster"
            :aria-label="`Play ${video.title || 'video'}`"
            @click="playingId = video.id"
          >
            <img v-if="video.image" :src="video.image" :alt="video.title" class="gallery-img" loading="lazy" decoding="async">
            <span class="gallery-play-badge"><i class="fa-solid fa-play"></i></span>
            <div class="gallery-overlay">
              <h4 v-if="video.title" class="gallery-item-title">{{ video.title }}</h4>
              <p v-if="video.caption" class="gallery-item-caption">{{ video.caption }}</p>
            </div>
          </button>
        </div>
      </div>
    </template>

    <!-- Tapping any photo opens it full size -->
    <PhotoLightbox
      :photos="gallery"
      :index="lightboxIdx"
      @update:index="lightboxIdx = $event"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import PhotoLightbox from '@/Components/PhotoLightbox.vue';

defineProps({
  gallery: {
    type: Array,
    default: () => []
  },
  // YouTube items from the same Gallery screen in the admin.
  videos: {
    type: Array,
    default: () => []
  }
});

// Index of the photo open in the viewer, null while closed.
const lightboxIdx = ref(null);

// The one video swapped from its still to a player; only one plays at a time.
const playingId = ref(null);
</script>

<style scoped>
.gallery-section {
  margin-top: 4rem;
  padding-top: 3rem;
  border-top: 1px solid #E2E8F0;
}

.gallery-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.gallery-title {
  font-family: var(--font-heading);
  font-size: 2.25rem;
  font-weight: 800;
  color: #061427;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.01em;
}

.gallery-sub {
  font-size: 1.125rem;
  color: #64748B;
  margin: 0;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

/* A button so the photo is keyboard-reachable — reset the chrome it brings */
.gallery-item {
  position: relative;
  display: block;
  width: 100%;
  padding: 0;
  border: none;
  font: inherit;
  text-align: left;
  border-radius: 16px;
  overflow: hidden;
  aspect-ratio: 4/3;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
  cursor: zoom-in;
  background: #F1F5F9;
}

.gallery-item:focus-visible {
  outline: 3px solid var(--primary-orange);
  outline-offset: 2px;
}

.gallery-header.is-second {
  margin-top: 3rem;
}

/* The video tile is a plain box holding either the still or the player, so it
   keeps the photo tiles' shape either way. */
.gallery-item.is-video {
  cursor: default;
  background: #061427;
}

.gallery-video-frame,
.gallery-video-poster {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: none;
  padding: 0;
  background: none;
  cursor: pointer;
}

.gallery-video-poster:focus-visible {
  outline: 3px solid var(--primary-orange);
  outline-offset: -3px;
}

.gallery-play-badge {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 2;
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background: rgba(228, 101, 46, 0.94);
  color: #FFFFFF;
  font-size: 18px;
  padding-left: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(6, 20, 39, 0.35);
  transition: transform 0.2s var(--ease-interact);
}

.gallery-video-poster:hover .gallery-play-badge {
  transform: translate(-50%, -50%) scale(1.08);
}

.gallery-zoom-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(6, 20, 39, 0.6);
  -webkit-backdrop-filter: blur(6px);
  backdrop-filter: blur(6px);
  color: #FFFFFF;
  font-size: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: scale(0.9);
  transition: opacity 0.25s var(--ease-interact), transform 0.25s var(--ease-interact);
}

.gallery-item:hover .gallery-zoom-badge,
.gallery-item:focus-visible .gallery-zoom-badge {
  opacity: 1;
  transform: scale(1);
}

.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.gallery-item:hover .gallery-img {
  transform: scale(1.08);
}

.gallery-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(6, 20, 39, 0.85) 0%, rgba(6, 20, 39, 0) 50%);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
  opacity: 1;
}

.gallery-item-title {
  color: #FFFFFF;
  font-family: var(--font-heading);
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
  transform: translateY(10px);
  transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-item-title {
  transform: translateY(0);
}

.gallery-item-caption {
  color: #CBD5E1;
  font-size: 0.875rem;
  margin: 0;
  transform: translateY(10px);
  transition: transform 0.3s ease 0.05s;
}

.gallery-item:hover .gallery-item-caption {
  transform: translateY(0);
}

@media (max-width: 768px) {
  .gallery-section {
    margin-top: 3rem;
    padding-top: 2rem;
  }
  
  .gallery-title {
    font-size: 1.75rem;
  }
  
  /* Two across, the way every other grid on a phone reads. `auto-fill` with a
     220px floor could only ever fit one column on a 390px screen, which turned
     the gallery into a column of full-width photos. */
  .gallery-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  
  .gallery-overlay {
    opacity: 1;
    padding: 10px;
    background: linear-gradient(to top, rgba(6, 20, 39, 0.92) 0%, rgba(6, 20, 39, 0) 65%);
  }

  /* Two tiles across is half a phone screen each, and the desktop type sizes
     filled the whole photo with the caption. Small enough to read as a label
     on the picture rather than over it. */
  .gallery-item-title {
    font-size: 12.5px;
    line-height: 1.25;
  }

  .gallery-item-caption {
    font-size: 10.5px;
    line-height: 1.3;
    margin-top: 2px;
  }
  
  .gallery-item-title, 
  .gallery-item-caption {
    transform: translateY(0);
  }
}
</style>
