<template>
  <div class="stores-page-root">
    <TopBar />
    <Navbar activePage="stores" @openModal="isModalOpen = true" />

    <main>


      <!-- 1. OUR STORES SECTION (6 LOCATIONS GRID) -->
      <section class="section-padding" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">STORES</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-store"></i> Store Locations</span>
            <!-- The page's one h1: this is what the outlet list is about, and
                 a page without one reads to a crawler as having no subject.
                 `.section-title` carries the whole type scale, so the tag swap
                 changes nothing on screen. -->
            <h1 class="section-title">Our Supermarkets & Supply Outlets</h1>
            <p class="section-subtitle">Visit any of our convenient store locations for retail groceries, daily staples, or B2B commercial inquiries.</p>
          </div>

          <div class="stores-grid">
            <div v-for="store in stores" :key="store.id" class="store-card reveal">
              <!-- Store Photo — tapping it opens the outlet on the map -->
              <button
                type="button"
                class="store-photo-wrapper"
                @click="openMap(store)"
                :aria-label="`Show ${store.name} on the map`"
              >
                <img
                  v-for="(shot, i) in photosOf(store)"
                  :key="shot"
                  :src="shot"
                  :alt="store.name"
                  class="store-photo"
                  :class="{ active: i === activePhoto(store) }"
                  loading="lazy"
                  decoding="async"
                >
                <span class="store-type-badge">{{ store.type }}</span>
                <span v-if="photosOf(store).length > 1" class="store-photo-count">
                  <i class="fa-solid fa-images"></i> {{ activePhoto(store) + 1 }}/{{ photosOf(store).length }}
                </span>
                <span class="store-map-hint"><i class="fa-solid fa-location-dot"></i> View location</span>
              </button>

              <!-- Store Content -->
              <div class="store-card-body">
                <h3 class="store-name">{{ store.name }}</h3>
                
                <!-- Full Address -->
                <div class="store-info-row">
                  <i class="fa-solid fa-location-dot store-icon"></i>
                  <div>
                    <strong style="display:block; font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.5px;">Address</strong>
                    <p class="store-address">{{ store.address }}</p>
                  </div>
                </div>

                <!-- Mobile Numbers -->
                <div class="store-info-row">
                  <i class="fa-solid fa-phone store-icon"></i>
                  <div>
                    <strong style="display:block; font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.5px;">Mobile Number</strong>
                    <div class="store-phones">
                      <a 
                        v-for="(pLink, idx) in store.phoneLinks" 
                        :key="idx" 
                        :href="'tel:+91' + pLink" 
                        class="phone-link"
                      >
                        +91 {{ pLink }}
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Email -->
                <div v-if="store.email" class="store-info-row">
                  <i class="fa-solid fa-envelope store-icon"></i>
                  <div>
                    <strong style="display:block; font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.5px;">Email</strong>
                    <a :href="`mailto:${store.email}`" class="store-email">{{ store.email }}</a>
                  </div>
                </div>

                <!-- GST number -->
                <div v-if="store.gstNumber" class="store-info-row">
                  <i class="fa-solid fa-file-invoice store-icon"></i>
                  <div>
                    <strong style="display:block; font-size:11px; color:#94A3B8; text-transform:uppercase; letter-spacing:0.5px;">GST Number</strong>
                    <span class="store-gst">{{ store.gstNumber }}</span>
                  </div>
                </div>

                <!-- Action Buttons: Google Maps Location & Directions -->
                <div class="store-card-actions">
                  <button
                    type="button"
                    @click="openMap(store)"
                    class="btn-outline"
                    style="padding:8px 14px; font-size:12px; border-radius:8px; cursor:pointer;"
                  >
                    <i class="fa-solid fa-map-location-dot"></i> View on Map
                  </button>

                  <a
                    :href="store.directionsUrl"
                    target="_blank"
                    rel="noopener"
                    class="btn-primary"
                    style="padding:8px 16px; font-size:12px; border-radius:8px;"
                  >
                    <i class="fa-solid fa-diamond-turn-right"></i> Get Directions
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. OUR OTHER VENTURES SECTION -->
      <section class="section-padding bg-light-surface" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">VENTURES</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-city"></i> Sister Enterprises</span>
            <h2 class="section-title">Our Other Ventures</h2>
            <p class="section-subtitle">Diversified corporate divisions spanning apparel manufacturing, event production, catering, and wellness lifestyle.</p>
          </div>

          <div class="ventures-grid">
            <div v-for="(v, idx) in ventures" :key="idx" class="venture-card reveal">
              <div class="venture-tag-row">
                <span class="venture-tag">{{ v.tag }}</span>
                <div class="venture-icon-box"><i class="fa-solid" :class="v.icon"></i></div>
              </div>

              <h3 class="venture-name">{{ v.name }}</h3>
              <div class="venture-division">{{ v.division }}</div>
              <p class="venture-desc">{{ v.desc }}</p>

              <button @click="isModalOpen = true" class="btn-venture-contact">
                Contact Division <i class="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Callout Banner -->
      <QuickCalloutBanner />
    </main>

    <!-- STORE LOCATION MAP MODAL -->
    <div class="modal-overlay" :class="{ active: !!activeStore }" @click.self="closeMap" data-lenis-prevent>
      <div v-if="activeStore" class="map-modal-card" data-lenis-prevent>
        <button class="modal-close" @click="closeMap" aria-label="Close map"><i class="fa-solid fa-xmark"></i></button>

        <div class="map-modal-head">
          <span class="section-badge"><i class="fa-solid fa-location-dot"></i> {{ activeStore.type || 'Store Location' }}</span>
          <h3 class="map-modal-title">{{ activeStore.name }}</h3>
          <p class="map-modal-address">{{ activeStore.address }}</p>
        </div>

        <!-- Keyless Google Maps embed, centred on the outlet -->
        <div class="map-frame">
          <iframe
            :src="activeStore.mapEmbedUrl"
            :title="`Map showing ${activeStore.name}`"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen
          ></iframe>
        </div>

        <div class="map-modal-actions">
          <a :href="activeStore.mapUrl" target="_blank" rel="noopener" class="btn-outline map-modal-btn">
            <i class="fa-solid fa-map-location-dot"></i> Open in Google Maps
          </a>
          <a :href="activeStore.directionsUrl" target="_blank" rel="noopener" class="btn-primary map-modal-btn">
            <i class="fa-solid fa-diamond-turn-right"></i> Get Directions
          </a>
        </div>
      </div>
    </div>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <WhatsAppFloat />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import QuickCalloutBanner from '@/Components/QuickCalloutBanner.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import { useParallax } from '@/composables/useParallax';

