<template>
  <div class="fl-products-page">
    <TopBar />
    <Navbar :activePage="navKey" @openModal="openBlankModal" />

    <main class="fl-shop-main">
      <div class="container">
        <!-- Breadcrumb -->
        <div class="fl-breadcrumb">
          <Link href="/"><i class="fa-solid fa-house"></i> Home</Link>
          <span class="fl-bc-sep"><i class="fa-solid fa-chevron-right"></i></span>
          <Link href="/products">Shop</Link>
          <template v-for="(crumb, i) in crumbs" :key="i">
            <span class="fl-bc-sep"><i class="fa-solid fa-chevron-right"></i></span>
            <Link v-if="crumb.href" :href="crumb.href">{{ crumb.label }}</Link>
            <span v-else :class="{ active: i === crumbs.length - 1 }">{{ crumb.label }}</span>
          </template>
        </div>

        <!-- 2-Column Shop Layout: Left Filter Sidebar + Right Product Grid -->
        <div class="fl-shop-layout">
          <!-- LEFT SIDEBAR -->
          <aside class="fl-filter-sidebar">
            <div class="fl-filter-head">
              <h2>Filter Products</h2>
              <button v-if="activeFilters.length" type="button" class="fl-clear-link" @click="resetSearch">
                Clear All
              </button>
            </div>

            <!-- Categories -->
            <div class="fl-filter-group">
              <button type="button" class="fl-group-toggle" @click="openSections.categories = !openSections.categories">
                <span>Categories</span>
                <i class="fa-solid" :class="openSections.categories ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <div v-show="openSections.categories" class="fl-group-body">
                <label class="fl-check-row">
                  <input
                    type="checkbox"
                    :checked="selectedCategories.length === 0"
                    @change="selectedCategories = []"
                  >
                  <span class="fl-check-label">All Products</span>
                  <span class="fl-check-count">({{ allProducts.length }})</span>
                </label>

                <label
                  v-for="opt in (showAllCats ? categoryOptions : categoryOptions.slice(0, 5))"
                  :key="opt.value"
                  class="fl-check-row"
                >
                  <input
                    type="checkbox"
                    :value="opt.value"
                    v-model="selectedCategories"
                  >
                  <span class="fl-check-label">{{ opt.label }}</span>
                  <span class="fl-check-count">({{ opt.count }})</span>
                </label>

                <button
                  v-if="categoryOptions.length > 5"
                  type="button"
                  class="fl-show-more-btn"
                  @click="showAllCats = !showAllCats"
                >
                  {{ showAllCats ? '− Show Less' : '+ Show More' }}
                </button>
              </div>
            </div>

            <!-- Price Range -->
            <div v-if="priceBounds.max > priceBounds.min" class="fl-filter-group">
              <button type="button" class="fl-group-toggle" @click="openSections.price = !openSections.price">
                <span>Price Range</span>
                <i class="fa-solid" :class="openSections.price ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <div v-show="openSections.price" class="fl-group-body">
                <div class="fl-range-sliders">
                  <input
                    type="range"
                    :min="priceBounds.min"
                    :max="priceBounds.max"
                    :step="priceStep"
                    :value="priceLow"
                    aria-label="Minimum price"
                    class="fl-range-input"
                    @input="setPriceLow($event.target.value)"
                  >
                  <input
                    type="range"
                    :min="priceBounds.min"
                    :max="priceBounds.max"
                    :step="priceStep"
                    :value="priceHigh"
                    aria-label="Maximum price"
                    class="fl-range-input"
                    @input="setPriceHigh($event.target.value)"
                  >
                </div>
                <div class="fl-price-readout">
                  <span>₹{{ formatAmount(priceLow) }}</span>
                  <span>–</span>
                  <span>₹{{ formatAmount(priceHigh) }}</span>
                </div>
              </div>
            </div>

            <!-- Pack Size -->
            <div v-if="packSizeOptions.length" class="fl-filter-group">
              <button type="button" class="fl-group-toggle" @click="openSections.pack = !openSections.pack">
                <span>Pack Size</span>
                <i class="fa-solid" :class="openSections.pack ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <div v-show="openSections.pack" class="fl-group-body">
                <label
                  v-for="opt in packSizeOptions.slice(0, 5)"
                  :key="opt.value"
                  class="fl-check-row"
                >
                  <input type="checkbox" :value="opt.value" v-model="selectedPackSizes">
                  <span class="fl-check-label">{{ opt.label }}</span>
                  <span class="fl-check-count">({{ opt.count }})</span>
                </label>
              </div>
            </div>

            <!-- Brand -->
            <div v-if="brandOptions.length" class="fl-filter-group">
              <button type="button" class="fl-group-toggle" @click="openSections.brand = !openSections.brand">
                <span>Brand</span>
                <i class="fa-solid" :class="openSections.brand ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <div v-show="openSections.brand" class="fl-group-body">
                <label
                  v-for="opt in brandOptions.slice(0, 6)"
                  :key="opt.value"
                  class="fl-check-row"
                >
                  <input type="checkbox" :value="opt.value" v-model="selectedBrands">
                  <span class="fl-check-label">{{ opt.label }}</span>
                  <span class="fl-check-count">({{ opt.count }})</span>
                </label>
              </div>
            </div>

            <!-- Variety -->
            <div v-if="subcategoryOptions.length" class="fl-filter-group">
              <button type="button" class="fl-group-toggle" @click="openSections.sub = !openSections.sub">
                <span>Variety</span>
                <i class="fa-solid" :class="openSections.sub ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </button>
              <div v-show="openSections.sub" class="fl-group-body">
                <label
                  v-for="opt in subcategoryOptions.slice(0, 6)"
                  :key="opt.value"
                  class="fl-check-row"
                >
                  <input type="checkbox" :value="opt.value" v-model="selectedSubcategories">
                  <span class="fl-check-label">{{ opt.label }}</span>
                  <span class="fl-check-count">({{ opt.count }})</span>
                </label>
              </div>
            </div>
          </aside>

          <!-- RIGHT MAIN AREA (NO TOP BANNER IMAGE) -->
          <div class="fl-shop-content">
            <!-- Toolbar Row -->
            <div ref="gridTopRef" class="fl-shop-toolbar">
              <div class="fl-toolbar-left">
                <h1 class="fl-shop-title">
                  {{ headingLead }} {{ headingAccent }}
                  <span class="fl-shop-count">({{ filteredProducts.length }} Products)</span>
                </h1>

                <div v-if="activeFilters.length" class="fl-active-pills">
                  <button
                    v-for="chip in activeFilters"
                    :key="chip.key"
                    type="button"
                    class="fl-filter-pill"
                    @click="chip.clear()"
                  >
                    {{ chip.label }} <i class="fa-solid fa-xmark"></i>
                  </button>
                  <button type="button" class="fl-clear-link" @click="resetSearch">Clear All</button>
                </div>
              </div>

              <div class="fl-toolbar-right">
                <div class="fl-sort-box">
                  <span>Sort by:</span>
                  <select v-model="sortBy" class="fl-sort-select" aria-label="Sort products">
                    <option value="default">Popularity</option>
                    <option value="low-high">Price: Low to High</option>
                    <option value="high-low">Price: High to Low</option>
                    <option value="name">Name: A to Z</option>
                  </select>
                </div>

                <div class="fl-view-toggles">
                  <button
                    type="button"
                    class="fl-view-btn"
                    :class="{ active: viewMode === 'grid' }"
                    aria-label="Grid view"
                    @click="viewMode = 'grid'"
                  >
                    <i class="fa-solid fa-border-all"></i>
                  </button>
                  <button
                    type="button"
                    class="fl-view-btn"
                    :class="{ active: viewMode === 'list' }"
                    aria-label="List view"
                    @click="viewMode = 'list'"
                  >
                    <i class="fa-solid fa-bars"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- 4-COLUMN x 2-ROW PRODUCTS GRID (PAGE_SIZE = 8) -->
            <div
              v-if="filteredProducts.length > 0"
              class="fl-shop-grid"
              :class="{ 'is-list': viewMode === 'list' }"
            >
              <article
                v-for="item in visibleProducts"
                :key="item.id"
                class="fl-shop-card"
              >
                <div
                  class="fl-shop-img-box"
                  @mouseenter="startCycle(item)"
                  @mouseleave="stopCycle(item)"
                >
                  <span v-if="item.badge" class="fl-shop-badge">
                    {{ item.badge }}
                  </span>

                  <Link :href="item.href" class="fl-shop-img-link" :aria-label="`Open ${item.name}`">
                    <img :src="currentImage(item)" :alt="item.name" loading="lazy" decoding="async">
                  </Link>
                </div>

                <div class="fl-shop-card-body">
                  <h3 class="fl-shop-card-title">
                    <Link :href="item.href">{{ item.name }}</Link>
                  </h3>

                  <p class="fl-shop-card-unit">
                    {{ currentVariant(item)?.size || item.packSize || item.variety || 'Standard Pack' }}
                  </p>

                  <div class="fl-shop-card-meta">
                    <i class="fa-solid fa-star"></i>
                    <strong>{{ brandLabel(item) }}</strong>
                    <span>• {{ item.category }}</span>
                  </div>

                  <div v-if="item.variants && item.variants.length > 1" class="fl-shop-variants">
                    <button
                      v-for="(variant, i) in item.variants.slice(0, 3)"
                      :key="i"
                      type="button"
                      class="fl-var-chip"
                      :class="{ active: i === variantIndex(item) }"
                      @click.stop="pickVariant(item, i)"
                    >{{ variant.size }}</button>
                  </div>

                  <div class="fl-shop-price-row">
                    <span class="fl-shop-price">
                      {{ needsPrice(item) ? 'Ask for Price' : priceOf(item) }}
                    </span>
                  </div>

                  <button
                    type="button"
                    class="fl-shop-cart-btn"
                    @click="openInquiryModal(item)"
                  >
                    <i class="fa-solid fa-cart-shopping"></i>
                    {{ needsPrice(item) ? 'Ask for Price' : 'Add to Cart' }}
                  </button>
                </div>
              </article>
            </div>

            <!-- EMPTY STATE -->
            <div v-else class="fl-empty-state">
              <i class="fa-solid fa-basket-shopping"></i>
              <h3>No products matched</h3>
              <p>Try clearing some filters or searching with another keyword.</p>
              <button type="button" class="fl-shop-cart-btn" style="width:auto; padding:0 24px;" @click="resetSearch">
                Reset All Filters
              </button>
            </div>

            <!-- PAGINATION FOOTER -->
            <div v-if="filteredProducts.length > 0" class="fl-pagination-bar">
              <div class="fl-showing-text">
                Showing {{ rangeStart }}–{{ rangeEnd }} of {{ filteredProducts.length }} products
              </div>

              <div v-if="totalPages > 1" class="fl-page-numbers">
                <button
                  type="button"
                  class="fl-page-btn"
                  :disabled="currentPage === 1"
                  aria-label="Previous page"
                  @click="goToPage(currentPage - 1)"
                >
                  <i class="fa-solid fa-chevron-left"></i>
                </button>

                <template v-for="(entry, idx) in pageItems" :key="idx">
                  <span v-if="typeof entry === 'object'" class="fl-page-dots">...</span>
                  <button
                    v-else
                    type="button"
                    class="fl-page-btn"
                    :class="{ active: entry === currentPage }"
                    @click="goToPage(entry)"
                  >
                    {{ entry }}
                  </button>
                </template>

                <button
                  type="button"
                  class="fl-page-btn"
                  :disabled="currentPage === totalPages"
                  aria-label="Next page"
                  @click="goToPage(currentPage + 1)"
                >
                  <i class="fa-solid fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" :prefill="inquiryPrefill" @close="isModalOpen = false" />
    <CatalogueModal :catalogue="missingCatalogue" @close="closeCatalogueModal" />
    <WhatsAppFloat />
    <PromoPopup @openEnquiry="openBlankModal" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import FilterDropdown from '@/Components/FilterDropdown.vue';
