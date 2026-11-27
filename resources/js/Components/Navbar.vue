<template>
  <header class="fl-header" :class="{ 'is-scrolled': isScrolled }">
    <!-- Row 1: Brand + Search + Location + Account + Cart -->
    <div class="container fl-header-top">
      <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="fl-mobile-toggle fl-mobile-toggle-left" aria-label="Toggle Menu">
        <i class="fa-solid" :class="isMobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
      </button>

      <Link href="/" class="fl-logo-block">
        <span class="fl-logo-wordmark">VIZORA<span class="fl-logo-dot">.</span></span>
        <span class="fl-logo-tagline">Fresh &bull; Local &bull; Organic</span>
      </Link>

      <!-- Center Pill Search Bar -->
      <div ref="searchBoxRef" class="fl-search-wrap">
        <form class="fl-search-form" role="search" @submit.prevent="submitSearch">
          <i class="fa-solid fa-magnifying-glass fl-search-icon"></i>
          <input
            v-model="searchTerm"
            type="search"
            class="fl-search-input"
            placeholder="Search for dal, rice, honey, vegetables..."
            aria-label="Search products"
            autocomplete="off"
            @focus="onSearchFocus"
            @keydown.down.prevent="moveHighlight(1)"
            @keydown.up.prevent="moveHighlight(-1)"
            @keydown.enter="onSearchEnter"
            @keydown.esc="closeSuggestions"
          >
          <button type="submit" class="fl-search-btn">Search</button>
        </form>

        <div v-if="isSuggestOpen" id="navSearchSuggestions" class="suggest-panel" role="listbox">
          <p v-if="isSuggesting && !hasSuggestions" class="suggest-status">
            <i class="fa-solid fa-spinner fa-spin"></i> Searching…
          </p>
          <template v-else-if="hasSuggestions">
            <p v-if="suggestions.products.length" class="suggest-heading">Products</p>
            <button
              v-for="(item, i) in suggestions.products"
              :key="`p-${item.id}`"
              type="button"
              class="suggest-item"
              :class="{ active: highlight === i }"
              @mouseenter="highlight = i"
              @click="goTo(item.url)"
            >
              <span class="suggest-thumb">
                <img v-if="item.image" :src="item.image" :alt="item.name" loading="lazy">
                <i v-else class="fa-solid fa-box"></i>
              </span>
              <span class="suggest-copy">
                <span class="suggest-name" v-html="highlightMatch(item.name)"></span>
                <span class="suggest-meta">{{ [item.brand, item.category].filter(Boolean).join(' • ') }}</span>
              </span>
            </button>
            <button type="button" class="suggest-all" @click="submitSearch">
              See all results for "{{ searchTerm.trim() }}" <i class="fa-solid fa-arrow-right"></i>
            </button>
          </template>
          <p v-else class="suggest-status">
            No products matched "{{ searchTerm.trim() }}".
          </p>
        </div>
      </div>

      <!-- Right Actions: Deliver to + User + Cart -->
      <div class="fl-header-actions">
        <button type="button" class="fl-location-btn" @click="isLocationModalOpen = true">
          <i class="fa-solid fa-location-dot fl-loc-pin"></i>
          <span class="fl-loc-copy">
            <span class="fl-loc-label">Deliver to</span>
            <span class="fl-loc-val">{{ locationLabel }} <i class="fa-solid fa-chevron-down"></i></span>
          </span>
        </button>

        <Link href="/promotions" class="fl-icon-btn fl-bell-btn" aria-label="Offers">
          <i class="fa-regular fa-bell"></i>
        </Link>

        <Link href="/admin" class="fl-icon-btn fl-account-btn" aria-label="Account">
          <i class="fa-regular fa-user"></i>
        </Link>

        <Link href="/cart" class="fl-icon-btn fl-cart-btn" aria-label="Cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="fl-cart-badge">{{ totalCount }}</span>
        </Link>
      </div>
    </div>

    <!-- Mobile Location & Delivery ETA Strip -->
    <div class="fl-mobile-loc-strip">
      <div class="container fl-mobile-loc-inner">
        <button type="button" class="fl-mobile-loc-btn" @click="isLocationModalOpen = true">
          <i class="fa-solid fa-location-dot"></i>
          <span>Deliver to: <strong>{{ locationLabel }}</strong> <i class="fa-solid fa-chevron-down"></i></span>
        </button>
        <span class="fl-mobile-eta-pill">
          <i class="fa-solid fa-truck-fast"></i> Pan India Delivery
        </span>
      </div>
    </div>

    <!-- Row 2: Shop All Categories + Horizontal Nav Links -->
    <div class="fl-nav-row">
      <div class="container fl-nav-inner">
        <Link href="/categories" class="fl-all-cats-btn">
          <i class="fa-solid fa-bars"></i>
          <span>Shop All Categories</span>
          <i class="fa-solid fa-chevron-down fl-chevron-sm"></i>
        </Link>

        <nav class="fl-nav-links">
          <Link href="/" class="fl-nav-link" :class="{ active: currentNav === 'home' }" @click="clickedNav = 'home'">Home</Link>
          <Link href="/categories" class="fl-nav-link" :class="{ active: currentNav === 'categories' }" @click="clickedNav = 'categories'">Categories</Link>
          <Link href="/promotions" class="fl-nav-link" :class="{ active: currentNav === 'promotions' }" @click="clickedNav = 'promotions'">Offers</Link>
          <Link href="/products" class="fl-nav-link" :class="{ active: currentNav === 'products' }" @click="clickedNav = 'products'">New Arrivals</Link>
          <Link href="/products" class="fl-nav-link" :class="{ active: currentNav === 'b2b-categories' }" @click="clickedNav = 'b2b-categories'">Best Sellers</Link>
          <Link href="/categories" class="fl-nav-link" :class="{ active: currentNav === 'stores' }" @click="clickedNav = 'stores'">All Products</Link>
          <Link href="/about" class="fl-nav-link" :class="{ active: currentNav === 'about' }" @click="clickedNav = 'about'">About Us</Link>
        </nav>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <transition name="mobile-slide">
      <div v-if="isMobileMenuOpen" class="mobile-nav-drawer">
        <div class="mobile-nav-links">
          <Link href="/" @click="isMobileMenuOpen = false" class="mobile-nav-link">Home</Link>
          <Link href="/categories" @click="isMobileMenuOpen = false" class="mobile-nav-link">Categories</Link>
          <Link href="/promotions" @click="isMobileMenuOpen = false" class="mobile-nav-link">Offers</Link>
          <Link href="/products" @click="isMobileMenuOpen = false" class="mobile-nav-link">New Arrivals</Link>
          <Link href="/b2b-categories" @click="isMobileMenuOpen = false" class="mobile-nav-link">Best Sellers</Link>
          <Link href="/stores" @click="isMobileMenuOpen = false" class="mobile-nav-link">Recipes</Link>
          <Link href="/about" @click="isMobileMenuOpen = false" class="mobile-nav-link">Meet Our Farmers</Link>
        </div>
      </div>
    </transition>

    <LocationModal
      :isOpen="isLocationModalOpen"
      :currentCity="savedLocation.city"
      :currentPincode="savedLocation.pincode"
      @close="isLocationModalOpen = false"
      @save="saveLocation"
    />
  </header>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { useSite } from '@/composables/useSite';