const props = defineProps({
  stores: Array,
  ventures: Array,
  meta: Object
});

const isModalOpen = ref(false);

// Drifts the big ghost section words and the hero backdrop.
useParallax();

// The outlet whose map is open. The iframe is only created once a store is
// picked, so no map tiles are fetched until someone asks for them.
const activeStore = ref(null);

// ---- Rotating store photos -------------------------------------------------
// One shared 10-second tick drives every card, keyed by store id, so six cards
// cost one interval rather than six.
const photoIndex = ref({});
let photoTimer = null;

function photosOf(store) {
  const shots = (store.gallery && store.gallery.length) ? store.gallery : [store.image];
  return shots.filter(Boolean);
}

function activePhoto(store) {
  return photoIndex.value[store.id] || 0;
}

function advancePhotos() {
  const next = { ...photoIndex.value };

  for (const store of props.stores || []) {
    const total = photosOf(store).length;
    if (total > 1) next[store.id] = ((next[store.id] || 0) + 1) % total;
  }

  photoIndex.value = next;
}

function openMap(store) {
  activeStore.value = store;
  document.body.style.overflow = 'hidden';
}

function closeMap() {
  activeStore.value = null;
  document.body.style.overflow = '';
}

function onKeydown(e) {
  if (e.key === 'Escape' && activeStore.value) closeMap();
}

onMounted(() => {
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
    revealElements.forEach(el => observer.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('active'));
  }

  window.addEventListener('keydown', onKeydown);

  if ((props.stores || []).some(store => photosOf(store).length > 1)) {
    photoTimer = setInterval(advancePhotos, 10000);
  }
});

onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown);
  if (photoTimer) clearInterval(photoTimer);
  document.body.style.overflow = '';
});
</script>

<style scoped>
.stores-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
  max-width: 1100px;
  margin: 0 auto;
}

.store-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid #E2E8F0;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.store-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
  border-color: rgba(228, 101, 46, 0.3);
}