import QuickCalloutBanner from '@/Components/QuickCalloutBanner.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import CatalogueModal from '@/Components/CatalogueModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import { useParallax } from '@/composables/useParallax';
import { useCatalogues } from '@/composables/useCatalogues';

const props = defineProps({
  products: Array,
  categoryList: {
    type: Array,
    default: () => []
  },
  varietiesByCategory: {
    type: Object,
    default: () => ({})
  },
  brandList: {
    type: Array,
    default: () => []
  },
  // '' | 'retail' | 'b2b' — set when the visitor came in from one of the two
  // category pages, and the grid was scoped to that side of the catalogue.
  segment: {
    type: String,
    default: ''
  },
  initialCategory: String,
  initialVariety: String,
  initialSearch: String,
  backHref: {
    type: String,
    default: '/categories'
  },
  meta: Object
});

const isModalOpen = ref(false);
const inquiryPrefill = ref(null);

// Drifts the big ghost section words and the hero backdrop.
useParallax();

/* ------------------------------------------------------------------ filters */

const searchQuery = ref('');
const selectedBrands = ref([]);
const selectedCategories = ref([]);
const selectedSubcategories = ref([]);
const selectedPackSizes = ref([]);
// null on either end means "not narrowed" — that is what keeps a full-width
// range from excluding the Price-on-Request items.
const priceMin = ref(null);
const priceMax = ref(null);
const sortBy = ref('default');

const allProducts = computed(() => props.products || []);

// Deep links arrive as ?category=&variety= from both category pages, and their
// casing is whatever was in the URL. Snap them onto the real catalogue value so
// the multi-selects match by string like every other option.
function canonical(value, pool) {
  const needle = String(value).trim().toLowerCase();
  return pool.find(item => String(item).toLowerCase() === needle) ?? String(value).trim();
}

function syncFromProps() {
  const category = props.initialCategory && props.initialCategory !== 'all' ? props.initialCategory : '';
  const categoryPool = [...props.categoryList, ...allProducts.value.map(p => p.category)].filter(Boolean);
  const varietyPool = [
    ...Object.values(props.varietiesByCategory || {}).flat(),
    ...allProducts.value.map(p => p.variety),
  ].filter(Boolean);

  selectedCategories.value = category ? [canonical(category, categoryPool)] : [];
  selectedSubcategories.value = props.initialVariety ? [canonical(props.initialVariety, varietyPool)] : [];
  searchQuery.value = props.initialSearch || '';
}

syncFromProps();

// Inertia reuses this component instance when navigating from /products to
// /products (e.g. the navbar link while a category is open), so setup() does not
// re-run and these refs would keep the old filter. Follow the props instead.
watch(() => [props.initialCategory, props.initialVariety, props.initialSearch], syncFromProps);

const showBackLink = computed(() => Boolean(props.segment)
  || Boolean(props.initialCategory && props.initialCategory !== 'all'));

/* ------------------------------------------------- where the visitor is */

// The heading, the breadcrumb and the navbar all read the *live* filters
// rather than the URL, so narrowing the Category dropdown renames the page
// instead of leaving a stale title above a changed grid.
const activeCategory = computed(() => (selectedCategories.value.length === 1 ? selectedCategories.value[0] : ''));
const activeVariety = computed(() => (selectedSubcategories.value.length === 1 ? selectedSubcategories.value[0] : ''));

const segmentLabel = computed(() => {
  if (props.segment === 'b2b') return 'B2B Categories';
  if (props.segment === 'retail') return 'Store Categories';
  return '';
});

// Lights up the matching top-nav item. Without a segment the visitor arrived
// from search, which belongs to neither side, so nothing is highlighted.
const navKey = computed(() => {
  if (props.segment === 'b2b') return 'b2b-categories';
  if (props.segment === 'retail') return 'categories';
  return 'products';
});

const crumbs = computed(() => {
  const list = [];

  if (segmentLabel.value) list.push({ label: segmentLabel.value, href: props.backHref });

  if (activeCategory.value) {
    list.push({ label: activeCategory.value, href: null });
    if (activeVariety.value) list.push({ label: activeVariety.value, href: null });
  } else {
    list.push({ label: props.segment ? 'All Products' : 'Product Catalogue', href: null });
  }

  return list;
});

const headingLead = computed(() => activeVariety.value
  || activeCategory.value
  || (props.segment === 'b2b' ? 'B2B' : 'Supermarket'));