import { useCart } from '@/composables/useCart';
import LocationModal from '@/Components/LocationModal.vue';

const LOCATION_STORAGE_KEY = 'vizora_location_v1';

function loadSavedLocation() {
  if (typeof window === 'undefined') return { city: '', pincode: '' };
  try {
    const raw = window.localStorage.getItem(LOCATION_STORAGE_KEY);
    if (!raw) return { city: '', pincode: '' };
    const parsed = JSON.parse(raw);
    return {
      city: String(parsed?.city || ''),
      pincode: String(parsed?.pincode || ''),
    };
  } catch {
    return { city: '', pincode: '' };
  }
}

const isLocationModalOpen = ref(false);
const savedLocation = ref(loadSavedLocation());

const locationLabel = computed(() => {
  if (savedLocation.value.city && savedLocation.value.pincode) {
    return `${savedLocation.value.city}, ${savedLocation.value.pincode}`;
  }
  if (savedLocation.value.city) return savedLocation.value.city;
  return 'Select Location';
});

function saveLocation({ city, pincode }) {
  savedLocation.value = { city, pincode };
  if (typeof window !== 'undefined') {
    window.localStorage.setItem(LOCATION_STORAGE_KEY, JSON.stringify({ city, pincode }));
  }
}

// The logo carries a version so a replaced file is not served from cache.
const { site } = useSite();
const { totalCount } = useCart();

const props = defineProps({
  activePage: {
    type: String,
    default: 'home'
  }
});

const clickedNav = ref(null);
const currentNav = computed(() => clickedNav.value ?? props.activePage);

defineEmits(['openModal']);