/* A button, so the photo is keyboard-reachable — reset the chrome it brings */
.store-photo-wrapper {
  position: relative;
  height: 250px;
  overflow: hidden;
  display: block;
  width: 100%;
  padding: 0;
  border: none;
  background: none;
  cursor: pointer;
  font: inherit;
  text-align: left;
}

.store-photo-wrapper:focus-visible {
  outline: 3px solid #E4652E;
  outline-offset: -3px;
}

.store-map-hint {
  position: absolute;
  right: 12px;
  bottom: 12px;
  background: rgba(255, 255, 255, 0.94);
  color: #E4652E;
  font-size: 11.5px;
  font-weight: 800;
  padding: 6px 12px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(6, 20, 39, 0.18);
  opacity: 0;
  transform: translateY(6px);
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.store-card:hover .store-map-hint,
.store-photo-wrapper:focus-visible .store-map-hint {
  opacity: 1;
  transform: translateY(0);
}

.store-photo {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.8s var(--ease-entrance), transform 0.4s ease;
}

.store-photo.active {
  opacity: 1;
}

.store-card:hover .store-photo.active {
  transform: scale(1.06);
}

.store-photo-count {
  position: absolute;
  top: 14px;
  right: 14px;
  background: rgba(6, 20, 39, 0.72);
  -webkit-backdrop-filter: blur(6px);
  backdrop-filter: blur(6px);
  color: #FFFFFF;
  font-size: 10.5px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.store-email {
  font-size: 13px;
  font-weight: 600;
  color: #E4652E;
  word-break: break-word;
}

.store-email:hover {
  text-decoration: underline;
}

.store-gst {
  font-size: 13px;
  font-weight: 700;
  color: #061427;
  letter-spacing: 0.4px;
  background: #F1F5F9;
  padding: 3px 10px;
  border-radius: 6px;
  display: inline-block;
  margin-top: 2px;
}

.store-type-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  background-color: #061427;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.store-card-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  flex: 1;
  justify-content: space-between;
}

.store-name {
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: 800;
  color: #061427;
  margin-bottom: 16px;
}

.store-info-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 14px;
}

.store-icon {
  color: #E4652E;
  font-size: 16px;
  margin-top: 3px;
  flex-shrink: 0;
}

.store-address {
  font-size: 13px;
  color: #334155;
  line-height: 1.5;
  margin: 2px 0 0 0;
  font-weight: 500;
}

.store-phones {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
}

.phone-link {
  font-size: 13px;
  font-weight: 700;
  color: #061427;
  background-color: #F1F5F9;
  padding: 3px 10px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.phone-link:hover {
  background-color: #E4652E;
  color: #FFFFFF;
}

.store-card-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px dashed #E2E8F0;
}