const headingAccent = computed(() => (activeCategory.value ? 'Products' : 'Product Catalogue'));

const subHeading = computed(() => {
  if (activeCategory.value) {
    return activeVariety.value
      ? `${activeVariety.value} in ${activeCategory.value} — sizes, brands and pack options we stock.`
      : `Everything we stock under ${activeCategory.value}, with brands and pack sizes.`;
  }

  return props.segment === 'b2b'
    ? 'Bulk and commercial supply items — quoted with GST pricing and delivered to your site.'
    : 'Browse our complete list of genuine supermarket items, daily essentials & GST wholesale supplies.';
});

/* ------------------------------------------------------- pack size variants */

// Which chip is lit on a given card. Untouched cards follow the filters: with
// a Pack Size filter on, the card opens on the size that actually matched,
// rather than making the visitor hunt for it.
const chosenVariant = reactive({});
const chosenImage = reactive({});

function variantsOf(product) {
  return product.variants?.length ? product.variants : [];
}

function defaultVariantIndex(product) {
  const list = variantsOf(product);
  if (!list.length) return 0;

  if (selectedPackSizes.value.length) {
    const match = list.findIndex(variant => selectedPackSizes.value.includes(packBucket(variant.size)));
    if (match >= 0) return match;
  }

  return 0;
}

function variantIndex(product) {
  const list = variantsOf(product);
  if (!list.length) return 0;

  const chosen = chosenVariant[product.id];

  return chosen !== undefined && chosen < list.length ? chosen : defaultVariantIndex(product);
}

function currentVariant(product) {
  return variantsOf(product)[variantIndex(product)] ?? null;
}

function pickVariant(product, index) {
  chosenVariant[product.id] = index;
  // The new size may carry its own photos, so start that set from the top.
  chosenImage[product.id] = 0;
}

/* -------------------------------------------------------------- card photos */

function imagesOf(product) {
  const variant = currentVariant(product);
  const list = (variant?.images?.length ? variant.images : product.images) || [];

  if (list.length) return list;

  return product.image ? [product.image] : [];
}

function imageIndex(product) {
  const list = imagesOf(product);
  const chosen = chosenImage[product.id] ?? 0;

  return chosen < list.length ? chosen : 0;
}

function currentImage(product) {
  return imagesOf(product)[imageIndex(product)] ?? product.image;
}

function pickImage(product, index) {
  chosenImage[product.id] = index;
}

/**
 * One photo forward or back, wrapping at both ends so the arrows never dead-end
 * on a two-photo product.
 */
function stepImage(product, direction) {
  const total = imagesOf(product).length;

  if (total < 2) return;

  // Stops the hover cycle taking the photo away again a moment later.
  stopCycle(product);

  chosenImage[product.id] = (imageIndex(product) + direction + total) % total;
}

// Desktop only: hovering a card walks through its photos. Touch devices get
// the dots instead, since they never fire a lasting hover.
const hoverTimers = {};

function startCycle(product) {
  if (imagesOf(product).length < 2) return;

  stopCycle(product);

  hoverTimers[product.id] = setInterval(() => {
    const total = imagesOf(product).length;
    chosenImage[product.id] = ((chosenImage[product.id] ?? 0) + 1) % total;
  }, 1200);
}

function stopCycle(product) {
  if (hoverTimers[product.id]) {
    clearInterval(hoverTimers[product.id]);
    delete hoverTimers[product.id];
  }
}

onUnmounted(() => {
  Object.values(hoverTimers).forEach(clearInterval);
});

/* -------------------------------------------------------------------- price */

// A product the admin never priced behaves exactly like one flagged "Price on
// request" — the card asks for a quote instead of printing a blank.
function needsPrice(product) {
  const variant = currentVariant(product);
  const onRequest = variant ? variant.priceOnRequest : product.priceOnRequest;
  const price = variant ? variant.price : product.price;

  return Boolean(onRequest) || !String(price ?? '').trim();
}

function priceOf(product) {
  const variant = currentVariant(product);

  return variant ? variant.price : product.price;
}

// Prices arrive as display strings ("₹1,299", "₹ 45.50 / kg"), so strip
// everything that is not part of the number before comparing.
function parsePrice(raw) {
  const parsed = parseFloat(String(raw ?? '').replace(/[^\d.]/g, ''));

  return Number.isNaN(parsed) ? null : parsed;
}

// Every price a product can be bought at — one per pack size. Price-on-request
// sizes contribute nothing, exactly as a price-on-request product always has.
function priceValues(product) {
  const list = variantsOf(product);
  const sources = list.length ? list : [{ price: product.price, priceOnRequest: product.priceOnRequest }];

  return sources
    .filter(source => !source.priceOnRequest)
    .map(source => parsePrice(source.price))
    .filter(value => value !== null);
}

// Sorting and the range bounds go by what the product starts at.
function priceValue(product) {
  const values = priceValues(product);

  return values.length ? Math.min(...values) : null;
}

// A product's pack sizes: one per variant, or the single legacy field.
function packSizesOf(product) {
  const list = variantsOf(product);

  return list.length ? list.map(variant => variant.size) : [product.packSize];
}

/* ------------------------------------------------------------- pack sizes */

// `pack_size` is free text the admin types per product ("5 Litre Can",
// "125g x 5 Soap Bars"), so it is very nearly unique per row. Filtering on the
// raw strings gave a list of a hundred one-hit options; the filter offers these
// buckets instead, while the card still prints whatever was typed.
const PACK_BUCKET_ORDER = [
  'Up to 500 g',
  '500 g – 1 kg',
  '1 – 5 kg',
  '5 kg & above',
  'Up to 1 litre',
  '1 – 5 litre',
  '5 litre & above',
  'Single unit',
  'Multipack / combo',
];

// Longer unit spellings first, so "grams" is never read as a bare "g".
const PACK_UNIT = /(\d+(?:\.\d+)?)\s*(kilograms?|kilos?|kgs?|grams?|gms?|g|millilitres?|mls?|ml|litres?|liters?|ltrs?|ltr|l)\b/i;

function packBucket(raw) {
  const text = String(raw ?? '').trim();
  if (!text) return null;

  const hit = text.match(PACK_UNIT);

  if (hit) {
    const unit = hit[2].toLowerCase();
    // "12 x 70g" and "125g x 5" both describe a pack that holds several of the
    // matched size, so the bucket has to be the total, not the single piece.
    const times = text.match(/(\d+(?:\.\d+)?)\s*[x×]/i) ?? text.match(/[x×]\s*(\d+(?:\.\d+)?)/i);
    const amount = parseFloat(hit[1]) * (times ? parseFloat(times[1]) : 1);

    if (/^k/.test(unit) || /^g/.test(unit)) {
      const grams = /^k/.test(unit) ? amount * 1000 : amount;
      if (grams < 500) return 'Up to 500 g';
      if (grams < 1000) return '500 g – 1 kg';
      if (grams < 5000) return '1 – 5 kg';
      return '5 kg & above';
    }

    const millilitres = /^m/.test(unit) ? amount : amount * 1000;
    if (millilitres < 1000) return 'Up to 1 litre';
    if (millilitres < 5000) return '1 – 5 litre';
    return '5 litre & above';
  }

  // No unit at all — "12 Pieces Crate", "Single Book", "1 Unit + 3 Jars".
  const count = text.match(/(\d+)/);
  const pieces = count ? parseInt(count[1], 10) : 1;
  return pieces > 1 || /\+|combo|pack of/i.test(text) ? 'Multipack / combo' : 'Single unit';
}

const priceBounds = computed(() => {
  // Every pack size counts, so the slider reaches the dearest one on offer.
  const values = allProducts.value.flatMap(priceValues);
  if (!values.length) return { min: 0, max: 0 };
  return { min: Math.floor(Math.min(...values)), max: Math.ceil(Math.max(...values)) };
});

const priceStep = computed(() => {
  const span = priceBounds.value.max - priceBounds.value.min;
  return span > 400 ? Math.round(span / 200) : 1;
});

const priceLow = computed(() => priceMin.value ?? priceBounds.value.min);
const priceHigh = computed(() => priceMax.value ?? priceBounds.value.max);
const isPriceFiltered = computed(() => priceMin.value !== null || priceMax.value !== null);

