<template>
  <div class="fl-product-page">
    <TopBar />
    <Navbar activePage="products" @openModal="openInquiryModal(null)" />

    <main class="fl-pd-main">
      <div class="container">
        <!-- Breadcrumb -->
        <nav class="fl-pd-breadcrumb" aria-label="Breadcrumb">
          <Link href="/"><i class="fa-solid fa-house"></i> Home</Link>
          <span class="fl-pd-sep"><i class="fa-solid fa-chevron-right"></i></span>
          <Link :href="trail.categoryProductsHref">{{ trail.category }}</Link>
          <span class="fl-pd-sep"><i class="fa-solid fa-chevron-right"></i></span>
          <Link :href="trail.varietyHref">{{ trail.variety }}</Link>
          <span class="fl-pd-sep"><i class="fa-solid fa-chevron-right"></i></span>
          <span class="active">{{ product.name }}</span>
        </nav>

        <!-- Main 2-Column Product Detail (No right badges, No Check Delivery, No Heart) -->
        <div class="fl-pd-grid">
          <!-- LEFT: Vertical Different Images Column + Main Image + Description -->
          <div class="fl-pd-left-col">
            <div class="fl-pd-gallery">
              <div class="fl-pd-thumbs-left">
                <button
                  v-for="(thumb, i) in galleryThumbs"
                  :key="i"
                  type="button"
                  class="fl-pd-thumb-card"
                  :class="{ active: i === activeThumbIdx }"
                  :aria-label="thumb.label"
                  @click="selectThumb(i)"
                >
                  <img :src="thumb.src" :alt="thumb.label">
                </button>
              </div>

              <div class="fl-pd-main-frame">
                <button
                  type="button"
                  class="fl-pd-photo-btn"
                  :aria-label="`Open ${product.name} full size`"
                  @click="lightboxIdx = photoIndex"
                >
                  <img :src="displayedImage" :alt="product.name">
                </button>

                <template v-if="galleryThumbs.length > 1">
                  <button type="button" class="fl-pd-arrow prev" aria-label="Previous photo" @click="stepGallery(-1)">
                    <i class="fa-solid fa-chevron-left"></i>
                  </button>
                  <button type="button" class="fl-pd-arrow next" aria-label="Next photo" @click="stepGallery(1)">
                    <i class="fa-solid fa-chevron-right"></i>
                  </button>
                </template>
              </div>
            </div>

            <!-- Description right below Product Image -->
            <section class="fl-pd-info-section fl-pd-left-desc">
              <h2 class="fl-pd-section-heading">Description</h2>
              <p class="fl-pd-tab-text">
                {{ product.description || `${product.name} is carefully sourced and quality-checked to bring authentic freshness and everyday goodness straight to your kitchen.` }}
              </p>
            </section>
          </div>

          <!-- RIGHT: Product Details -->
          <div class="fl-pd-info">
            <span class="fl-pd-tag">{{ product.badge || 'Farm Fresh' }}</span>

            <h1 class="fl-pd-title">{{ product.name }}</h1>

            <p v-if="product.description" class="fl-pd-desc">{{ product.description }}</p>

            <div class="fl-pd-rating-row">
              <span class="fl-pd-stars">
                <i class="fa-solid fa-star"></i>
                <strong>{{ product.rating || '4.8' }}</strong>
                <span>({{ product.reviews || '1,248' }} reviews)</span>
              </span>
              <span class="fl-pd-verified">
                <i class="fa-solid fa-circle-check"></i>
                {{ brandLabel || 'Trusted VIZORA Quality' }}
              </span>
            </div>

            <div class="fl-pd-price-row">
              <span class="fl-pd-price">
                {{ needsPrice ? 'Price on Request' : activePrice }}
              </span>
              <span v-if="activePack" class="fl-pd-pack-pill">{{ activePack }}</span>
            </div>

            <!-- Weight / Pack Size Selector -->
            <div v-if="product.variants && product.variants.length" class="fl-pd-variants-block">
              <span class="fl-pd-label">Weight</span>
              <div class="fl-pd-variants-row">
                <button
                  v-for="(variant, i) in product.variants"
                  :key="i"
                  type="button"
                  class="fl-pd-var-card"
                  :class="{ active: i === variantIdx }"
                  @click="pickVariant(i)"
                >
                  <strong>{{ variant.size }}</strong>
                  <span>{{ variant.priceOnRequest || !variant.price ? 'Ask Price' : variant.price }}</span>
                </button>
              </div>
            </div>

            <!-- Quantity Stepper -->
            <div class="fl-pd-qty-block">
              <span class="fl-pd-label">Quantity</span>
              <div class="fl-pd-stepper">
                <button type="button" @click="qty = Math.max(1, qty - 1)" aria-label="Decrease quantity">
                  <i class="fa-solid fa-minus"></i>
                </button>
                <span>{{ qty }}</span>
                <button type="button" @click="qty++" aria-label="Increase quantity">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>

            <!-- Action Button -->
            <div class="fl-pd-actions">
              <button type="button" class="fl-pd-btn-primary" @click="handleAddToCart">
                <i class="fa-solid fa-cart-shopping"></i>
                {{ needsPrice ? 'Ask for Price' : (addedToCart ? 'Added to Cart!' : 'Add to Cart') }}
              </button>
            </div>

            <!-- Product Details (2 Columns right below Add to Cart) -->
            <div class="fl-pd-inline-details">
              <h2 class="fl-pd-section-heading">Product Details</h2>
              <dl class="fl-pd-specs-grid">
                <div v-if="activeSku"><dt>Product Code</dt><dd>{{ activeSku }}</dd></div>
                <div v-if="product.brand"><dt>Brand</dt><dd>{{ brandLabel }}</dd></div>
                <div><dt>Category</dt><dd><Link :href="trail.categoryProductsHref">{{ trail.category }}</Link></dd></div>
                <div><dt>Variety</dt><dd><Link :href="trail.varietyHref">{{ trail.variety }}</Link></dd></div>
                <div v-if="activePack"><dt>Pack Size</dt><dd>{{ activePack }}</dd></div>
                <div><dt>Supply</dt><dd>{{ trail.segment === 'b2b' ? 'Bulk & Commercial Supply' : 'Retail & Home Delivery' }}</dd></div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Reviews Section -->
        <div class="fl-pd-stacked-sections">
          <section class="fl-pd-info-section">
            <h2 class="fl-pd-section-heading">Reviews ({{ product.reviews || '1,248' }})</h2>
            <div class="fl-pd-reviews-summary">
              <div class="fl-pd-rev-score">
                <strong>{{ product.rating || '4.8' }}</strong>
                <div class="fl-pd-rev-stars">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <span>Based on {{ product.reviews || '1,248' }} verified reviews</span>
              </div>
              <p class="fl-pd-tab-text">
                Rated <strong>{{ product.rating || '4.8' }} / 5</strong> by verified buyers for consistent freshness, clean packaging, and reliable delivery.
              </p>
            </div>
          </section>
        </div>

        <!-- You May Also Like -->
        <section v-if="related.length" class="fl-pd-related-box">
          <div class="fl-pd-related-head">
            <h2>You May Also Like</h2>
            <Link :href="trail.varietyHref" class="fl-pd-view-all">
              View All <i class="fa-solid fa-arrow-right"></i>
            </Link>
          </div>

          <div class="fl-pd-related-grid">
            <Link
              v-for="item in related.slice(0, 4)"
              :key="item.id"
              :href="item.href"
              class="fl-pd-rel-card"
            >
              <div class="fl-pd-rel-img">
                <img :src="item.image" :alt="item.name" loading="lazy">
              </div>
              <div class="fl-pd-rel-body">
                <h3>{{ item.name }}</h3>
                <span class="fl-pd-rel-brand">{{ item.brand || trail.variety }}</span>
                <div class="fl-pd-rel-rating">
                  <i class="fa-solid fa-star"></i> <strong>4.6</strong> <span>(860)</span>
                </div>
                <div class="fl-pd-rel-foot">
                  <span class="fl-pd-rel-price">{{ item.priceOnRequest || !item.price ? 'Ask Price' : item.price }}</span>
                  <span class="fl-pd-rel-plus"><i class="fa-solid fa-plus"></i></span>
                </div>
              </div>
            </Link>
          </div>
        </section>
      </div>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" :prefill="inquiryPrefill" @close="isModalOpen = false" />
    <WhatsAppFloat />

    <PhotoLightbox
      :photos="lightboxPhotos"
      :index="lightboxIdx"
      @update:index="lightboxIdx = $event"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import QuickCalloutBanner from '@/Components/QuickCalloutBanner.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PhotoLightbox from '@/Components/PhotoLightbox.vue';