/* STORE LOCATION MAP MODAL — .modal-overlay / .modal-close come from style.css */
.map-modal-card {
  background: #FFFFFF;
  border-radius: var(--radius-banner);
  width: calc(100% - 32px);
  max-width: 720px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 24px;
  position: relative;
  box-shadow: 0 20px 60px rgba(6, 20, 39, 0.35);
  transform: translateY(12px);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-overlay.active .map-modal-card {
  transform: translateY(0);
}

/* Explicit hit area: the global .modal-close is sized only by its icon glyph,
   which leaves nothing to tap if the icon font fails to load. */
.map-modal-card .modal-close {
  top: 14px;
  right: 14px;
  width: 38px;
  height: 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 50%;
  background: #F1F5F9;
  color: #475569;
  font-size: 18px;
  cursor: pointer;
  z-index: 2;
}

.map-modal-card .modal-close:hover {
  background: #FDF3ED;
  color: #E4652E;
}

.map-modal-head {
  padding-right: 46px;
  margin-bottom: 16px;
}

.map-modal-title {
  font-family: var(--font-heading);
  font-size: 22px;
  font-weight: 800;
  color: #061427;
  margin: 10px 0 4px 0;
}

.map-modal-address {
  font-size: 13px;
  color: #475569;
  line-height: 1.5;
  margin: 0;
}

.map-frame {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 10;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #E2E8F0;
  background: #F1F5F9;
}

.map-frame iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

.map-modal-actions {
  display: flex;
  gap: 10px;
  margin-top: 16px;
}

.map-modal-btn {
  flex: 1;
  justify-content: center;
  text-align: center;
  padding: 11px 14px;
  font-size: 13px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

/* Other Ventures Grid */
.ventures-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.venture-card {
  background: #FFFFFF;
  border-radius: 20px;
  padding: 28px 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.venture-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
  border-color: rgba(228, 101, 46, 0.3);
}

.venture-tag-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.venture-tag {
  font-size: 11px;
  font-weight: 700;
  color: #E4652E;
  background-color: #FDF3ED;
  padding: 4px 12px;
  border-radius: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.venture-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background-color: #061427;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.venture-name {
  font-family: var(--font-heading);
  font-size: 19px;
  font-weight: 800;
  color: #061427;
  margin-bottom: 4px;
  line-height: 1.25;
}

.venture-division {
  font-size: 12px;
  font-weight: 700;
  color: #64748B;
  margin-bottom: 12px;
}

.venture-desc {
  font-size: 13px;
  color: #475569;
  line-height: 1.6;
  margin-bottom: 20px;
}

.btn-venture-contact {
  font-size: 12.5px;
  font-weight: 700;
  color: #E4652E;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: none;
  background: none;
  padding: 0;
  cursor: pointer;
}

.btn-venture-contact:hover {
  gap: 10px;
  color: #C9541F;
}

@media (max-width: 1024px) {
  .ventures-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ---------- PHONE: stores and ventures stay two-up, compacted ---------- */
@media (max-width: 768px) {
  .stores-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .store-card {
    border-radius: 16px;
  }

  .store-photo-wrapper {
    height: 120px;
  }

  .store-type-badge {
    top: 8px;
    left: 8px;
    font-size: 9px;
    padding: 4px 9px;
  }

  .store-photo-count {
    top: 8px;
    right: 8px;
    font-size: 9px;
    padding: 3px 7px;
  }

  .store-email,
  .store-gst {
    font-size: 11.5px;
  }

  /* No hover on touch — the hint is always on, and smaller */
  .store-map-hint {
    right: 8px;
    bottom: 8px;
    font-size: 9.5px;
    padding: 4px 9px;
    opacity: 1;
    transform: none;
  }

  .map-modal-card {
    padding: 16px;
    border-radius: 18px;
    max-height: 92vh;
  }

  .map-modal-title {
    font-size: 18px;
  }

  .map-modal-address {
    font-size: 12px;
  }

  .map-frame {
    aspect-ratio: 4 / 3;
    border-radius: 12px;
  }

  .map-modal-actions {
    flex-direction: column;
    gap: 8px;
  }

  .map-modal-btn {
    font-size: 12.5px;
    padding: 10px 12px;
  }

  .store-card-body {
    padding: 14px 12px;
  }

  .store-name {
    font-size: 15px;
    margin-bottom: 12px;
  }

  .store-info-row {
    gap: 8px;
    margin-bottom: 10px;
  }

  .store-icon {
    font-size: 13px;
  }

  .store-info-row strong {
    font-size: 9.5px !important;
  }

  .store-address {
    font-size: 11.5px;
    line-height: 1.45;
  }

  .store-phones {
    flex-direction: column;
    gap: 5px;
  }

  .phone-link {
    font-size: 11.5px;
    padding: 4px 8px;
    text-align: center;
  }

  /* Two full-width buttons stacked — side by side they would truncate */
  .store-card-actions {
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
    margin-top: 14px;
    padding-top: 12px;
  }

  .store-card-actions .btn-outline,
  .store-card-actions .btn-primary {
    width: 100%;
    justify-content: center;
    text-align: center;
    font-size: 11px !important;
    padding: 8px 6px !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .ventures-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .venture-card {
    padding: 16px 13px;
    border-radius: 16px;
  }

  .venture-tag-row {
    margin-bottom: 12px;
    gap: 8px;
  }

  .venture-tag {
    font-size: 9px;
    padding: 3px 8px;
  }

  .venture-icon-box {
    width: 34px;
    height: 34px;
    font-size: 14px;
    border-radius: 10px;
    flex-shrink: 0;
  }

  .venture-name {
    font-size: 15px;
  }

  .venture-division {
    font-size: 10.5px;
    margin-bottom: 8px;
  }

  .venture-desc {
    font-size: 11.5px;
    line-height: 1.5;
    margin-bottom: 14px;
  }

  .btn-venture-contact {
    font-size: 11px;
  }
}

@media (max-width: 380px) {
  .stores-grid,
  .ventures-grid {
    gap: 10px;
  }

  .store-photo-wrapper {
    height: 104px;
  }

  .store-name,
  .venture-name {
    font-size: 13.5px;
  }
}
</style>