function setPriceLow(raw) {
  const value = Math.min(Math.max(Number(raw), priceBounds.value.min), priceHigh.value);
  priceMin.value = value <= priceBounds.value.min ? null : value;
}

function setPriceHigh(raw) {
  const value = Math.max(Math.min(Number(raw), priceBounds.value.max), priceLow.value);
  priceMax.value = value >= priceBounds.value.max ? null : value;
}

function clearPrice() {
  priceMin.value = null;
  priceMax.value = null;
}

const priceFillStyle = computed(() => {
  const { min, max } = priceBounds.value;
  const span = max - min || 1;
  return {
    left: `${((priceLow.value - min) / span) * 100}%`,
    right: `${100 - ((priceHigh.value - min) / span) * 100}%`,
  };
});

function formatAmount(value) {
  return Number(value || 0).toLocaleString('en-IN');
}

/**
 * A brand cell may name several brands at once.
 *
 * The admin's sheet has one Brand column, and a product carried under more than
 * one label is typed into it as "Amul / Mother Dairy / Hocco". Each of those is
 * its own brand as far as the filter is concerned: it lists them separately,
 * and picking any one of them finds this product.
 *
 * Split on slashes, pipes, semicolons and commas only — never on "&", which
 * belongs inside names like "Head & Shoulders".
 */
const BRAND_SPLIT = /\s*[/|;,]\s*/;

/** The spelling the catalogue uses, keyed lowercase. */
const brandCanon = computed(() => new Map(
  (props.brandList || []).map(brand => [String(brand).toLowerCase(), String(brand)]),
));

function brandsOf(product) {
  return String(product?.brand ?? '')
    .split(BRAND_SPLIT)
    .map(brand => brand.trim())
    .filter(Boolean)
    // "AMUL" in one row and "Amul" in the next are the same brand, and the
    // filter should offer it once.
    .map(brand => brandCanon.value.get(brand.toLowerCase()) ?? brand);
}

/**
 * The card's brand line, spaced out however the cell was typed:
 * "Amul/Mother Dairy/Hocco" reads as "Amul / Mother Dairy / Hocco".
 */
function brandLabel(product) {
  return brandsOf(product).join(' / ');
}

/**
 * One predicate for the grid and for the facet counts. `skip` leaves a single
 * filter out, which is how each dropdown can show how many products its own
 * options would add on top of everything else that is already selected.
 */
function passes(product, skip = null) {
  if (skip !== 'brand' && selectedBrands.value.length
    && !brandsOf(product).some(brand => selectedBrands.value.includes(brand))) return false;
  if (skip !== 'category' && selectedCategories.value.length && !selectedCategories.value.includes(product.category)) return false;
  if (skip !== 'subcategory' && selectedSubcategories.value.length && !selectedSubcategories.value.includes(product.variety)) return false;
  // A product survives if any one of its pack sizes matches.
  if (skip !== 'pack' && selectedPackSizes.value.length
    && !packSizesOf(product).some(size => selectedPackSizes.value.includes(packBucket(size)))) return false;

  if (skip !== 'price' && isPriceFiltered.value) {
    const values = priceValues(product);
    if (!values.some(value => value >= priceLow.value && value <= priceHigh.value)) return false;
  }

  if (skip !== 'search' && searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase();
    const haystack = ['name', 'brand', 'category', 'variety', 'description', 'keywords', 'sku']
      .map(field => String(product[field] ?? '').toLowerCase())
      // Sizes and their codes too, so "500 ml" and a supplier SKU both land.
      .concat(packSizesOf(product).map(size => String(size ?? '').toLowerCase()))
      .concat(variantsOf(product).map(variant => String(variant.sku ?? '').toLowerCase()));
    if (!haystack.some(text => text.includes(q))) return false;
  }

  return true;
}

/**
 * Options for one facet, counted against every *other* active filter.
 * `valuesOf` may return one value or several — a product with three pack sizes
 * belongs to three buckets, but counts once in each.
 */
function facetOptions(skip, valuesOf, order = null, selected = [], always = []) {
  const counts = new Map();

  // Values that stay on the list whether or not anything is stocked under them
  // — see subcategoryOptions, where an empty panel would read as a broken
  // filter rather than as an empty shelf.
  for (const value of always) {
    if (value) counts.set(value, 0);
  }

  for (const product of allProducts.value) {
    const values = new Set([].concat(valuesOf(product)).filter(Boolean));

    for (const value of values) {
      if (!counts.has(value)) counts.set(value, 0);
      if (passes(product, skip)) counts.set(value, counts.get(value) + 1);
    }
  }

  // Only values that can still add something are offered — that is what keeps
  // the Brand list down to the brands actually stocked in the open category.
  // A value already ticked stays on the list even at zero, so the visitor's own
  // choice never vanishes from the dropdown they picked it in.
  const options = [...counts.entries()]
    .filter(([value, count]) => count > 0 || selected.includes(value) || always.includes(value))
    .map(([value, count]) => ({ value, label: value, count }));

  if (order?.length) {
    const rank = new Map(order.map((name, index) => [String(name).toLowerCase(), index]));
    return options.sort((a, b) =>
      (rank.get(a.label.toLowerCase()) ?? Infinity) - (rank.get(b.label.toLowerCase()) ?? Infinity)
      || a.label.localeCompare(b.label));
  }

  return options.sort((a, b) => a.label.localeCompare(b.label, undefined, { numeric: true }));
}

const brandOptions = computed(() => facetOptions('brand', brandsOf, props.brandList, selectedBrands.value));
const categoryOptions = computed(() => facetOptions('category', p => p.category, props.categoryList, selectedCategories.value));

/**
 * Every variety of the open category, in the admin's order — stocked or not.
 *
 * Only while a category is actually picked: on the "all products" view the
 * whole catalogue's varieties would be a wall of empty rows, so there the list
 * stays driven by what the grid holds.
 */
const knownSubcategories = computed(() => {
  const map = props.varietiesByCategory || {};

  if (!selectedCategories.value.length) return [];

  return selectedCategories.value.flatMap(category => map[category] || []);
});

const subcategoryOptions = computed(() => facetOptions(
  'subcategory',
  p => p.variety,
  // Catalogue order: varieties grouped under their category, as the admin set them.
  Object.values(props.varietiesByCategory || {}).flat(),
  selectedSubcategories.value,
  knownSubcategories.value,
));
const packSizeOptions = computed(() => facetOptions(
  'pack',
  p => packSizesOf(p).map(packBucket),
  PACK_BUCKET_ORDER,
  selectedPackSizes.value,
));

const filteredProducts = computed(() => {
  const list = allProducts.value.filter(product => passes(product));

  if (sortBy.value === 'low-high') {
    return [...list].sort((a, b) => (priceValue(a) ?? Infinity) - (priceValue(b) ?? Infinity));
  }
  if (sortBy.value === 'high-low') {
    return [...list].sort((a, b) => (priceValue(b) ?? -Infinity) - (priceValue(a) ?? -Infinity));
  }
  if (sortBy.value === 'name') {
    return [...list].sort((a, b) => a.name.localeCompare(b.name));
  }

  return list;
});

// Chips shown above the grid, one per value that is actually narrowing results.
const activeFilters = computed(() => {
  const chips = [];

  if (searchQuery.value.trim()) {
    chips.push({
      key: 'search',
      label: `Search: ${searchQuery.value.trim()}`,
      clear: () => { searchQuery.value = ''; }
    });
  }

  const facets = [
    ['Category', selectedCategories],
    ['Subcategory', selectedSubcategories],
    ['Brand', selectedBrands],
    ['Pack', selectedPackSizes],
  ];

  for (const [label, model] of facets) {
    for (const value of model.value) {
      chips.push({
        key: `${label}:${value}`,
        label: `${label}: ${value}`,
        clear: () => { model.value = model.value.filter(item => item !== value); }
      });
    }
  }

  if (isPriceFiltered.value) {
    chips.push({
      key: 'price',
      label: `Price: ₹${formatAmount(priceLow.value)} – ₹${formatAmount(priceHigh.value)}`,
      clear: clearPrice
    });
  }

  return chips;
});

function resetSearch() {
  searchQuery.value = '';
  selectedBrands.value = [];
  selectedCategories.value = [];
  selectedSubcategories.value = [];
  selectedPackSizes.value = [];
  clearPrice();
  sortBy.value = 'default';
}

