<template>
  <section
    v-if="categories.length || products.length || gallery.length"
    class="section-padding featured-showcase"
    id="featured"
    style="position:relative; overflow:hidden;"
  >
    <div class="watermark-backdrop" data-parallax="26">FEATURED</div>

    <div class="container" style="position:relative; z-index:2;">
      <div class="featured-layout">
        <!-- LEFT: the two featured rails -->
        <div class="featured-main">
          <!-- Featured Categories -->
          <div v-if="categories.length" class="featured-block">
            <div class="section-header featured-head">
              <div>
                <span class="section-badge"><i class="fa-solid fa-star"></i> Handpicked</span>
                <h2 class="section-title featured-title">Featured Categories</h2>
              </div>
              <Link href="/categories" class="view-all-link">
                All Categories <i class="fa-solid fa-arrow-right"></i>
              </Link>
            </div>

            <div class="featured-cat-grid stagger-fast">
              <Link
                v-for="cat in categories"
                :key="cat.id"
                :href="cat.href"
                class="featured-cat-card reveal"
              >
                <span class="featured-cat-art">
                  <img v-if="cat.image" :src="cat.image" :alt="cat.name" loading="lazy" decoding="async">
                  <i v-else class="fa-solid" :class="cat.icon || 'fa-box'" :style="{ color: cat.iconColor || null }"></i>
                </span>
                <span class="featured-cat-name">{{ cat.name }}</span>
                <span class="featured-cat-go"><i class="fa-solid fa-arrow-right"></i></span>
              </Link>
            </div>
          </div>

          <!-- Featured Products -->
          <div v-if="products.length" class="featured-block">
            <div class="section-header featured-head">
              <div>
                <span class="section-badge"><i class="fa-solid fa-bolt"></i> Best Sellers</span>
                <h2 class="section-title featured-title">Featured Products</h2>
              </div>
              <Link href="/products" class="view-all-link">
                Full Catalogue <i class="fa-solid fa-arrow-right"></i>
              </Link>
            </div>

            <div class="featured-prod-grid stagger-fast">
              <div
                v-for="item in products"
                :key="item.id"
                class="featured-prod-card reveal"
              >
                <div class="featured-prod-img" @click="$emit('openInquiry', item)">
                  <span v-if="item.badge" class="featured-prod-badge" :style="{ backgroundColor: item.badgeBg || null }">
                    {{ item.badge }}
                  </span>
                  <div class="img-wrapper">
                    <img :src="item.image" :alt="item.name" loading="lazy" decoding="async">
                  </div>

                  <!-- Desktop hover overlay button -->
                  <button @click.stop="$emit('openInquiry', item)" class="btn-product-ask btn-product-ask-overlay" type="button" :aria-label="needsPrice(item) ? 'Ask for the price of this product' : 'Ask Now for this product'">
                    <i class="fa-solid" :class="needsPrice(item) ? 'fa-tag' : 'fa-comments'"></i>
                    {{ needsPrice(item) ? 'Ask for Price' : 'Ask Now' }}
                  </button>
                </div>
                <div class="featured-prod-body">
                  <span class="featured-prod-brand">{{ item.brand }}</span>
                  <Link :href="`/products?search=${encodeURIComponent(item.name)}`" class="featured-prod-name">
                    {{ item.name }}
                  </Link>
                  <div class="featured-prod-foot" :class="{ 'is-request-row': needsPrice(item) }">
                    <!-- No price on the card is not a blank line: it is the
                         invitation to ask, same as on the catalogue page. -->
                    <span
                      class="featured-prod-price"
                      :class="{ 'is-on-request': needsPrice(item) }"
                    >{{ needsPrice(item) ? 'Price on Request' : item.price }}</span>
                    <span class="featured-prod-pack">{{ item.packSize }}</span>
                  </div>
                  <!-- Mobile action button below price -->
                  <button @click.stop="$emit('openInquiry', item)" class="btn-product-ask-mobile" type="button" :aria-label="needsPrice(item) ? 'Ask for the price of this product' : 'Ask Now for this product'">
                    <i class="fa-solid" :class="needsPrice(item) ? 'fa-tag' : 'fa-comments'"></i>
                    {{ needsPrice(item) ? 'Ask for Price' : 'Ask Now' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT: gallery carousel -->
        <aside v-if="gallery.length" class="gallery-rail reveal-right">
          <div
            class="gallery-frame"
            role="button"
            tabindex="0"
            :aria-label="`Open ${activeShot.title || 'gallery photo'} full size`"
            @click="openLightbox(galleryIdx)"
            @keydown.enter.prevent="openLightbox(galleryIdx)"
            @keydown.space.prevent="openLightbox(galleryIdx)"
          >
            <img
              v-for="(shot, i) in gallery"
              :key="shot.id"
              :src="shot.image"
              :alt="shot.title || 'Store gallery'"
              class="gallery-slide"
              :class="{ active: i === galleryIdx }"
              loading="lazy"
              decoding="async"
            >
            <div class="gallery-scrim"></div>

            <div class="gallery-caption">
              <span class="gallery-eyebrow"><i class="fa-solid fa-camera"></i> Gallery</span>
              <h3 v-if="activeShot.title">{{ activeShot.title }}</h3>
              <p v-if="activeShot.caption">{{ activeShot.caption }}</p>
            </div>

            <span class="gallery-zoom-hint"><i class="fa-solid fa-magnifying-glass-plus"></i> Click to zoom</span>

            <button v-if="gallery.length > 1" class="gallery-arrow prev" @click.stop="step(-1)" aria-label="Previous photo">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button v-if="gallery.length > 1" class="gallery-arrow next" @click.stop="step(1)" aria-label="Next photo">
              <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div v-if="gallery.length > 1" class="gallery-dots">
              <button
                v-for="(shot, i) in gallery"
                :key="`dot-${shot.id}`"
                class="gallery-dot"
                :class="{ active: i === galleryIdx }"
                @click.stop="goTo(i)"
                :aria-label="`Show photo ${i + 1}`"
              ></button>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <!-- Full-size viewer, shared with the other galleries -->
    <PhotoLightbox
      :photos="gallery"
      :index="lightboxIdx"
      @update:index="lightboxIdx = $event"
      @close="restart"
    />
  </section>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import PhotoLightbox from '@/Components/PhotoLightbox.vue';

const props = defineProps({
  categories: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  gallery: { type: Array, default: () => [] },
});

defineEmits(['openInquiry']);

/**
 * A card with nothing to print in the price slot.
 *
 * Either the admin marked it price-on-request, or the row simply carries no
 * price — a bulk item, or one whose sizes are quoted. Both read the same to a
 * visitor, and both are an ask rather than an empty line. Same rule as
 * Pages/Products.vue.
 */
function needsPrice(product) {
  return Boolean(product?.priceOnRequest) || !String(product?.price ?? '').trim();
}

const galleryIdx = ref(0);
const activeShot = computed(() => props.gallery[galleryIdx.value] || {});

let timer = null;

function restart() {
  if (timer) clearInterval(timer);
  if (props.gallery.length > 1) timer = setInterval(() => step(1), 6000);
}

function step(direction) {
  const total = props.gallery.length;
  if (!total) return;
  galleryIdx.value = (galleryIdx.value + direction + total) % total;
}

function goTo(index) {
  galleryIdx.value = index;
  restart();
}

/* ------------------------------------------------------------- lightbox */

// null while closed. Auto-rotation pauses while the viewer is up and resumes
// from whichever photo was last looked at.
const lightboxIdx = ref(null);

function openLightbox(index) {
  lightboxIdx.value = index;
  if (timer) clearInterval(timer);
}

watch(lightboxIdx, value => {
  if (value !== null) galleryIdx.value = value;
});

onMounted(restart);
onUnmounted(() => { if (timer) clearInterval(timer); });
</script>

<style scoped>
/* Two rails on the left, the photo carousel standing beside them */
.featured-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 380px;
  gap: 32px;
  align-items: start;
}

.featured-main {
  display: flex;
  flex-direction: column;
  gap: 44px;
  min-width: 0;
}

.featured-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 22px;
}