import { useParallax } from '@/composables/useParallax';
import { useCustomAlert } from '@/composables/useCustomAlert';
import { useCart } from '@/composables/useCart';

const props = defineProps({
  product: { type: Object, required: true },
  trail: { type: Object, required: true },
  related: { type: Array, default: () => [] },
  meta: Object,
});

useParallax();

const { showSuccess } = useCustomAlert();
const { addItem } = useCart();

const isModalOpen = ref(false);
const inquiryPrefill = ref(null);
const addedToCart = ref(false);

/* ---------------------------------------------------------------- photos */

const variantIdx = ref(0);
const photoIndex = ref(0);
const lightboxIdx = ref(null);
const qty = ref(1);
const activeTab = ref('desc');

const activeVariant = computed(() => props.product.variants?.[variantIdx.value] ?? null);

/** The chosen size's own photos where it has them, the product's otherwise. */
const photos = computed(() => {
  const own = activeVariant.value?.images;
  const list = own?.length ? own : props.product.images;

  return list?.length ? list : [props.product.image].filter(Boolean);
});

// PhotoLightbox takes the same shape the gallery gives it.
const lightboxPhotos = computed(() => photos.value.map((image, i) => ({
  id: i,
  image,
  title: props.product.name,
  caption: activeVariant.value?.size || props.product.packSize || '',
})));

