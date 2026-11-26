<template>
  <div class="promotions-page-root">
    <TopBar />
    <Navbar activePage="promotions" @openModal="isModalOpen = true" />

    <main>
      <!-- Hero Section -->
      <section class="about-hero-section">
        <div class="container">
          <div class="about-hero-card reveal">
            <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=1600&q=80" alt="Promotions Banner" class="about-hero-bg">
            <div class="about-hero-overlay"></div>
            <div class="about-hero-content">
              <div class="breadcrumb-trail">
                <Link href="/"><i class="fa-solid fa-house"></i> Home</Link>
                <span>/</span>
                <span class="active">Promotions & Offers</span>
              </div>
              <h1 class="about-hero-title">
                Exclusive Deals & <br>
                <span class="hero-title-gradient shiny-text">Promotional Offers</span>
              </h1>
              <p class="about-hero-sub">
                Save big on monthly supermarket grocery packages, corporate B2B bulk purchases, and seasonal discounts.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Sticky Category Bar Wrapper -->
      <div ref="stickyBarWrapperRef" class="sticky-bar-wrapper">
        <div
          class="sticky-category-bar"
          :class="{ 'is-fixed': isStickyBarFixed, 'is-hidden': isHideStickyBar }"
          :style="isStickyBarFixed ? { top: `${headerOffset}px` } : null"
        >
          <div class="container" style="display:flex; justify-content:center;">
            <div ref="pillsBarRef" class="filter-pills-centered-bar">
              <button
                v-for="group in liveGroups"
                :key="group.id"
                @click="scrollToCategory(group.id)"
                class="filter-pill"
                :class="{ active: activeCategory === group.id }"
              >
                <i class="fa-solid" :class="group.icon"></i> {{ group.title }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Promotions Catalog Section -->
      <section class="section-padding" style="position:relative;">
        <div class="watermark-backdrop" data-parallax="30">OFFERS</div>
        <div ref="catalogRef" class="container" style="position:relative; z-index:2;">

          <!-- One group per offer category, each an anchor for the sticky bar.
               `liveGroups` drops any group whose offers have all expired, so a
               retired category leaves no orphan heading behind. -->
          <div
            v-for="group in liveGroups"
            :key="group.id"
            class="category-offers-group"
            :id="group.id"
          >
            <div class="group-header stagger">
              <span class="section-badge reveal"><i class="fa-solid" :class="group.icon"></i> {{ group.badge }}</span>
              <h2 class="group-title reveal">{{ group.title }}</h2>
            </div>

            <div class="promotions-catalog-grid stagger">
              <div
                v-for="promo in group.promotions"
                :key="promo.id"
                class="promo-catalog-card reveal"
                :style="cardStyle(promo)"
              >
                <div class="card-badge-row">
                  <span class="promo-badge-tag" :style="promo.badgeBg ? { backgroundColor: promo.badgeBg } : {}">{{ promo.badge }}</span>
                </div>
                <div class="card-body-row">
                  <div class="card-text-col">
                    <h3 class="promo-card-title" :style="promo.isDark ? { color: '#FFFFFF' } : {}">{{ promo.title }}</h3>
                    <div class="promo-card-discount" :style="promo.discountColor ? { color: promo.discountColor } : {}">{{ promo.discount }}</div>
                    <p class="promo-card-subtitle" :style="promo.isDark ? { color: '#CBD5E1' } : {}">{{ promo.subtitle }}</p>
                  </div>
                  <div class="card-graphic-col">
                    <img :src="promo.image" :alt="promo.title" class="promo-catalog-img">
                  </div>
                </div>
                <div class="card-bottom-bar" :style="promo.isDark ? { borderTopColor: 'rgba(255,255,255,0.15)' } : null">
                  <div class="validity-info" :style="promo.isDark ? { color: '#94A3B8' } : null">
                    <div v-if="promo.publish_at" class="date-row">
                      <i class="fa-regular fa-calendar-days"></i><span>Starts {{ promo.publish_at }}</span>
                    </div>
                    <div class="date-row">
                      <i class="fa-regular fa-calendar-days"></i>
                      <span v-if="promo.expire_at">Valid till {{ promo.expire_at }}</span>
                      <span v-else>{{ promo.validity }}</span>
                    </div>
                  </div>
                  <!-- Link from the admin if there is one, enquiry form if not. -->
                  <Link v-if="promo.ctaUrl" :href="promo.ctaUrl" class="btn-claim-deal">{{ promo.ctaText }}</Link>
                  <button v-else @click="isModalOpen = true" class="btn-claim-deal">{{ promo.ctaText }}</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Every offer expired (or none created yet). The page still has its
               reels, gallery and bulk-inquiry banner, so it says so rather than
               leaving a blank stretch above them. -->
          <div v-if="!liveGroups.length" class="offers-empty-state reveal">
            <i class="fa-regular fa-calendar-xmark"></i>
            <h3>No offers running right now</h3>
            <p>Our next round of deals is on its way — send us an enquiry and we'll share the pricing directly.</p>
            <button @click="isModalOpen = true" class="btn-claim-deal">Enquire Now</button>
          </div>

          <!-- INSTAGRAM REELS — renders nothing until a reel is added in the admin -->
          <ReelsSection :reels="reels" />

          <!-- GALLERY SECTION -->
          <GallerySection :gallery="gallery" :videos="galleryVideos" />

          <!-- BOTTOM FEATURE HIGHLIGHTS STRIP (5 COLUMNS MATCHING SCREENSHOT) -->
          <div class="feature-highlights-strip reveal stagger">
            <div class="feature-item reveal">
              <div class="feature-icon"><i class="fa-solid fa-ribbon"></i></div>
              <div>
                <h4>Best Quality Products</h4>
                <p>100% genuine & trusted products</p>
              </div>
            </div>

            <div class="feature-item reveal">
              <div class="feature-icon"><i class="fa-solid fa-tags"></i></div>
              <div>
                <h4>Great Offers Everyday</h4>
                <p>Exciting deals & discounts</p>
              </div>
            </div>

            <div class="feature-item reveal">
              <div class="feature-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
              <div>
                <h4>Bulk Order Benefits</h4>
                <p>Special pricing for bulk purchases</p>
              </div>
            </div>

            <div class="feature-item reveal">
              <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
              <div>
                <h4>Fast & Timely Delivery</h4>
                <p>On-time delivery, every time</p>
              </div>
            </div>

            <div class="feature-item reveal">
              <div class="feature-icon"><i class="fa-solid fa-headset"></i></div>
              <div>
                <h4>Customer Support</h4>
                <p>We're here to help you</p>
              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- Bulk Supply Inquiry Section (Triggers sticky bar hide on scroll) -->
      <section class="section-padding section-flush-top" id="bulkInquirySection">
        <div class="container">
          <div class="bulk-inquiry-banner reveal">
            <div class="bulk-inquiry-text">
              <h2 class="bulk-inquiry-title">Looking for Customized<br>Corporate Bulk Discounts?</h2>
              <p class="bulk-inquiry-sub">Get direct GST wholesale invoices & express delivery in Surat</p>
            </div>
            <BulkInquiryForm />
          </div>
        </div>
      </section>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <WhatsAppFloat />
    <PromoPopup @openEnquiry="isModalOpen = true" />
  </div>
</template>

<script setup>
import { computed, nextTick, ref, watch, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import BulkInquiryForm from '@/Components/BulkInquiryForm.vue';
import ReelsSection from '@/Components/ReelsSection.vue';
import GallerySection from '@/Components/GallerySection.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import { useParallax } from '@/composables/useParallax';
import { scrollTo } from '@/composables/useSmoothScroll';
import { activateReveals, isWithinSchedule, useServerClock } from '@/composables/useLiveSchedule';

const props = defineProps({
  groups: {
    type: Array,
    default: () => []
  },
  reels: {
    type: Array,
    default: () => []
  },
  gallery: {
    type: Array,
    default: () => []
  },
  galleryVideos: {
    type: Array,
    default: () => []
  },
  meta: Object
});

const isModalOpen = ref(false);

// Server-side the groups already exclude anything outside its window, but an
// offer can expire while this page is open. The ticker re-filters each group
// and drops the whole group — heading, anchor and pill — once it empties.
const now = useServerClock();
const liveGroups = computed(() => props.groups
  .map(group => ({ ...group, promotions: group.promotions.filter(promo => isWithinSchedule(promo, now.value)) }))
  .filter(group => group.promotions.length > 0));

const catalogRef = ref(null);

// Offers that only *start* mid-visit land below the scroll observer's pass, so
// give the fresh cards their revealed state directly.
//
// Keyed on the visible ids, not on `liveGroups` itself: the ticker rebuilds
// that array every 15s, and reacting to the rebuild would reveal every card
// before the reader scrolled to it. Re-point the scrollspy on any change, but
// only force-reveal when a card has actually appeared.
watch(
  () => liveGroups.value.flatMap(group => group.promotions.map(promo => promo.id)).join(','),
  (ids, previous) => {
    const before = new Set((previous ?? '').split(','));
    const gained = ids.split(',').some(id => id && !before.has(id));

    nextTick(() => {
      if (gained) activateReveals(catalogRef.value);
      observeGroups();
    });
  }
);

const activeCategory = ref(liveGroups.value[0]?.id ?? 'latest-offers');
const isHideStickyBar = ref(false);
const isStickyBarFixed = ref(false);
const stickyBarWrapperRef = ref(null);
const pillsBarRef = ref(null);
// `.main-header` is itself sticky at top: 0, so the pill bar has to park below
// it rather than at 0 — otherwise it covers the navbar once you scroll.
const headerOffset = ref(0);

// Drifts the big ghost "OFFERS" word behind the catalogue (`data-parallax`).
useParallax();

function cardStyle(promo) {
  const style = {};
  if (promo.cardBg) style.background = promo.cardBg;
  if (promo.borderColor) style.borderColor = promo.borderColor;
  if (promo.isDark) style.color = '#FFFFFF';
  return style;
}

function scrollToCategory(id) {
  activeCategory.value = id;
  const el = document.getElementById(id);
  if (!el) return;
  // Clear both fixed layers — the sticky navbar and the pill bar parked under it.
  const bar = document.querySelector('.sticky-category-bar');
  const barHeight = bar ? Math.round(bar.getBoundingClientRect().height) : 0;
  scrollTo(el, { offset: -(headerOffset.value + barHeight + 16) });
}

const measureHeader = () => {
  const header = document.querySelector('.main-header');
  headerOffset.value = header ? Math.round(header.getBoundingClientRect().height) : 0;
  handleWindowScroll();
};

// On phones the pills are one scrollable line, so the pill the scrollspy just
// activated is usually off-screen. Bring it into view as the reader moves
// between groups. A no-op on desktop, where the row never overflows.
watch(activeCategory, (id) => {
  const bar = pillsBarRef.value;
  if (!bar || bar.scrollWidth <= bar.clientWidth) return;

  const pill = bar.children[liveGroups.value.findIndex(group => group.id === id)];
  if (!pill) return;

  // Measured against the row itself — offsetLeft would be relative to the
  // positioned .sticky-category-bar, not to the scrolling content box.
  const barRect = bar.getBoundingClientRect();
  const pillRect = pill.getBoundingClientRect();
  const centred = bar.scrollLeft + (pillRect.left - barRect.left) - (bar.clientWidth - pillRect.width) / 2;

  bar.scrollTo({ left: Math.max(0, centred), behavior: 'smooth' });
});

const handleWindowScroll = () => {
  if (stickyBarWrapperRef.value) {
    const rect = stickyBarWrapperRef.value.getBoundingClientRect();
    // Hand over to `fixed` exactly where the wrapper meets the header's bottom
    // edge, so the bar keeps its place instead of jumping.
    isStickyBarFixed.value = rect.top <= headerOffset.value;

    // Retire / hide the category bar once scroll has passed the last category group
    const groupEls = document.querySelectorAll('.category-offers-group');
    if (groupEls.length > 0) {
      const lastGroup = groupEls[groupEls.length - 1];
      const lastGroupRect = lastGroup.getBoundingClientRect();
      const threshold = headerOffset.value + 60;
      isHideStickyBar.value = lastGroupRect.bottom <= threshold;
    }
  }
};

let categoryObserver = null;

// The scrollspy watches one element per group, so it has to be re-pointed when
// a group retires or starts — otherwise it keeps a removed anchor and misses
// the new one, and the pill bar stops tracking the scroll.
const observeGroups = () => {
  if (!categoryObserver) return;

  categoryObserver.disconnect();
  liveGroups.value
    .map(group => document.getElementById(group.id))
    .filter(Boolean)
    .forEach(el => categoryObserver.observe(el));

  if (!liveGroups.value.some(group => group.id === activeCategory.value)) {
    activeCategory.value = liveGroups.value[0]?.id ?? activeCategory.value;
  }
};

onMounted(() => {
  measureHeader();
  window.addEventListener('scroll', handleWindowScroll, { passive: true });
  window.addEventListener('resize', measureHeader, { passive: true });

  // 1. Scroll Reveal Intersection Observer
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

    // 2. ScrollSpy Observer for Promotions Categories
    categoryObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          activeCategory.value = entry.target.id;
        }
      });
    }, {
      threshold: 0.25,
      rootMargin: '-10% 0px -40% 0px'
    });

    observeGroups();
  } else {
    revealElements.forEach(el => el.classList.add('active'));
  }
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleWindowScroll);
  window.removeEventListener('resize', measureHeader);
  if (categoryObserver) categoryObserver.disconnect();
});
</script>