.featured-title {
  font-size: 26px;
  margin: 8px 0 0 0;
  text-align: left;
}

/* ---- Featured categories ---- */
.featured-cat-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.featured-cat-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  text-decoration: none;
  transition: transform 0.3s var(--ease-interact), box-shadow 0.3s var(--ease-interact), border-color 0.3s var(--ease-interact);
}

.featured-cat-card:hover {
  transform: translateY(var(--lift-tile));
  border-color: var(--primary-orange);
  box-shadow: var(--shadow-hover-orange);
}

.featured-cat-art {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--primary-orange-light);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  color: var(--primary-orange);
  font-size: 18px;
}

.featured-cat-art img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 5px;
}

.featured-cat-name {
  font-family: var(--font-heading);
  font-size: 13.5px;
  font-weight: 800;
  color: var(--dark-navy);
  line-height: 1.25;
  min-width: 0;
}

.featured-cat-go {
  margin-left: auto;
  color: var(--primary-orange);
  font-size: 11px;
  opacity: 0;
  transform: translateX(-4px);
  transition: opacity 0.25s var(--ease-interact), transform 0.25s var(--ease-interact);
}

.featured-cat-card:hover .featured-cat-go {
  opacity: 1;
  transform: translateX(0);
}

/* ---- Featured products ---- */
.featured-prod-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.featured-prod-card {
  position: relative;
  display: flex;
  flex-direction: column;
  background: #FFFFFF;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: transform 0.3s var(--ease-interact), box-shadow 0.3s var(--ease-interact), border-color 0.3s var(--ease-interact);
}