const activeThumbIdx = ref(0);

const galleryThumbs = computed(() => {
  if (photos.value.length > 1) {
    return photos.value.map((src, idx) => ({
      src,
      label: `${props.product.name} view ${idx + 1}`,
    }));
  }

  const primary = photos.value[0];
  const extras = [
    ...(props.related || []).map(item => item.image),
    '/assets/grocery.PNG',
    '/assets/store-categories-products.png',
    '/assets/kitcvhen.png',
    '/assets/fruits and veggies.png',
  ].filter((src) => src && src !== primary);

  const unique = [...new Set([primary, ...extras])].slice(0, 4);

  return unique.map((src, idx) => ({
    src,
    label: idx === 0 ? props.product.name : `${props.product.name} view ${idx + 1}`,
  }));
});

const displayedImage = computed(() => galleryThumbs.value[activeThumbIdx.value]?.src || photos.value[photoIndex.value]);

function selectThumb(index) {
  activeThumbIdx.value = index;
  photoIndex.value = photos.value.length > 1 ? index : 0;
}

function stepGallery(direction) {
  const total = galleryThumbs.value.length;
  if (total < 2) return;
  const next = (activeThumbIdx.value + direction + total) % total;
  selectThumb(next);
}

function stepPhoto(direction) {
  const total = photos.value.length;

  if (total < 2) return;

  photoIndex.value = (photoIndex.value + direction + total) % total;
}

function pickVariant(index) {
  variantIdx.value = index;
  // A size with photos of its own starts from its first one.
  photoIndex.value = 0;
}

/* ----------------------------------------------------------------- price */

const needsPrice = computed(() => {
  const source = activeVariant.value ?? props.product;
  const onRequest = activeVariant.value ? activeVariant.value.priceOnRequest : props.product.priceOnRequest;

  return Boolean(onRequest) || !String(source.price ?? '').trim();
});

const activePrice = computed(() => activeVariant.value?.price ?? props.product.price);
const activePack = computed(() => activeVariant.value?.size ?? props.product.packSize);
const activeSku = computed(() => activeVariant.value?.sku ?? props.product.sku);

function parsePriceNumber(raw) {
  const num = Number(String(raw ?? '').replace(/[^0-9.]/g, ''));
  return Number.isFinite(num) && num > 0 ? num : 99;
}

function buildCartPayload() {
  const numericPrice = parsePriceNumber(activePrice.value);
  const pack = activePack.value || '1 pack';
  return {
    id: `${props.product.id || props.product.slug}-${pack}`,
    name: props.product.name,
    unit: pack,
    subtitle: `${props.product.brand || 'VIZORA FOODS'} • ${pack}`,
    price: numericPrice,
    oldPrice: Math.round(numericPrice * 1.15),
    off: props.product.badge || '15% OFF',
    qty: qty.value || 1,
    image: displayedImage.value || props.product.image,
  };
}

function handleAddToCart() {
  if (needsPrice.value) {
    openInquiryModal(props.product);
    return;
  }
  addItem(buildCartPayload());
  addedToCart.value = true;
  setTimeout(() => {
    addedToCart.value = false;
  }, 1500);
}

/** Same rule as the catalogue: "Amul/Mother Dairy" is three brands, spaced. */
const brandLabel = computed(() => String(props.product.brand ?? '')
  .split(/\s*[/|;,]\s*/)
  .map(brand => brand.trim())
  .filter(Boolean)
  .join(' / '));