<style scoped>
/* Stands in for the whole catalogue when every offer has expired. */
.offers-empty-state {
  text-align: center;
  padding: 56px 24px;
  border: 1px dashed var(--border-color);
  border-radius: var(--radius-lg);
  background: var(--bg-card);
  margin-bottom: 48px;
}

.offers-empty-state i {
  font-size: 34px;
  color: var(--primary-orange);
  margin-bottom: 14px;
  display: block;
}

.offers-empty-state h3 {
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: 700;
  color: var(--dark-navy);
  margin-bottom: 8px;
}

.offers-empty-state p {
  font-size: 14px;
  color: var(--text-muted);
  max-width: 460px;
  margin: 0 auto 20px;
  line-height: 1.6;
}

.sticky-bar-wrapper {
  min-height: 52px;
  margin-top: 28px;
  margin-bottom: 24px;
}

/* STICKY CATEGORY BAR CONTAINER WITH HIDE TRANSITION */
.sticky-category-bar {
  position: relative;
  background: transparent;
  padding: 4px 0;
  border-bottom: none;
  box-shadow: none;
  transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
  opacity: 1;
  transform: translateY(0);
  visibility: visible;
  width: 100%;
}

.sticky-category-bar.is-fixed {
  position: fixed;
  /* `top` is set inline from the measured header height. */
  top: 0;
  left: 0;
  right: 0;
  /* Below .main-header's 1000 so the navbar and its shadow stay on top. */
  z-index: 999;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  padding: 12px 0;
  border-bottom: 1px solid #E2E8F0;
  box-shadow: 0 4px 20px rgba(6, 20, 39, 0.08);
}