// One source for both the desktop bar and the mobile drawer.
const menu = [
  { key: 'home', label: 'Home', href: '/', icon: 'fa-house' },
  { key: 'categories', label: 'Store Categories', href: '/categories', icon: 'fa-layer-group' },
  { key: 'b2b-categories', label: 'B2B Categories', href: '/b2b-categories', icon: 'fa-industry' },
  { key: 'stores', label: 'Our Stores', href: '/stores', icon: 'fa-store' },
  { key: 'promotions', label: 'Updates & Offers', href: '/promotions', icon: 'fa-bullhorn' },
  { key: 'about', label: 'About Us', href: '/about', icon: 'fa-building' },
  { key: 'contact', label: 'Contact Us', href: '/contact', icon: 'fa-headset' },
];

const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);
const searchTerm = ref('');

/* ------------------------------------------------------ type-ahead search */

const searchBoxRef = ref(null);
const suggestions = ref({ products: [], categories: [] });
const isSuggesting = ref(false);
const isSuggestOpen = ref(false);
const highlight = ref(-1);

let debounce = null;
let requestId = 0;

const hasSuggestions = computed(() =>
  suggestions.value.products.length > 0 || suggestions.value.categories.length > 0
);

// Flat list in the order the panel renders, so the arrow keys can walk it.
const flatSuggestions = computed(() => [
  ...suggestions.value.products,
  ...suggestions.value.categories,
]);

// Debounced so a fast typist fires one request, not one per keystroke. The
// request id guards against an earlier, slower response landing last and
// overwriting the results for what is now a longer query.
watch(searchTerm, term => {
  const query = term.trim();

  if (debounce) clearTimeout(debounce);
  highlight.value = -1;

  if (query.length < 2) {
    suggestions.value = { products: [], categories: [] };
    isSuggesting.value = false;
    isSuggestOpen.value = false;
    return;
  }

  isSuggestOpen.value = true;
  isSuggesting.value = true;

  debounce = setTimeout(() => {
    const id = ++requestId;

    axios.get('/search/suggest', { params: { q: query } })
      .then(({ data }) => {
        if (id !== requestId) return;
        suggestions.value = {
          products: data.products || [],
          categories: data.categories || [],
        };
      })
      .catch(() => {
        if (id === requestId) suggestions.value = { products: [], categories: [] };
      })
      .finally(() => {
        if (id === requestId) isSuggesting.value = false;
      });
  }, 180);
});

function onSearchFocus() {
  if (searchTerm.value.trim().length >= 2) isSuggestOpen.value = true;
}

function closeSuggestions() {
  isSuggestOpen.value = false;
  highlight.value = -1;
}

function moveHighlight(step) {
  const total = flatSuggestions.value.length;
  if (!isSuggestOpen.value || total === 0) return;

  // -1 means "nothing picked", where Enter searches the whole catalogue.
  // Walking off either end comes back to it, so the keyboard is never trapped.
  const next = highlight.value + step;
  highlight.value = next < -1 ? total - 1 : (next >= total ? -1 : next);
}

function onSearchEnter(event) {
  const picked = flatSuggestions.value[highlight.value];

  if (picked) {
    event.preventDefault();
    goTo(picked.url);
  }
  // Otherwise the form's submit handler runs and searches the whole catalogue.
}

function goTo(url) {
  closeSuggestions();
  isMobileMenuOpen.value = false;
  router.visit(url);
}

// The panel is not inside the input, so a click anywhere else has to close it.
function onDocumentClick(event) {
  if (searchBoxRef.value && !searchBoxRef.value.contains(event.target)) closeSuggestions();
}

// Bold the typed term inside a suggestion so the match is obvious.
function highlightMatch(text) {
  const term = searchTerm.value.trim();
  const escapedText = String(text ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  })[c]);

  if (!term) return escapedText;

  const pattern = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return escapedText.replace(new RegExp(`(${pattern})`, 'ig'), '<mark>$1</mark>');
}

// The catalogue filters client-side, so the search only has to land on
// /products with the term in the query string.
function submitSearch() {
  const term = searchTerm.value.trim();
  closeSuggestions();
  isMobileMenuOpen.value = false;
  router.visit(term ? `/products?search=${encodeURIComponent(term)}` : '/products');
}

// Reading scrollY inside a passive scroll listener is cheap — the value is
// already current when the event dispatches, so it forces no layout. Writing
// only on an actual change keeps Vue from re-rendering on every scroll tick.
// (Deliberately not rAF-throttled: a `ticking` flag set before the frame
// latches forever if rAF is paused, e.g. while the tab is backgrounded.)
const handleScroll = () => {
  const scrolled = window.scrollY > 8;
  if (scrolled !== isScrolled.value) isScrolled.value = scrolled;
};