/* -------------------------------------------------------------- enquiry */

const page = usePage();
const whatsapp = computed(() => page.props.whatsapp || {});

const enquiryLines = computed(() => [
  `Product: ${props.product.name}`,
  props.product.brand ? `Brand: ${brandLabel.value}` : null,
  activePack.value ? `Pack Size: ${activePack.value}` : null,
  activeSku.value ? `Code: ${activeSku.value}` : null,
  '',
  'Please share pricing and availability.',
].filter(line => line !== null).join('\n'));

const whatsappHref = computed(() =>
  `https://api.whatsapp.com/send?phone=${whatsapp.value.number}&text=${encodeURIComponent(enquiryLines.value)}`);

/* ------------------------------------------------------------------ share */

const shareOpen = ref(false);
const shareRef = ref(null);

/** The page's own address, once the browser is actually there. */
const shareUrl = ref('');

const shareText = computed(() => [
  props.product.name,
  props.product.brand ? `by ${brandLabel.value}` : null,
  '— VIZORA FOODS',
].filter(Boolean).join(' '));

const shareLinks = computed(() => {
  const url = encodeURIComponent(shareUrl.value);
  const text = encodeURIComponent(shareText.value);

  return {
    whatsapp: `https://api.whatsapp.com/send?text=${text}%20${url}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
    x: `https://twitter.com/intent/tweet?text=${text}&url=${url}`,
    email: `mailto:?subject=${text}&body=${text}%20${url}`,
  };
});

/**
 * The system share sheet where the browser has one — that is the thing people
 * expect on a phone, and it reaches apps a list of links never could. The menu
 * is the fallback, not the first choice.
 */
async function share() {
  if (navigator.share) {
    try {
      await navigator.share({ title: props.product.name, text: shareText.value, url: shareUrl.value });

      return;
    } catch (error) {
      // Dismissing the sheet throws AbortError; anything else falls through to
      // the menu rather than leaving the button doing nothing.
      if (error?.name === 'AbortError') return;
    }
  }

  shareOpen.value = !shareOpen.value;
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(shareUrl.value);
    showSuccess('The link to this product is on your clipboard.', 'Copied');
  } catch (error) {
    // Clipboard access is refused outside https and in some in-app browsers.
    showSuccess(shareUrl.value, 'Copy this link');
  }

  shareOpen.value = false;
}

function onDocumentClick(event) {
  if (shareOpen.value && shareRef.value && !shareRef.value.contains(event.target)) {
    shareOpen.value = false;
  }
}

function openInquiryModal(item) {
  inquiryPrefill.value = item
    ? {
      inquiryType: props.trail.segment === 'b2b' ? 'b2b' : 'retail',
      productName: item.name,
      message: enquiryLines.value,
    }
    : null;

  isModalOpen.value = true;
}

/* ------------------------------------------------------- scroll reveals */

let observer = null;

onMounted(() => {
  // Read here rather than in setup: the page is built in the browser, and a
  // product reached through an Inertia visit has a different address than the
  // one the component was first created on.
  shareUrl.value = window.location.href;
  document.addEventListener('click', onDocumentClick);

  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
    .forEach(el => observer.observe(el));
});

onUnmounted(() => {
  observer?.disconnect();
  document.removeEventListener('click', onDocumentClick);
});
</script>

<style scoped>
.fl-product-page {
  background-color: #FFFFFF;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-pd-main {
  padding: 20px 0 56px;
}

.fl-pd-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  color: #6B7280;
  margin-bottom: 22px;
  flex-wrap: wrap;
}