.sticky-category-bar.is-hidden {
  opacity: 0;
  transform: translateY(-16px);
  pointer-events: none;
  visibility: hidden;
}

.filter-pills-centered-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
}

.filter-pill {
  background-color: #FFFFFF;
  border: 1px solid #E2E8F0;
  color: #475569;
  font-size: 13.5px;
  font-weight: 700;
  padding: 11px 22px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.filter-pill:hover {
  border-color: #E4652E;
  color: #E4652E;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(228, 101, 46, 0.12);
}

.filter-pill.active {
  background-color: #E4652E;
  border-color: #E4652E;
  color: #FFFFFF;
  box-shadow: 0 6px 20px rgba(228, 101, 46, 0.3);
  transform: translateY(-2px);
}

/* Category Offers Groups */
.category-offers-group {
  margin-bottom: 56px;
  scroll-margin-top: 150px;
}

.group-header {
  margin-bottom: 24px;
}

.group-title {
  font-family: var(--font-heading);
  font-size: 26px;
  font-weight: 800;
  color: #061427;
  margin: 6px 0 0 0;
  letter-spacing: -0.4px;
}

/* PROMOTIONS CATALOG GRID - EXACT 3 CARDS PER ROW */
.promotions-catalog-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.promo-catalog-card {
  border: 1.5px solid;
  border-radius: 20px;
  padding: 22px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  min-height: 240px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.promo-catalog-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
}

.card-badge-row {
  margin-bottom: 12px;
}

.promo-badge-tag {
  display: inline-block;
  color: #FFFFFF;
  font-size: 10.5px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 12px;
  letter-spacing: 0.8px;
  text-transform: uppercase;
}

.card-body-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 18px;
  flex: 1;
}