/* ------------------------------------------------------ numbered pagination */

// A page's worth of cards is all that is ever mounted, so a category with a few
// hundred products stays as light as a small one.
const PAGE_SIZE = 8;
const currentPage = ref(1);
const gridTopRef = ref(null);

const totalPages = computed(() => Math.max(1, Math.ceil(filteredProducts.value.length / PAGE_SIZE)));

const visibleProducts = computed(() => {
  const start = (currentPage.value - 1) * PAGE_SIZE;
  return filteredProducts.value.slice(start, start + PAGE_SIZE);
});

// "Showing 25–48 of 132"
const rangeStart = computed(() => (filteredProducts.value.length ? (currentPage.value - 1) * PAGE_SIZE + 1 : 0));
const rangeEnd = computed(() => Math.min(currentPage.value * PAGE_SIZE, filteredProducts.value.length));

// First, last and a window around the current page, with gaps marked so the bar
// never grows past ~7 buttons however long the catalogue gets.
const pageItems = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;

  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

  const pages = new Set([1, total, current]);
  if (current > 1) pages.add(current - 1);
  if (current < total) pages.add(current + 1);
  if (current <= 3) pages.add(2).add(3).add(4);
  if (current >= total - 2) pages.add(total - 1).add(total - 2).add(total - 3);

  const sorted = [...pages].filter(n => n >= 1 && n <= total).sort((a, b) => a - b);
  const items = [];

  sorted.forEach((page, i) => {
    if (i > 0 && page - sorted[i - 1] > 1) items.push({ gap: true, key: `gap-${page}` });
    items.push({ page, key: `p-${page}` });
  });

  return items.map(item => (item.gap ? item : item.page));
});

function goToPage(page) {
  const target = Math.min(totalPages.value, Math.max(1, page));
  if (target === currentPage.value) return;

  currentPage.value = target;

  // Put the reader back at the top of the grid rather than leaving them
  // stranded beside the pagination bar of the page they just left.
  nextTick(() => {
    const top = gridTopRef.value?.getBoundingClientRect().top ?? 0;
    window.scrollTo({ top: window.scrollY + top - 100, behavior: 'smooth' });
  });
}

// Any change to the result set starts the paging over.
watch(filteredProducts, () => {
  currentPage.value = 1;
});

/* --------------------------------------------------------------- inquiries */

function openBlankModal() {
  // Clears the product name and message left behind by an earlier "Ask Now".
  inquiryPrefill.value = {
    inquiryType: props.segment === 'b2b' ? 'b2b' : 'retail',
    productName: '',
    message: '',
  };
  isModalOpen.value = true;
}

function openInquiryModal(item) {
  const variant = currentVariant(item);
  const packSize = variant ? variant.size : item.packSize;

  const lines = [`Product: ${item.name}`];
  if (item.brand) lines.push(`Brand: ${item.brand}`);
  // The size they had selected, not the product's first one — otherwise the
  // quote comes back for the wrong pack.
  if (packSize) lines.push(`Pack Size: ${packSize}`);
  if (variant?.sku || item.sku) lines.push(`Code: ${variant?.sku || item.sku}`);

  inquiryPrefill.value = {
    // B2B categories quote differently, so the form opens on the right desk.
    inquiryType: item.segment === 'b2b' ? 'b2b' : 'retail',
    // Named at the top of the form, so the visitor can see which card they
    // clicked without having to read it back out of the message box.
    productName: item.name,
    message: `${lines.join('\n')}\n\nPlease share pricing and availability.`,
  };
  isModalOpen.value = true;
}

const openSections = reactive({
  categories: true,
  price: true,
  pack: true,
  brand: false,
  sub: false,
});
const showAllCats = ref(false);
const viewMode = ref('grid');

const { downloadCatalogue, missingCatalogue, closeCatalogueModal } = useCatalogues();

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
});
</script>

<style scoped>
.fl-products-page {
  background-color: #FFFFFF;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-shop-main {
  padding: 18px 0 56px;
}

.fl-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #6B7280;
  margin-bottom: 18px;
  flex-wrap: wrap;
}

.fl-breadcrumb a {
  color: #6B7280;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.fl-breadcrumb a:hover,
.fl-breadcrumb .active {
  color: #14532D;
  font-weight: 600;
}

.fl-bc-sep {
  font-size: 10px;
  color: #9CA3AF;
}

/* 2-Column Shop Layout */
.fl-shop-layout {
  display: grid;
  grid-template-columns: 245px minmax(0, 1fr);
  gap: 28px;
  align-items: start;
}

/* Left Filter Sidebar */
.fl-filter-sidebar {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 14px;
  padding: 18px;
  position: sticky;
  top: 120px;
}

.fl-filter-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 14px;
  border-bottom: 1px solid #EEF2EC;
  margin-bottom: 4px;
}

.fl-filter-head h2 {
  font-size: 16px;
  font-weight: 800;
  color: #111827;
  margin: 0;
}

.fl-clear-link {
  background: transparent;
  border: none;
  color: #3F9C1E;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  padding: 0;
  font-family: inherit;
}

.fl-filter-group {
  border-bottom: 1px solid #EEF2EC;
  padding: 12px 0;
}

.fl-filter-group:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.fl-group-toggle {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: transparent;
  border: none;
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  cursor: pointer;
  padding: 2px 0;
  font-family: inherit;
}

.fl-group-toggle i {
  font-size: 11px;
  color: #4B5563;
}

.fl-group-body {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.fl-check-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #374151;
  cursor: pointer;
}

.fl-check-row input[type="checkbox"] {
  accent-color: #14532D;
  width: 15px;
  height: 15px;
  cursor: pointer;
}

.fl-check-label {
  flex: 1;
  line-height: 1.3;
}

.fl-check-count {
  color: #9CA3AF;
  font-size: 12px;
}

.fl-show-more-btn {
  background: transparent;
  border: none;
  color: #14532D;
  font-size: 12.5px;
  font-weight: 700;
  text-align: left;
  cursor: pointer;
  padding: 2px 0 0;
  font-family: inherit;
}

.fl-range-sliders {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.fl-range-input {
  width: 100%;
  accent-color: #14532D;
  cursor: pointer;
}

.fl-price-readout {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  margin-top: 4px;
}

/* Right Main Content */
.fl-shop-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 18px;
}

.fl-toolbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.fl-shop-title {
  font-size: 22px;
  font-weight: 800;
  color: #111827;
  margin: 0;
}

.fl-shop-count {
  font-size: 13.5px;
  font-weight: 500;
  color: #6B7280;
  margin-left: 4px;
}