.fl-pd-breadcrumb a {
  color: #6B7280;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.fl-pd-breadcrumb a:hover,
.fl-pd-breadcrumb .active {
  color: #14532D;
  font-weight: 600;
}

.fl-pd-sep {
  font-size: 10px;
  color: #9CA3AF;
}

/* 2-Column Main Detail */
.fl-pd-grid {
  display: grid;
  grid-template-columns: 1.08fr 0.92fr;
  gap: 36px;
  align-items: start;
  margin-bottom: 36px;
}

.fl-pd-left-col {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.fl-pd-left-desc {
  padding-top: 18px;
  border-top: 1px solid #E5ECE1;
}

.fl-pd-gallery {
  display: flex;
  flex-direction: row;
  gap: 14px;
  align-items: stretch;
}

.fl-pd-thumbs-left {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 92px;
  flex-shrink: 0;
}

.fl-pd-thumb-card {
  flex: 1;
  min-height: 90px;
  border-radius: 12px;
  border: 1.5px solid #E5ECE1;
  background: #F4F5EF;
  padding: 6px;
  cursor: pointer;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.2s ease, transform 0.2s ease;
}

.fl-pd-thumb-card:hover {
  border-color: #3F9C1E;
}

.fl-pd-thumb-card.active {
  border: 2px solid #14532D;
  background: #EAF5E2;
}

.fl-pd-thumb-card img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.fl-pd-photo-btn img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.is-angle-zoom {
  transform: scale(1.22);
}

.is-angle-left {
  transform: scale(1.1) translateX(-4%);
}

.is-angle-right {
  transform: scale(1.1) translateX(4%);
}

.fl-pd-main-frame {
  position: relative;
  flex: 1;
  background: #F4F5EF;
  border-radius: 18px;
  height: 515px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px;
  overflow: hidden;
}

.fl-pd-photo-btn {
  width: 100%;
  height: 100%;
  padding: 0;
  background: transparent;
  border: none;
  cursor: zoom-in;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fl-pd-photo-btn img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.fl-pd-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  color: #14532D;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fl-pd-arrow.prev { left: 12px; }
.fl-pd-arrow.next { right: 12px; }

/* Right Info */
.fl-pd-tag {
  display: inline-block;
  background: #DCF7B8;
  color: #14532D;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 9999px;
  margin-bottom: 10px;
}

.fl-pd-title {
  font-size: 32px;
  font-weight: 800;
  color: #111827;
  line-height: 1.15;
  margin: 0 0 10px 0;
  letter-spacing: -0.02em;
}

.fl-pd-desc {
  font-size: 14.5px;
  color: #4B5563;
  line-height: 1.55;
  margin: 0 0 14px 0;
}

.fl-pd-rating-row {
  display: flex;
  align-items: center;
  gap: 18px;
  flex-wrap: wrap;
  font-size: 13px;
  margin-bottom: 16px;
}

.fl-pd-stars {
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.fl-pd-stars i { color: #F59E0B; }
.fl-pd-stars strong { color: #111827; }
.fl-pd-stars span { color: #6B7280; }

.fl-pd-verified {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #14532D;
  font-weight: 600;
}

.fl-pd-price-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.fl-pd-price {
  font-size: 30px;
  font-weight: 800;
  color: #111827;
}

.fl-pd-pack-pill {
  background: #DCF0CF;
  color: #14532D;
  font-size: 12.5px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 9999px;
}

.fl-pd-label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}

.fl-pd-variants-block {
  margin-bottom: 18px;
}

.fl-pd-variants-row {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.fl-pd-var-card {
  border: 1.5px solid #D1D5DB;
  background: #FFFFFF;
  border-radius: 10px;
  padding: 8px 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  font-family: inherit;
}

.fl-pd-var-card.active {
  background: #EBF7E3;
  border-color: #14532D;
}

.fl-pd-var-card strong {
  font-size: 13.5px;
  color: #111827;
}

.fl-pd-var-card span {
  font-size: 12px;
  color: #4B5563;
}

.fl-pd-qty-block {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 22px;
}

.fl-pd-qty-block .fl-pd-label {
  margin-bottom: 0;
}

.fl-pd-stepper {
  display: inline-flex;
  align-items: center;
  border: 1px solid #D1D5DB;
  border-radius: 9999px;
  height: 36px;
  padding: 0 6px;
  gap: 14px;
}

.fl-pd-stepper button {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: none;
  background: transparent;
  color: #111827;
  cursor: pointer;
  font-size: 12px;
}

.fl-pd-stepper span {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  min-width: 16px;
  text-align: center;
}

.fl-pd-actions {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
}

.fl-pd-btn-primary {
  background: #14532D;
  color: #FFFFFF;
  border: none;
  border-radius: 9999px;
  padding: 13px 34px;
  font-size: 14.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-family: inherit;
  transition: background-color 0.2s ease;
}

.fl-pd-btn-primary:hover {
  background: #0F4022;
}

.fl-pd-btn-outline {
  background: #FFFFFF;
  color: #14532D;
  border: 1.5px solid #14532D;
  border-radius: 9999px;
  padding: 12px 28px;
  font-size: 14.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  cursor: pointer;
  font-family: inherit;
}

/* Reviews Section below Main Detail */
.fl-pd-stacked-sections {
  border-top: 1px solid #E5ECE1;
  padding-top: 26px;
  margin-bottom: 36px;
}

.fl-pd-info-section {
  min-width: 0;
}

.fl-pd-section-heading {
  display: inline-block;
  font-size: 16px;
  font-weight: 800;
  color: #14532D;
  margin: 0 0 14px 0;
  padding-bottom: 6px;
  border-bottom: 2px solid #14532D;
}

.fl-pd-reviews-summary {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}

.fl-pd-rev-score {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #F4FAF0;
  border: 1px solid #DCECD3;
  border-radius: 12px;
  padding: 10px 16px;
}

.fl-pd-rev-score strong {
  font-size: 24px;
  font-weight: 800;
  color: #14532D;
}

.fl-pd-rev-stars {
  color: #F59E0B;
  font-size: 13px;
  display: flex;
  gap: 2px;
}

.fl-pd-rev-score span {
  font-size: 12.5px;
  color: #4A5548;
  font-weight: 500;
}

.fl-pd-tab-panel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
  flex-wrap: wrap;
}

.fl-pd-tab-text {
  font-size: 14px;
  line-height: 1.65;
  color: #4B5563;
  margin: 0;
}

.fl-pd-mini-highlights {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 20px;
}

.fl-pd-mh-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #1F2937;
}

.fl-pd-mh-item i {
  font-size: 20px;
  color: #14532D;
}

.fl-pd-inline-details {
  margin-top: 24px;
  padding-top: 18px;
  border-top: 1px solid #E5ECE1;
}

.fl-pd-specs-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px 24px;
  width: 100%;
  margin: 0;
}

.fl-pd-specs-grid dt {
  font-size: 12px;
  color: #6B7280;
  margin-bottom: 2px;
}

.fl-pd-specs-grid dd {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}

/* You May Also Like */
.fl-pd-related-box {
  background: #F4FAF0;
  border-radius: 18px;
  padding: 22px 24px;
}

.fl-pd-related-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.fl-pd-related-head h2 {
  font-size: 20px;
  font-weight: 800;
  color: #111827;
  margin: 0;
}

.fl-pd-view-all {
  font-size: 13.5px;
  font-weight: 700;
  color: #14532D;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.fl-pd-related-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.fl-pd-rel-card {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  border: 1px solid #E8F0E3;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.fl-pd-rel-card:hover {
  transform: translateY(-2px);
  border-color: #3F9C1E;
}

.fl-pd-rel-img {
  width: 72px;
  height: 72px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fl-pd-rel-img img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.fl-pd-rel-body {
  flex: 1;
  min-width: 0;
}

.fl-pd-rel-body h3 {
  font-size: 13.5px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.fl-pd-rel-brand {
  display: block;
  font-size: 11.5px;
  color: #6B7280;
  margin-bottom: 4px;
}

.fl-pd-rel-rating {
  font-size: 11.5px;
  color: #6B7280;
  margin-bottom: 6px;
}

.fl-pd-rel-rating i { color: #F59E0B; }
.fl-pd-rel-rating strong { color: #111827; }

.fl-pd-rel-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.fl-pd-rel-price {
  font-size: 14px;
  font-weight: 800;
  color: #111827;
}

.fl-pd-rel-plus {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #14532D;
  color: #FFFFFF;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}

@media (max-width: 1024px) {
  .fl-pd-grid,
  .fl-pd-stacked-sections {
    grid-template-columns: 1fr;
  }
  .fl-pd-main-frame {
    height: 420px;
    min-height: 0;
  }
  .fl-pd-related-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 600px) {
  .fl-pd-gallery {
    flex-direction: column-reverse;
  }
  .fl-pd-thumbs-left {
    flex-direction: row;
    width: 100%;
  }
  .fl-pd-related-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<style scoped>
/* ------------------------------------------------------------ breadcrumb */
.breadcrumb-trail {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  font-weight: 600;
  color: #64748B;
  margin-bottom: 22px;
}

.breadcrumb-trail a {
  color: var(--primary-orange);
  text-decoration: none;
}

.breadcrumb-trail a:hover {
  text-decoration: underline;
}

.crumb-sep {
  color: #CBD5E1;
}

.crumb-current {
  color: #0F172A;
  font-weight: 700;
}

/* --------------------------------------------------------------- layout */
.product-detail {
  display: grid;
  grid-template-columns: minmax(0, 5fr) minmax(0, 6fr);
  gap: 36px;
  align-items: start;
}

/* --------------------------------------------------------------- photos */
.detail-frame {
  position: relative;
  aspect-ratio: 1 / 1;
  background: radial-gradient(circle at center, rgba(228, 101, 46, 0.05) 0%, rgba(248, 250, 252, 0.95) 100%);
  border: 1px solid #E2E8F0;
  border-radius: 18px;
  overflow: hidden;
}

.detail-photo {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  padding: 22px;
  border: none;
  background: none;
  cursor: zoom-in;
}

/* `contain`, like the catalogue card: the pack is the point, not the crop. */
.detail-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.detail-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  z-index: 2;
  color: #FFFFFF;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  padding: 5px 12px;
  border-radius: 11px;
}

.detail-zoom {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: rgba(6, 20, 39, 0.55);
  color: #FFFFFF;
  font-size: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.detail-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 3;
  width: 38px;
  height: 38px;
  border: none;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.94);
  color: #061427;
  font-size: 13px;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(6, 20, 39, 0.16);
  transition: color 0.2s var(--ease-interact);
}

.detail-arrow:hover {
  color: var(--primary-orange);
}

.detail-arrow-prev {
  left: 12px;
}

.detail-arrow-next {
  right: 12px;
}

.detail-thumbs {
  display: flex;
  gap: 10px;
  margin-top: 12px;
  flex-wrap: wrap;
}

.detail-thumb {
  width: 68px;
  height: 68px;
  padding: 6px;
  border: 1.5px solid #E2E8F0;
  border-radius: 12px;
  background: #FFFFFF;
  cursor: pointer;
  transition: border-color 0.2s var(--ease-interact);
}

.detail-thumb.active {
  border-color: var(--primary-orange);
}

.detail-thumb img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

/* ---------------------------------------------------------------- facts */
.detail-brandline {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 11.5px;
  font-weight: 800;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: #64748B;
  margin-bottom: 10px;
}

.detail-brandline i {
  color: var(--primary-orange);
}

.detail-brandline .dot {
  color: #CBD5E1;
}

.detail-variety {
  color: var(--primary-orange);
  text-decoration: none;
}

.detail-title {
  font-family: var(--font-heading);
  font-size: 30px;
  font-weight: 800;
  line-height: 1.22;
  letter-spacing: -0.5px;
  color: var(--dark-navy);
  margin-bottom: 12px;
}

.detail-desc {
  font-size: 14.5px;
  line-height: 1.7;
  color: #475569;
  margin-bottom: 20px;
}

.detail-label {
  display: block;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.7px;
  text-transform: uppercase;
  color: #94A3B8;
  margin-bottom: 7px;
}

.size-chip-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.detail-chip {
  padding: 9px 15px;
  border: 1.5px solid #E2E8F0;
  border-radius: 11px;
  background: #FFFFFF;
  font-family: var(--font-main);
  font-size: 13px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  transition: border-color 0.2s var(--ease-interact), color 0.2s var(--ease-interact), background-color 0.2s var(--ease-interact);
}

.detail-chip:hover {
  border-color: var(--primary-orange);
  color: var(--primary-orange);
}

.detail-chip.active {
  border-color: var(--primary-orange);
  background: #FDF3ED;
  color: var(--primary-orange);
}

.detail-sizes {
  margin-bottom: 20px;
}

.detail-price-row {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 18px;
  padding: 16px 0 18px;
  border-top: 1px dashed #E2E8F0;
  border-bottom: 1px dashed #E2E8F0;
  margin-bottom: 20px;
}

.detail-price {
  font-family: var(--font-heading);
  font-size: 28px;
  font-weight: 800;
  color: var(--dark-navy);
  line-height: 1;
}

.detail-price.is-request {
  font-size: 19px;
  color: var(--primary-orange);
}

.detail-pack {
  text-align: right;
}

.detail-pack-val {
  font-size: 13.5px;
  font-weight: 700;
  color: #475569;
}

.detail-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.detail-cta,
.detail-whatsapp {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  padding: 12px 24px;
  border: none;
  border-radius: 12px;
  font-family: var(--font-main);
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  text-decoration: none;
  transition: background-color 0.2s var(--ease-interact), transform 0.2s var(--ease-interact);
}

.detail-cta {
  background: var(--primary-orange);
  color: #FFFFFF;
  box-shadow: 0 8px 22px rgba(228, 101, 46, 0.28);
}

.detail-cta:hover {
  background: #C9541F;
  transform: translateY(-2px);
}

.detail-whatsapp {
  background: #FFFFFF;
  border: 1.5px solid #25D366;
  color: #128C4A;
}

.detail-whatsapp:hover {
  background: #F0FDF4;
}

/* Quieter than the two it stands beside — it is an aside, not an action. */
.detail-share-wrap {
  position: relative;
}

.detail-share {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: 1.5px solid #E2E8F0;
  border-radius: 12px;
  background: #FFFFFF;
  font-family: var(--font-main);
  font-size: 14px;
  font-weight: 800;
  color: #475569;
  cursor: pointer;
  transition: border-color 0.2s var(--ease-interact), color 0.2s var(--ease-interact);
}

.detail-share:hover {
  border-color: var(--primary-orange);
  color: var(--primary-orange);
}

.share-menu {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  z-index: 20;
  min-width: 190px;
  padding: 6px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  box-shadow: 0 18px 40px rgba(6, 20, 39, 0.16);
  animation: share-in 0.16s var(--ease-entrance) both;
}

@keyframes share-in {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: none; }
}

.share-menu a,
.share-menu button {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 11px;
  border: none;
  border-radius: 9px;
  background: none;
  font-family: var(--font-main);
  font-size: 13px;
  font-weight: 700;
  color: #0F172A;
  text-align: left;
  text-decoration: none;
  cursor: pointer;
}

.share-menu a:hover,
.share-menu button:hover {
  background: #F8FAFC;
}

.detail-specs {
  margin-top: 24px;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  overflow: hidden;
}

.detail-specs > div {
  display: grid;
  grid-template-columns: 150px minmax(0, 1fr);
  gap: 12px;
  padding: 11px 16px;
  border-bottom: 1px solid #F1F5F9;
}

.detail-specs > div:last-child {
  border-bottom: none;
}

.detail-specs > div:nth-child(odd) {
  background: #FAFBFD;
}

.detail-specs dt {
  font-size: 12px;
  font-weight: 700;
  color: #64748B;
}

.detail-specs dd {
  font-size: 13px;
  font-weight: 600;
  color: #0F172A;
  margin: 0;
}

.detail-specs dd a {
  color: var(--primary-orange);
  text-decoration: none;
}

/* -------------------------------------------------------------- related */
.related-header {
  margin-bottom: 24px;
}

.related-header .section-subtitle {
  margin-top: 6px;
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

.related-card {
  display: block;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  overflow: hidden;
  text-decoration: none;
  transition: transform 0.25s var(--ease-interact), box-shadow 0.25s var(--ease-interact), border-color 0.25s var(--ease-interact);
}

.related-card:hover {
  transform: translateY(-4px);
  border-color: var(--primary-orange);
  box-shadow: 0 14px 30px rgba(6, 20, 39, 0.09);
}

.related-img {
  aspect-ratio: 1 / 1;
  max-height: 170px;
  padding: 12px;
  background: radial-gradient(circle at center, rgba(228, 101, 46, 0.04) 0%, rgba(248, 250, 252, 0.95) 100%);
  border-bottom: 1px solid #F1F5F9;
}

.related-img img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.related-body {
  padding: 12px 14px 14px;
}

.related-brand {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: #94A3B8;
}

.related-name {
  font-family: var(--font-heading);
  font-size: 14px;
  font-weight: 700;
  color: var(--dark-navy);
  line-height: 1.35;
  margin: 4px 0 8px;
}

.related-price {
  font-size: 13.5px;
  font-weight: 800;
  color: var(--primary-orange);
}

.related-all {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 22px;
  font-size: 13.5px;
  font-weight: 800;
  color: var(--primary-orange);
  text-decoration: none;
}

/* ---------------------------------------------------------- responsive */
@media (max-width: 1024px) {
  .product-detail {
    grid-template-columns: minmax(0, 1fr);
    gap: 26px;
  }

  .related-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .breadcrumb-trail {
    font-size: 11.5px;
    gap: 6px;
  }

  .detail-title {
    font-size: 23px;
  }

  .detail-photo {
    padding: 14px;
  }

  .detail-thumb {
    width: 56px;
    height: 56px;
  }

  .detail-price {
    font-size: 24px;
  }

  .detail-specs > div {
    grid-template-columns: 118px minmax(0, 1fr);
    padding: 10px 13px;
  }

  /* Stacked and full width on a phone — there is no room for two side by
     side, and a thumb wants the whole row. */
  .detail-actions {
    flex-direction: column;
  }

  .detail-cta,
  .detail-whatsapp,
  .detail-share {
    width: 100%;
    padding: 13px;
    justify-content: center;
  }

  /* Full width like its neighbours, and the menu follows it. */
  .detail-share-wrap {
    width: 100%;
  }

  .share-menu {
    left: 0;
    right: 0;
    min-width: 0;
  }

  /* Two across, like every other grid on a phone. */
  .related-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
}
</style>