.card-text-col {
  flex: 1;
}

.promo-card-title {
  font-family: var(--font-heading);
  font-size: 19px;
  font-weight: 800;
  margin-bottom: 2px;
  line-height: 1.2;
}

.promo-card-discount {
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 4px;
}

.promo-card-subtitle {
  font-size: 12.5px;
  opacity: 0.85;
  line-height: 1.4;
  margin: 0;
}

.card-graphic-col {
  width: 100px;
  height: 100px;
  flex-shrink: 0;
}

.promo-catalog-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  filter: drop-shadow(0 6px 14px rgba(0,0,0,0.12));
  transition: transform 0.3s ease;
}

.promo-catalog-card:hover .promo-catalog-img {
  transform: scale(1.08);
}

/* Card Bottom Action Bar */
.card-bottom-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 14px;
  border-top: 1px dashed rgba(0,0,0,0.1);
}

.validity-info {
  font-size: 11.5px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 4px;
  opacity: 0.85;
  font-weight: 600;
}

.date-row {
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-claim-deal {
  background-color: #FFFFFF;
  color: #E4652E;
  border: 1.5px solid #E4652E;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-claim-deal:hover {
  background-color: #E4652E;
  color: #FFFFFF;
}

/* 5-COLUMN FEATURE HIGHLIGHTS STRIP */
.feature-highlights-strip {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  padding: 24px 32px;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.03);
  margin-top: 24px;
  margin-bottom: 48px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.feature-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background-color: #FDF3ED;
  color: #E4652E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.feature-item h4 {
  font-family: var(--font-heading);
  font-size: 13.5px;
  font-weight: 800;
  color: #0F172A;
  margin: 0 0 2px 0;
  line-height: 1.2;
}