.featured-prod-card:hover {
  transform: translateY(var(--lift-card));
  border-color: var(--primary-orange);
  box-shadow: var(--shadow-hover);
}

.featured-prod-img {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 140px;
  background: radial-gradient(circle at center, rgba(228, 101, 46, 0.05) 0%, rgba(253, 251, 249, 0.95) 100%);
  border-bottom: 1px solid var(--border-light);
  padding: 12px;
  overflow: hidden;
}

.featured-prod-img .img-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.featured-prod-img img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  filter: drop-shadow(0 6px 14px rgba(6, 20, 39, 0.12));
  transition: transform 0.35s var(--ease-interact);
}

.featured-prod-card:hover .featured-prod-img img {
  transform: scale(1.08);
}

.btn-product-ask-overlay {
  position: absolute;
  left: 10px;
  right: 10px;
  bottom: 10px;
  z-index: 3;
  background-color: var(--primary-orange);
  color: #FFFFFF;
  border: none;
  padding: 8px 12px;
  border-radius: 10px;
  font-family: var(--font-main);
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  box-shadow: 0 4px 14px rgba(228, 101, 46, 0.35);
  opacity: 0;
  transform: translateY(8px);
  pointer-events: none;
  transition: opacity 0.25s var(--ease-interact), transform 0.25s var(--ease-interact), background-color 0.2s ease;
}

.featured-prod-card:hover .btn-product-ask-overlay,
.featured-prod-card:focus-within .btn-product-ask-overlay {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.btn-product-ask-overlay:hover {
  background-color: #C9541F;
}

.btn-product-ask-mobile {
  display: none;
  width: 100%;
  height: 34px;
  margin-top: 8px;
  background: linear-gradient(135deg, #E4652E 0%, #EA580C 100%);
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-family: var(--font-main);
  font-size: 12px;
  font-weight: 700;
  align-items: center;
  justify-content: center;
  gap: 6px;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(228, 101, 46, 0.22);
  transition: all 0.2s ease;
}

.btn-product-ask-mobile:active {
  transform: scale(0.98);
}

.featured-prod-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: var(--primary-orange);
  color: #FFFFFF;
  font-size: 9px;
  font-weight: 800;
  letter-spacing: 0.4px;
  text-transform: uppercase;
  padding: 3px 9px;
  border-radius: 10px;
  z-index: 2;
}

.featured-prod-body {
  display: block;
  padding: 14px;
}

.featured-prod-brand {
  display: block;
  font-size: 10.5px;
  font-weight: 700;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.4px;
  margin-bottom: 4px;
}

.featured-prod-name {
  display: block;
  font-family: var(--font-heading);
  font-size: 14px;
  font-weight: 800;
  color: var(--dark-navy);
  line-height: 1.3;
  margin-bottom: 12px;
  min-height: 36px;
  text-decoration: none;
  transition: color 0.2s ease;
}

.featured-prod-name:hover {
  color: var(--primary-orange);
}

.featured-prod-foot {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 8px;
  padding-top: 10px;
  border-top: 1px dashed var(--border-color);
}

.featured-prod-price {
  font-family: var(--font-heading);
  font-size: 16px;
  font-weight: 800;
  color: var(--primary-orange);
}

/* "Price on Request" is far longer than "₹430.00", and on a phone the card is
   half a screen wide. It steps down a size and is never broken mid-phrase; if
   the pack label cannot sit beside it, the pack drops to its own line. */
.featured-prod-price.is-on-request {
  font-size: 12.5px;
  letter-spacing: -0.2px;
  white-space: nowrap;
}

.featured-prod-foot.is-request-row {
  flex-wrap: wrap;
  row-gap: 2px;
}

.featured-prod-pack {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 600;
}

/* ---- Gallery carousel ---- */
.gallery-rail {
  position: sticky;
  top: 92px;
}

.gallery-frame {
  cursor: zoom-in;
  position: relative;
  height: 560px;
  border-radius: var(--radius-banner);
  overflow: hidden;
  background: var(--dark-navy);
  box-shadow: var(--shadow-lg);
}

.gallery-slide {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transform: scale(1.04);
  transition: opacity 0.9s var(--ease-entrance), transform 6s linear;
}

.gallery-slide.active {
  opacity: 1;
  transform: scale(1);
}

.gallery-scrim {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(6, 20, 39, 0.15) 0%, rgba(6, 20, 39, 0) 40%, rgba(6, 20, 39, 0.88) 100%);
  pointer-events: none;
}