onMounted(() => {
  handleScroll(); // account for a page restored mid-scroll
  window.addEventListener('scroll', handleScroll, { passive: true });
  document.addEventListener('click', onDocumentClick);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  document.removeEventListener('click', onDocumentClick);
  if (debounce) clearTimeout(debounce);
});
</script>

<style scoped>
/* ---------------- Type-ahead suggestions ---------------- */
.nav-search-wrap {
  position: relative;
  flex-shrink: 0;
}

/* style.css hides `.nav-search` — the form — from the nav on a phone, but the
   suggestions panel is the form's sibling inside this wrapper, not its child,
   so it went on rendering: a 380px popover anchored to a collapsed box, 180px
   of it off the left edge of the screen, on top of the drawer's own inline
   suggestions. The wrapper goes with the form. */
@media (max-width: 992px) {
  .nav-search-wrap {
    display: none;
  }
}

.suggest-panel {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 380px;
  max-height: min(70vh, 480px);
  overflow-y: auto;
  z-index: 1100;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  box-shadow: 0 18px 44px rgba(6, 20, 39, 0.16);
  padding: 8px;
  animation: suggestIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Inside the mobile drawer it sits in the flow instead of floating. */
.suggest-panel.is-inline {
  position: static;
  width: auto;
  max-height: 320px;
  margin-bottom: 14px;
  box-shadow: none;
  animation: none;
}

@keyframes suggestIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

.suggest-heading {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: #94A3B8;
  margin: 8px 10px 4px;
}

.suggest-status {
  font-size: 13px;
  color: #64748B;
  padding: 14px 12px;
  margin: 0;
  text-align: center;
}

.suggest-item {
  display: flex;
  align-items: center;
  gap: 11px;
  width: 100%;
  padding: 9px 10px;
  border: none;
  border-radius: 11px;
  background: none;
  cursor: pointer;
  text-align: left;
  font: inherit;
  transition: background-color 0.15s ease;
}

.suggest-item.active,
.suggest-item:hover {
  background: #FDF3ED;
}

.suggest-thumb {
  width: 40px;
  height: 40px;
  flex-shrink: 0;
  border-radius: 10px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: #E4652E;
  font-size: 14px;
}

.suggest-thumb img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 3px;
}

.suggest-copy {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.suggest-name {
  font-family: var(--font-heading);
  font-size: 13px;
  font-weight: 700;
  color: #061427;
  line-height: 1.3;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.suggest-name :deep(mark) {
  background: none;
  color: #E4652E;
  font-weight: 800;
}

.suggest-meta {
  font-size: 11px;
  color: #64748B;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.suggest-all {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  margin-top: 6px;
  padding: 10px;
  border: none;
  border-top: 1px solid #F1F5F9;
  border-radius: 0 0 10px 10px;
  background: none;
  color: #E4652E;
  font-family: var(--font-heading);
  font-size: 12.5px;
  font-weight: 800;
  cursor: pointer;
}

.suggest-all:hover {
  background: #FDF3ED;
}

/* Mobile Drawer Styles */
.mobile-nav-drawer {
  background: #FFFFFF;
  border-bottom: 1px solid #E2E8F0;
  box-shadow: 0 10px 25px rgba(6, 20, 39, 0.1);
  padding: 16px 20px 24px 20px;
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 1000;
  max-height: calc(100vh - 72px);
  overflow-y: auto;
}

.mobile-search {
  position: relative;
  margin-bottom: 14px;
}

.mobile-search i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94A3B8;
  font-size: 14px;
}

.mobile-search input {
  width: 100%;
  padding: 12px 14px 12px 40px;
  border: 1.5px solid #E2E8F0;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: #0F172A;
  outline: none;
  background: #FFFFFF;
}

.mobile-search input:focus {
  border-color: #E4652E;
}

.mobile-nav-links {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  color: #061427;
  text-decoration: none;
  background: #F8FAFC;
  transition: all 0.2s ease;
}

.mobile-nav-link i {
  color: #E4652E;
  font-size: 16px;
  width: 20px;
}

.mobile-nav-link:hover,
.mobile-nav-link.active {
  background-color: #FDF3ED;
  color: #E4652E;
}

.mobile-cta-btn {
  width: 100%;
  margin-top: 12px;
  justify-content: center;
  padding: 14px;
}

.mobile-slide-enter-active,
.mobile-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.mobile-slide-enter-from,
.mobile-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (min-width: 993px) {
  .mobile-nav-drawer {
    display: none !important;
  }
}
</style>