.feature-item p {
  font-size: 11px;
  color: #64748B;
  margin: 0;
}

@media (max-width: 1024px) {
  .promotions-catalog-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .feature-highlights-strip {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* ---------- PHONE: two offer cards per row ---------- */
@media (max-width: 768px) {
  .sticky-bar-wrapper {
    min-height: 56px;
  }

  .sticky-category-bar {
    padding: 9px 0;
  }

  /* One scrollable line of pills — wrapping ate a third of the screen */
  .filter-pills-centered-bar {
    flex-wrap: nowrap;
    justify-content: flex-start;
    overflow-x: auto;
    gap: 8px;
    padding-bottom: 2px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }

  .filter-pills-centered-bar::-webkit-scrollbar {
    display: none;
  }

  .filter-pill {
    font-size: 12px;
    padding: 8px 14px;
    border-radius: 11px;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .filter-pill:hover,
  .filter-pill.active {
    transform: none;
  }

  .category-offers-group {
    margin-bottom: 34px;
    scroll-margin-top: 110px;
  }

  .group-title {
    font-size: 19px;
  }

  .promotions-catalog-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .promo-catalog-card {
    padding: 14px 12px;
    border-radius: 16px;
    border-width: 1px;
    min-height: 0;
  }

  .card-badge-row {
    margin-bottom: 8px;
  }

  .promo-badge-tag {
    font-size: 8.5px;
    padding: 3px 8px;
    letter-spacing: 0.4px;
  }

  /* Artwork drops under the copy so neither gets squeezed */
  .card-body-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 12px;
  }

  .card-text-col {
    width: 100%;
  }

  .promo-card-title {
    font-size: 14.5px;
  }

  .promo-card-discount {
    font-size: 15.5px;
  }

  .promo-card-subtitle {
    font-size: 11px;
    line-height: 1.35;
  }

  .card-graphic-col {
    width: 64px;
    height: 64px;
    align-self: flex-end;
    margin-top: -6px;
  }

  .card-bottom-bar {
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
    padding-top: 10px;
  }

  .validity-info {
    font-size: 10.5px;
  }

  .btn-claim-deal {
    width: 100%;
    font-size: 11.5px;
    padding: 8px 10px;
  }

  .feature-highlights-strip {
    grid-template-columns: repeat(2, 1fr);
    padding: 16px 14px;
    gap: 14px;
    border-radius: 16px;
  }

  .feature-icon {
    width: 36px;
    height: 36px;
    font-size: 15px;
    border-radius: 10px;
  }

  .feature-item {
    gap: 10px;
  }

  .feature-item h4 {
    font-size: 12px;
  }

  .feature-item p {
    font-size: 10px;
  }
}

@media (max-width: 380px) {
  .promotions-catalog-grid {
    gap: 10px;
  }

  .promo-card-title {
    font-size: 13px;
  }

  .promo-card-discount {
    font-size: 14px;
  }
}
</style>