.fl-active-pills {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.fl-filter-pill {
  background: #EAF6E2;
  color: #14532D;
  border: 1px solid #C8E4B7;
  border-radius: 9999px;
  padding: 4px 10px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  font-family: inherit;
}

.fl-toolbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.fl-sort-box {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #4B5563;
}

.fl-sort-select {
  border: 1px solid #D1D5DB;
  border-radius: 8px;
  padding: 7px 12px;
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  background: #FFFFFF;
  font-family: inherit;
  cursor: pointer;
}

.fl-view-toggles {
  display: flex;
  align-items: center;
  gap: 4px;
}

.fl-view-btn {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  border: 1px solid #D1D5DB;
  background: #FFFFFF;
  color: #4B5563;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.fl-view-btn.active {
  background: #14532D;
  border-color: #14532D;
  color: #FFFFFF;
}

/* 4-Column x 2-Row Products Grid */
.fl-shop-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

.fl-shop-grid.is-list {
  grid-template-columns: 1fr;
}

.fl-shop-grid.is-list .fl-shop-card {
  flex-direction: row;
  align-items: center;
  gap: 20px;
}

.fl-shop-grid.is-list .fl-shop-img-box {
  width: 210px;
  flex-shrink: 0;
}

.fl-shop-card {
  background: #FFFFFF;
  border: 1px solid #E5ECE1;
  border-radius: 16px;
  padding: 12px;
  box-shadow: 0 2px 10px rgba(20, 83, 45, 0.04);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.fl-shop-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(20, 83, 45, 0.08);
}

/* Larger Image Box */
.fl-shop-img-box {
  position: relative;
  background: #F4F5EF;
  border-radius: 12px;
  height: 200px;
  padding: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.fl-shop-img-link {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fl-shop-img-link img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.25s ease;
}

.fl-shop-card:hover .fl-shop-img-link img {
  transform: scale(1.04);
}

.fl-shop-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;
  background-color: #14532D;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 6px;
}

.fl-shop-card-body {
  padding: 12px 4px 2px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.fl-shop-card-title {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 3px 0;
  line-height: 1.3;
}

.fl-shop-card-title a {
  color: inherit;
  text-decoration: none;
}

.fl-shop-card-title a:hover {
  color: #14532D;
}

.fl-shop-card-unit {
  font-size: 12.5px;
  color: #6B7280;
  margin: 0 0 6px 0;
}

.fl-shop-card-meta {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  margin-bottom: 8px;
}

.fl-shop-card-meta i {
  color: #F59E0B;
}

.fl-shop-card-meta strong {
  color: #111827;
}

.fl-shop-card-meta span {
  color: #6B7280;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.fl-shop-variants {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.fl-var-chip {
  border: 1px solid #D5E4CE;
  background: #FAFBF9;
  color: #374151;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  cursor: pointer;
  font-family: inherit;
}

.fl-var-chip.active {
  background: #DCF0CF;
  border-color: #14532D;
  color: #14532D;
}

.fl-shop-price-row {
  margin-top: auto;
  margin-bottom: 10px;
}

.fl-shop-price {
  font-size: 17px;
  font-weight: 800;
  color: #14532D;
}

.fl-shop-cart-btn {
  width: 100%;
  height: 38px;
  border: none;
  border-radius: 8px;
  background-color: #14532D;
  color: #FFFFFF;
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.fl-shop-cart-btn:hover {
  background-color: #0F4022;
}

.fl-empty-state {
  text-align: center;
  padding: 56px 20px;
  border: 1px dashed #D5E4CE;
  border-radius: 16px;
  background: #FAFBF9;
}

.fl-empty-state i {
  font-size: 40px;
  color: #3F9C1E;
  margin-bottom: 12px;
}

.fl-empty-state h3 {
  margin: 0 0 6px;
  color: #111827;
}

.fl-empty-state p {
  margin: 0 0 18px;
  color: #6B7280;
}

/* Pagination */
.fl-pagination-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 28px;
  flex-wrap: wrap;
  gap: 14px;
}

.fl-showing-text {
  font-size: 13px;
  color: #6B7280;
  font-weight: 500;
}

.fl-page-numbers {
  display: flex;
  align-items: center;
  gap: 8px;
}

.fl-page-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 1px solid #E5ECE1;
  background: #FFFFFF;
  color: #111827;
  font-size: 13px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-family: inherit;
}

.fl-page-btn.active {
  background: #14532D;
  border-color: #14532D;
  color: #FFFFFF;
}

.fl-page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.fl-page-dots {
  color: #6B7280;
  font-size: 13px;
  padding: 0 4px;
}

@media (max-width: 1120px) {
  .fl-shop-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 900px) {
  .fl-shop-layout {
    grid-template-columns: 1fr;
  }
  .fl-filter-sidebar {
    position: static;
  }
  .fl-shop-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 540px) {
  .fl-shop-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<style scoped>
/* The shared breadcrumb is coloured for the dark page heroes (white text on a
   photo). This page's header sits on white, so the separators and the current
   crumb need dark ink or they read as blank space. */
.products-header-bar .breadcrumb-trail {
  color: #94A3B8;
  flex-wrap: wrap;
  row-gap: 4px;
}

.products-header-bar .breadcrumb-trail .active {
  color: #061427;
}

/* PRODUCTS HEADER & SEARCH */
.products-header-bar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid #E2E8F0;
  flex-wrap: wrap;
  gap: 20px;
}

.page-main-heading {
  font-family: var(--font-heading);
  font-size: 34px;
  font-weight: 800;
  color: #061427;
  margin: 6px 0 4px 0;
  letter-spacing: -0.6px;
}

.page-sub-heading {
  font-size: 14.5px;
  color: #64748B;
  margin: 0;
}

.search-tools {
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-box-wrapper {
  position: relative;
  width: 340px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94A3B8;
  font-size: 15px;
}

.search-input {
  width: 100%;
  padding: 12px 40px 12px 46px;
  border: 1.5px solid #E2E8F0;
  border-radius: 14px;
  font-size: 13.5px;
  font-weight: 600;
  color: #0F172A;
  outline: none;
  background: #FFFFFF;
  box-shadow: 0 4px 14px rgba(6, 20, 39, 0.03);
  transition: all 0.25s ease;
}

.search-input:focus {
  border-color: #E4652E;
  box-shadow: 0 4px 20px rgba(228, 101, 46, 0.12);
}

.clear-search {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94A3B8;
  cursor: pointer;
  font-size: 14px;
}

/* ADVANCED FILTER BAR */
.filter-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  padding: 12px 16px;
  margin-bottom: 20px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
}

.filter-bar-main {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  min-width: 0;
}

.filter-bar-legend {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 11.5px;
  font-weight: 800;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding-right: 6px;
  border-right: 1px solid #E2E8F0;
  margin-right: 2px;
}

.filter-bar-legend i {
  color: #E4652E;
  font-size: 12px;
}

.filter-back {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: #FDF3ED;
  border: 1px solid #F3D2BF;
  color: #E4652E;
  font-size: 12.5px;
  font-weight: 700;
  padding: 9px 14px;
  border-radius: 12px;
  white-space: nowrap;
}

.filter-back i {
  font-size: 11px;
  transition: transform 0.2s var(--ease-interact);
}

.filter-back:hover i {
  transform: translateX(-3px);
}

.filter-bar-end {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.sort-label {
  font-size: 11.5px;
  font-weight: 800;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.sort-select {
  padding: 9px 12px;
  border: 1.5px solid #E2E8F0;
  border-radius: 12px;
  font-family: var(--font-main);
  font-size: 12.5px;
  font-weight: 700;
  color: #0F172A;
  background: #FFFFFF;
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s var(--ease-interact);
}

.sort-select:focus,
.sort-select:hover {
  border-color: #E4652E;
}

/* PRICE RANGE POPOVER */
.price-panel {
  padding: 16px 16px 8px;
}

.price-readout {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 14px;
}

.price-readout-val {
  font-family: var(--font-heading);
  font-size: 15px;
  font-weight: 800;
  color: #061427;
}

.price-readout-dash {
  color: #94A3B8;
  font-weight: 700;
}

/* Two range inputs stacked on one track: the inputs themselves are transparent
   and only their thumbs take the pointer, so both ends stay grabbable. */
.price-slider {
  position: relative;
  height: 22px;
  margin-bottom: 14px;
}

.price-track,
.price-fill {
  position: absolute;
  top: 9px;
  height: 4px;
  border-radius: 2px;
}

.price-track {
  left: 0;
  right: 0;
  background: #E2E8F0;
}

.price-fill {
  background: linear-gradient(90deg, var(--primary-orange, #E4652E), #F29163);
}

.price-thumb {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 22px;
  margin: 0;
  background: none;
  pointer-events: none;
  -webkit-appearance: none;
  appearance: none;
}

.price-thumb:focus {
  outline: none;
}

.price-thumb::-webkit-slider-thumb {
  pointer-events: auto;
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 2.5px solid var(--primary-orange, #E4652E);
  box-shadow: 0 2px 8px rgba(6, 20, 39, 0.18);
  cursor: grab;
}

.price-thumb::-moz-range-thumb {
  pointer-events: auto;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 2.5px solid var(--primary-orange, #E4652E);
  box-shadow: 0 2px 8px rgba(6, 20, 39, 0.18);
  cursor: grab;
}

.price-thumb::-moz-range-track {
  background: none;
}

.price-fields {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.price-fields label {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.price-fields span {
  font-size: 10.5px;
  font-weight: 800;
  color: #94A3B8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.price-fields input {
  width: 100%;
  min-width: 0;
  padding: 8px 10px;
  border: 1.5px solid #E2E8F0;
  border-radius: 9px;
  font-family: var(--font-main);
  font-size: 12.5px;
  font-weight: 700;
  color: #0F172A;
  outline: none;
}

.price-fields input:focus {
  border-color: #E4652E;
}

.price-note {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  font-size: 11px;
  line-height: 1.45;
  color: #94A3B8;
  margin: 12px 0 4px;
}

.price-note i {
  color: #CBD5E1;
  margin-top: 2px;
}

/* ACTIVE FILTER CHIPS */
.active-filters-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin: 0 0 16px 0;
}

.active-filter-chip {
  background: #FDF3ED;
  border: 1px solid #F3D2BF;
  color: #E4652E;
  font-size: 11.5px;
  font-weight: 700;
  padding: 5px 10px;
  border-radius: 9px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  max-width: 100%;
}

.active-filter-chip i {
  font-size: 10px;
}

.btn-clear-chip {
  background: none;
  border: none;
  color: #E4652E;
  cursor: pointer;
  font-size: 11px;
  padding: 0;
  line-height: 1;
}

.chip-clear-all {
  background: none;
  border: none;
  color: #64748B;
  font-size: 11.5px;
  font-weight: 700;
  text-decoration: underline;
  cursor: pointer;
  padding: 0 4px;
}

.chip-clear-all:hover {
  color: #E4652E;
}

/* STATUS ROW */
.products-status-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.status-count {
  font-size: 13.5px;
  color: #64748B;
}

.status-count strong {
  color: #061427;
  font-weight: 800;
}

/* PRODUCTS GRID */
.products-grid {
  display: grid !important;
  grid-template-columns: repeat(4, 1fr) !important;
  /* Every row as tall as the tallest card on the page, not just the tallest
     in its own row. Names run to one line or two and only some products carry
     a description or size chips, so without this each row settles at its own
     height and the shelf steps up and down as you scroll. */
  grid-auto-rows: 1fr !important;
  gap: 24px !important;
  margin-bottom: 40px !important;
  opacity: 1 !important;
  visibility: visible !important;
}

.product-card {
  background: #FFFFFF;
  border: 1.5px solid #E2E8F0;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  transition: transform 0.3s var(--ease-interact), box-shadow 0.3s var(--ease-interact), border-color 0.3s var(--ease-interact);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  opacity: 1 !important;
  visibility: visible !important;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
  border-color: #E4652E;
}

/* Square frame — every card in a row lines up regardless of the photo — but
   capped, because at four across on a wide screen a card is over 400px and a
   true square made the shelf enormous. Below that width the cap never bites
   and the frame stays square. No padding: the photo is the tile. */
.product-img-box {
  aspect-ratio: 1 / 1;
  max-height: 220px;
  background: radial-gradient(circle at center, rgba(228, 101, 46, 0.04) 0%, rgba(248, 250, 252, 0.95) 100%);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid #F1F5F9;
}

.product-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  color: #FFFFFF;
  font-size: 9.5px;
  font-weight: 800;
  padding: 3px 10px;
  border-radius: 10px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  z-index: 2;
}

.img-wrapper {
  width: 100%;
  height: 100%;
  /* Inside the frame, not part of it: the box keeps its size and the photo
     sits within this inset, clear of the badge and the border. */
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* `contain`: the whole photo, never a crop. Filling the frame made the packs
   read as one consistent size, but it also cut the top and bottom off every
   shot — a rice bag lost its brand line, an oil can lost its cap — and a
   catalogue exists to show the pack, not to frame it prettily.

   The frame itself is untouched: same square, same cap, same background, so
   the cards and the shelf keep the exact geometry they had. Only what happens
   inside the box changed. The padding keeps a photo off the frame's edge and
   out from under the badge.

   No drop shadow either — it belonged to a cutout floating on a background,
   and reads as a grey smear under a photo. */
.product-cutout-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
  transition: transform 0.35s ease;
}

/* Gentler than the old 1.1: a zoom big enough to push the edges of the photo
   out of a `contain` frame would undo the point of it. */
.product-card:hover .product-cutout-img {
  transform: scale(1.05);
}

/* Content stacks from the top and the price block is pushed to the floor by
   its own auto margin. `space-between` would instead share the leftover height
   out between every child, so a short card would sit with gaps in odd places
   now that all the cards are the same height. */
.product-details-box {
  padding: 14px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.brand-tag {
  font-size: 11px;
  font-weight: 700;
  color: #64748B;
  text-transform: uppercase;
  margin-bottom: 4px;
}

/* Held at two lines, whether the name needs them or not: a supplier's names
   run from "Toor Dal" to "Disinfectant Cleaner Multi-Purpose Liquid - Lime
   Fresh", and letting that set the height staggers everything below it. */
.product-name-link {
  color: inherit;
  text-decoration: none;
}

.product-name-link:hover {
  color: var(--primary-orange);
}

.product-name {
  font-family: var(--font-heading);
  font-size: 15.5px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 5px 0;
  line-height: 1.3;
  min-height: 2.6em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Deliberately quiet — it supports the name, it does not compete with it. */
.product-desc {
  font-size: 12px;
  line-height: 1.5;
  color: #94A3B8;
  margin: 0 0 8px 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.variety-chip {
  font-size: 11.5px;
  font-weight: 700;
  color: #E4652E;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* PHOTO DOTS — sit over the image, step aside for the hover CTA on desktop */
/* Above .btn-product-ask-overlay (z-index 3) so a click reaches the arrow and
   not the enquiry button underneath it. */
.img-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 4;
  width: 30px;
  height: 30px;
  border: none;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.92);
  color: #061427;
  font-size: 11px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(6, 20, 39, 0.18);
  opacity: 0;
  transition: opacity 0.2s var(--ease-interact), background-color 0.2s var(--ease-interact);
}

.product-img-box:hover .img-arrow,
.img-arrow:focus-visible {
  opacity: 1;
}

.img-arrow:hover {
  background: #FFFFFF;
  color: var(--primary-orange);
}

.img-arrow-prev {
  left: 8px;
}

.img-arrow-next {
  right: 8px;
}

.img-dots {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 12px;
  z-index: 3;
  display: flex;
  justify-content: center;
  gap: 6px;
  transition: opacity 0.2s var(--ease-interact);
}

.img-dot {
  width: 7px;
  height: 7px;
  padding: 0;
  border: none;
  border-radius: 50%;
  background: rgba(6, 20, 39, 0.22);
  cursor: pointer;
  transition: background-color 0.2s var(--ease-interact), transform 0.2s var(--ease-interact);
}

.img-dot.active {
  background: #E4652E;
  transform: scale(1.3);
}

@media (hover: hover) {
  .product-card:hover .img-dots {
    opacity: 0;
    pointer-events: none;
  }
}

/* PACK SIZE CHIPS — stand where the single pack line used to */
.size-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 12px;
}

.size-chip {
  padding: 4px 10px;
  border: 1.5px solid #E2E8F0;
  border-radius: 8px;
  background: #FFFFFF;
  color: #475569;
  font-family: var(--font-main);
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: border-color 0.2s var(--ease-interact), color 0.2s var(--ease-interact), background-color 0.2s var(--ease-interact);
}

.size-chip:hover {
  border-color: #E4652E;
  color: #E4652E;
}

.size-chip.active {
  background: #FDF3ED;
  border-color: #E4652E;
  color: #E4652E;
}

.price-pack-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  /* Anchors the price, and the Ask button after it, to the bottom of the card
     however much or little sits above. */
  margin-top: auto;
  padding-top: 10px;
  border-top: 1px dashed #E2E8F0;
}

.price-val {
  font-family: var(--font-heading);
  font-size: 18px;
  font-weight: 800;
  color: #061427;
}

.price-on-request {
  font-size: 13px;
  color: #E4652E;
  letter-spacing: -0.2px;
}

.pack-size {
  font-size: 11.5px;
  color: #64748B;
  font-weight: 600;
  white-space: nowrap;
}

/* Hover-only CTA on Desktop */
.btn-product-ask-overlay {
  position: absolute;
  left: 12px;
  right: 12px;
  bottom: 12px;
  z-index: 3;
  background-color: #E4652E;
  color: #FFFFFF;
  border: none;
  padding: 10px;
  border-radius: 12px;
  font-family: var(--font-main);
  font-size: 13px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-shadow: 0 6px 18px rgba(6, 20, 39, 0.22);
  opacity: 0;
  transform: translateY(10px);
  pointer-events: none;
  transition: opacity 0.25s var(--ease-interact), transform 0.25s var(--ease-interact), background-color 0.25s var(--ease-interact);
}

.product-card:hover .btn-product-ask-overlay,
.product-card:focus-within .btn-product-ask-overlay {
  opacity: 1;
  transform: none;
  pointer-events: auto;
}

.btn-product-ask-overlay:hover {
  background-color: #C9541F;
}

/* Mobile CTA Button (hidden on desktop, shown on mobile below price) */
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

/* NUMBERED PAGINATION */
.pagination-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
  margin: 0 0 48px 0;
  padding-top: 24px;
  border-top: 1px solid #E2E8F0;
}

.pagination-range {
  font-size: 13px;
  color: #64748B;
}

.pagination-range strong {
  color: #061427;
}

.pagination-pages {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-left: auto;
}

.page-btn {
  min-width: 38px;
  height: 38px;
  padding: 0 11px;
  border: 1.5px solid #E2E8F0;
  border-radius: 10px;
  background: #FFFFFF;
  color: #334155;
  font-family: var(--font-heading);
  font-size: 13.5px;
  font-weight: 800;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.2s var(--ease-interact), color 0.2s var(--ease-interact), background-color 0.2s var(--ease-interact);
}

.page-btn:hover:not(:disabled):not(.active) {
  border-color: #E4652E;
  color: #E4652E;
}

.page-btn.active {
  background: #E4652E;
  border-color: #E4652E;
  color: #FFFFFF;
  box-shadow: 0 6px 16px rgba(228, 101, 46, 0.24);
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.page-arrow {
  font-size: 11px;
}

.page-gap {
  min-width: 20px;
  text-align: center;
  color: #94A3B8;
  font-weight: 700;
  user-select: none;
}

/* EMPTY STATE */
.empty-products-box {
  text-align: center;
  padding: 60px 20px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  margin-bottom: 48px;
}

.empty-icon {
  font-size: 48px;
  color: #CBD5E1;
  margin-bottom: 16px;
}

.empty-products-box h3 {
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: 800;
  color: #061427;
  margin-bottom: 6px;
}

.empty-products-box p {
  font-size: 13.5px;
  color: #64748B;
  margin-bottom: 20px;
}

.btn-reset-search {
  background-color: #E4652E;
  color: #FFFFFF;
  border: none;
  padding: 10px 22px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
}

/* 4-COLUMN VALUE HIGHLIGHTS BAR */
.value-highlights-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  padding: 24px 32px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.03);
}

.value-highlight-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.vh-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background-color: #F0F5FD;
  color: #061427;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.value-highlight-item h4 {
  font-family: var(--font-heading);
  font-size: 14px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 2px 0;
}

.value-highlight-item p {
  font-size: 11.5px;
  color: #64748B;
  margin: 0;
}

/* MASTER CATALOGUES BANNER */
.master-catalogues-banner {
  background: linear-gradient(135deg, #040E1B 0%, #061427 100%);
  border-radius: var(--radius-banner);
  padding: 36px 44px;
  color: #FFFFFF;
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 32px;
  align-items: center;
  box-shadow: 0 16px 40px rgba(6, 20, 39, 0.15);
}

.master-sub {
  font-size: 11.5px;
  font-weight: 800;
  color: #E4652E;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 6px;
  display: block;
}

.master-title {
  font-family: var(--font-heading);
  font-size: 28px;
  font-weight: 800;
  color: #FFFFFF;
  margin-bottom: 8px;
}

.master-desc {
  font-size: 13.5px;
  color: #CBD5E1;
  line-height: 1.5;
  margin: 0;
}

.master-cards-row {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.master-download-card {
  background: #FFFFFF;
  border-radius: 14px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  color: #061427;
}

.pdf-icon {
  font-size: 28px;
}

.master-download-card h4 {
  font-family: var(--font-heading);
  font-size: 13.5px;
  font-weight: 800;
  margin: 0 0 6px 0;
}

.btn-download-orange {
  background-color: #E4652E;
  color: #FFFFFF;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-download-navy {
  background-color: #061427;
  color: #FFFFFF;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr) !important;
  }
  .value-highlights-card {
    grid-template-columns: repeat(2, 1fr);
  }
  .master-catalogues-banner {
    grid-template-columns: 1fr;
  }
}

/* ---------- PHONE: two products per row, compact cards ---------- */
@media (max-width: 768px) {
  .search-tools {
    width: 100%;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .search-box-wrapper {
    width: 100%;
  }

  /* One scrollable line of filter chips, exactly like the pills bar was */
  .filter-bar {
    padding: 10px 12px;
    border-radius: 14px;
    gap: 10px;
  }

  .filter-bar-main {
    width: 100%;
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    /* Room for the chips' focus ring and the popover's shadow */
    padding-bottom: 2px;
  }

  .filter-bar-main::-webkit-scrollbar {
    display: none;
  }

  .filter-bar-main > * {
    flex-shrink: 0;
  }

  .filter-bar-legend {
    display: none;
  }

  .filter-bar-end {
    width: 100%;
    justify-content: space-between;
  }

  .sort-select {
    flex: 1;
    min-width: 0;
    /* 16px keeps iOS from zooming the page on focus */
    font-size: 16px;
    padding: 8px 10px;
  }

  .status-count {
    font-size: 12.5px;
  }

  .pagination-bar {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    padding-top: 18px;
    margin-bottom: 32px;
  }

  .pagination-range {
    text-align: center;
    font-size: 12px;
  }

  .pagination-pages {
    margin: 0 auto;
    gap: 5px;
  }

  .page-btn {
    min-width: 34px;
    height: 34px;
    padding: 0 8px;
    font-size: 12.5px;
    border-radius: 9px;
  }

  .products-status-row {
    margin-bottom: 16px;
  }

  .products-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 12px !important;
    margin-bottom: 28px !important;
  }

  .product-card {
    border-radius: 16px;
    border-width: 1px;
  }

  .product-img-box {
    /* Two across on a phone never reaches the desktop cap, so the frame is
       square here and the photo fills it. */
    max-height: none;
  }

  /* Half a screen wide is little enough already — the inset comes down so the
     photo keeps as much of the frame as it can. */
  .img-wrapper {
    padding: 8px;
  }

  .product-badge {
    font-size: 8px;
    padding: 2px 7px;
    top: 8px;
    left: 8px;
  }

  .product-details-box {
    padding: 12px 11px 13px;
  }

  .brand-tag {
    font-size: 9.5px;
    margin-bottom: 3px;
  }

  .product-name {
    font-size: 13px;
    margin-bottom: 4px;
    line-height: 1.25;
    /* Kept in step with the line-height above — two lines, whatever it is. */
    min-height: 2.5em;
  }

  .product-desc {
    font-size: 10.5px;
    margin-bottom: 6px;
  }

  .variety-chip {
    font-size: 10px;
    margin-bottom: 10px;
    gap: 4px;
  }

  .size-chips {
    gap: 5px;
    margin-bottom: 9px;
  }

  .size-chip {
    padding: 3px 8px;
    font-size: 10px;
    border-width: 1px;
    border-radius: 7px;
  }

  /* A phone has no hover, so the arrows are simply always there — and a
     little bigger, because a thumb is not a cursor. */
  .img-arrow {
    opacity: 1;
    width: 28px;
    height: 28px;
    font-size: 10px;
    background: rgba(255, 255, 255, 0.86);
  }

  .img-arrow-prev {
    left: 6px;
  }

  .img-arrow-next {
    right: 6px;
  }

  /* Bigger tap targets, and no hover CTA to make room for on touch */
  .img-dots {
    bottom: 8px;
    gap: 8px;
  }

  .img-dot {
    width: 6px;
    height: 6px;
  }

  .price-pack-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    padding-top: 8px;
  }

  .price-val {
    font-size: 15px;
  }

  .price-on-request {
    font-size: 11.5px;
  }

  .btn-product-ask-overlay {
    display: none !important;
  }

  .btn-product-ask-mobile {
    display: flex;
  }

  .value-highlights-card {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 380px) {
  .products-grid {
    gap: 10px !important;
  }

  .product-name {
    font-size: 12px;
  }
}
</style>