.gallery-caption {
  position: absolute;
  left: 24px;
  right: 24px;
  bottom: 56px;
  color: #FFFFFF;
  z-index: 2;
}

.gallery-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #FFFFFF;
  background: rgba(228, 101, 46, 0.92);
  padding: 5px 12px;
  border-radius: 999px;
  margin-bottom: 10px;
}

.gallery-caption h3 {
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: 800;
  margin: 0 0 4px 0;
  line-height: 1.25;
}

.gallery-caption p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.82);
  margin: 0;
  line-height: 1.5;
}

.gallery-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 3;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.35);
  background: rgba(255, 255, 255, 0.14);
  -webkit-backdrop-filter: blur(8px);
  backdrop-filter: blur(8px);
  color: #FFFFFF;
  font-size: 12px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.25s var(--ease-interact);
}

/* Nudges the reader that the photo opens full size */
.gallery-zoom-hint {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 3;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 10.5px;
  font-weight: 800;
  color: #FFFFFF;
  background: rgba(6, 20, 39, 0.6);
  -webkit-backdrop-filter: blur(6px);
  backdrop-filter: blur(6px);
  padding: 5px 11px;
  border-radius: 999px;
  opacity: 0;
  transform: translateY(-4px);
  transition: opacity 0.25s var(--ease-interact), transform 0.25s var(--ease-interact);
  pointer-events: none;
}

.gallery-frame:hover .gallery-zoom-hint,
.gallery-frame:focus-visible .gallery-zoom-hint {
  opacity: 1;
  transform: translateY(0);
}

.gallery-arrow.prev { left: 14px; }
.gallery-arrow.next { right: 14px; }

.gallery-arrow:hover {
  background-color: var(--primary-orange);
  border-color: var(--primary-orange);
}

.gallery-dots {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 20px;
  display: flex;
  justify-content: center;
  gap: 7px;
  z-index: 3;
}

.gallery-dot {
  width: 20px;
  height: 4px;
  border-radius: 2px;
  border: none;
  padding: 0;
  background: rgba(255, 255, 255, 0.4);
  cursor: pointer;
  transition: width 0.35s var(--ease-entrance), background-color 0.35s var(--ease-entrance);
}

.gallery-dot.active {
  width: 30px;
  background: var(--primary-orange);
}

/* ---- Responsive ---- */
@media (max-width: 1200px) {
  .featured-layout {
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 24px;
  }

  .featured-cat-grid,
  .featured-prod-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .gallery-frame {
    height: 480px;
  }
}

@media (max-width: 1024px) {
  .featured-layout {
    grid-template-columns: 1fr;
  }

  .gallery-rail {
    position: static;
  }

  .gallery-frame {
    height: 360px;
  }
}

@media (max-width: 768px) {
  .featured-main {
    gap: 32px;
  }

  .featured-head {
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 16px;
  }

  .featured-title {
    font-size: 20px;
  }

  .featured-cat-grid,
  .featured-prod-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .featured-cat-card {
    padding: 11px 12px;
    gap: 10px;
    border-radius: 14px;
  }

  .featured-cat-art {
    width: 36px;
    height: 36px;
    font-size: 15px;
    border-radius: 10px;
  }

  .featured-cat-name {
    font-size: 12px;
  }

  .featured-prod-img {
    height: 124px;
    padding: 10px 8px;
    cursor: pointer;
  }

  .btn-product-ask-overlay {
    display: none !important;
  }

  .btn-product-ask-mobile {
    display: flex;
  }

  .featured-prod-badge {
    top: 7px;
    left: 7px;
    font-size: 8px;
    font-weight: 800;
    padding: 2.5px 7px;
    border-radius: 6px;
    letter-spacing: 0.3px;
    max-width: calc(100% - 14px);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .featured-prod-body {
    padding: 10px 10px 12px;
  }

  .featured-prod-brand {
    font-size: 9.5px;
    margin-bottom: 2px;
  }

  .featured-prod-name {
    font-size: 12.5px;
    line-height: 1.25;
    min-height: 32px;
    max-height: 32px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 8px;
  }

  .featured-prod-foot {
    padding-top: 8px;
  }

  .featured-prod-price {
    font-size: 14.5px;
  }

  .featured-prod-pack {
    font-size: 10.5px;
  }

  .gallery-zoom-hint {
    opacity: 1;
    transform: none;
    font-size: 9.5px;
    padding: 4px 9px;
  }

  .gallery-frame {
    height: 280px;
    border-radius: 18px;
  }

  .gallery-caption {
    left: 16px;
    right: 16px;
    bottom: 46px;
  }

  .gallery-caption h3 {
    font-size: 16px;
  }

  .gallery-caption p {
    font-size: 11.5px;
  }
}
</style>
