<template>
  <div class="categories-page-root">
    <TopBar />
    <Navbar activePage="b2b-categories" @openModal="isModalOpen = true" />

    <main>
      <!-- Premium gradient hero -->
      <CategoryHero
        :banner="banner"
        crumb="B2B Categories"
        badge="Corporate Supply"
        badge-icon="fa-industry"
        title="Bulk Supply For"
        highlight="Every Business"
        subtitle="Canteen, housekeeping, office and industrial consumables — quoted with GST pricing and delivered to your site."
        :count="categories.length"
        :varieties="varietyCount"
      />

      <!-- Editable USP cards -->
      <UspCards :cards="uspCards" />

      <!-- Main Catalog Section -->
      <section class="section-padding" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">CATEGORIES</div>
        <div class="container" style="position:relative; z-index:2;">

          <!-- 4 INDEPENDENT COLUMN STACKS (ONLY THE CARD BELOW THE EXPANDED CARD PUSHES DOWN) -->
          <div
            class="category-columns-wrapper reveal"
            :style="{ gridTemplateColumns: `repeat(${columnCount}, minmax(0, 1fr))` }"
          >
            <div 
              v-for="(col, colIdx) in categoryColumns" 
              :key="colIdx" 
              class="category-column"
            >
              <template v-for="cat in col" :key="cat.id">
              <!-- Closing tile: straight through to the full catalogue -->
              <Link
                v-if="cat.isShowAll"
                :href="showAllHref"
                class="premium-cat-card show-all-card"
              >
                <span class="show-all-icon"><i class="fa-solid fa-grip"></i></span>
                <span class="show-all-title">Show All Products</span>
                <span class="show-all-sub">Browse the complete {{ showAllLabel }} catalogue in one list</span>
                <span class="show-all-cta">Open catalogue <i class="fa-solid fa-arrow-right"></i></span>
              </Link>

              <div
                v-else
                class="premium-cat-card"
                :class="{ 'is-expanded': expandedCatId === cat.id }"
              >
                <!-- Top Graphic Box with Cutout Image -->
                <div class="card-hero-box" @click="toggleExpandCard(cat.id)">
                  <div class="img-wrapper">
                    <!-- Categories awaiting a cut-out PNG leave the box empty rather
                         than rendering a broken image; .card-hero-box is a fixed
                         185px so the card keeps its height either way. -->
                    <img v-if="cat.image" :src="cat.image" :alt="cat.name" class="cat-cutout-img" loading="lazy" decoding="async">
                  </div>
                </div>

                <!-- Card Details Box (Clean & Center Aligned) -->
                <div class="card-details-box">
                  <div class="card-title-row" @click="toggleExpandCard(cat.id)">
                    <h3 class="cat-title">{{ cat.name }}</h3>
                  </div>

                  <!-- IN-PLACE EXPANDED CONTENT PANEL (Only visible when card is clicked) -->
                  <div v-show="expandedCatId === cat.id" class="expanded-card-panel">
                    <div class="expanded-title">
                      <i class="fa-solid fa-boxes-packing" style="color:#E4652E;"></i> Product Varieties:
                    </div>

                    <div class="expanded-varieties-list">
                      <Link
                        :href="cat.allHref"
                        class="expanded-variety-item is-view-all"
                      >
                        <i class="fa-solid fa-list"></i>
                        <span>View All {{ cat.name }}</span>
                        <i class="fa-solid fa-arrow-right" style="margin-left:auto; font-size:10px;"></i>
                      </Link>

                      <Link 
                        v-for="(sub, i) in cat.subCategories" 
                        :key="i" 
                        :href="`/products?segment=b2b&category=${encodeURIComponent(cat.name)}&variety=${encodeURIComponent(sub)}`"
                        class="expanded-variety-item"
                      >
                        <i class="fa-solid fa-circle-check" style="color:#E4652E;"></i>
                        <span>{{ sub }}</span>
                        <i class="fa-solid fa-arrow-right" style="margin-left:auto; font-size:10px; color:#E4652E;"></i>
                      </Link>
                    </div>
                  </div>

                  <!-- Bottom Action Toggle Button -->
                  <div class="card-action-bar">
                    <button @click.stop="toggleExpandCard(cat.id)" class="btn-explore-cat">
                      <span>{{ expandedCatId === cat.id ? 'Close Varieties' : 'Explore Varieties' }}</span>
                      <i class="fa-solid" :class="expandedCatId === cat.id ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                  </div>
                </div>

              </div>
              </template>
            </div>
          </div>

        </div>
      </section>

      <!-- B2B order process — steps slide across as the section scrolls -->
      <OrderProcess :steps="orderSteps" />

      <!-- Download Master Catalogues Banner — nothing uploaded in the admin,
           nothing shown here. -->
      <section v-if="catalogueFor('retail') || catalogueFor('b2b')" class="section-padding">
        <div class="container">
          <div class="master-catalogues-banner reveal">
            <div class="master-banner-text">
              <span class="master-sub">DOWNLOAD OUR CATALOGUES</span>
              <h2 class="master-title">Explore Our Master Catalogues</h2>
              <p class="master-desc">Download our comprehensive Retail and B2B Master Catalogues to explore our complete product range.</p>
            </div>

            <div class="master-cards-row">
              <div v-if="catalogueFor('retail')" class="master-download-card">
                <div class="pdf-icon"><i class="fa-solid fa-file-pdf" style="color:#E4652E;"></i></div>
                <div>
                  <h4>Retail Master Catalog (PDF)</h4>
                  <button @click="downloadCatalogue('retail')" class="btn-download-orange">
                    Download Now <i class="fa-solid fa-download"></i>
                  </button>
                </div>
              </div>

              <div v-if="catalogueFor('b2b')" class="master-download-card">
                <div class="pdf-icon"><i class="fa-solid fa-file-pdf" style="color:#061427;"></i></div>
                <div>
                  <h4>B2B Master Catalog (PDF)</h4>
                  <button @click="downloadCatalogue('b2b')" class="btn-download-navy">
                    Download Now <i class="fa-solid fa-download"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick Contact Footer Callout -->
      <QuickCalloutBanner />
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <CatalogueModal :catalogue="missingCatalogue" @close="closeCatalogueModal" />
    <WhatsAppFloat />
    <PromoPopup @openEnquiry="isModalOpen = true" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import QuickCalloutBanner from '@/Components/QuickCalloutBanner.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import CatalogueModal from '@/Components/CatalogueModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import CategoryHero from '@/Components/CategoryHero.vue';
import UspCards from '@/Components/UspCards.vue';
import OrderProcess from '@/Components/OrderProcess.vue';
import { useCatalogues } from '@/composables/useCatalogues';
import { useColumnCount, splitIntoColumns } from '@/composables/useColumnCount';
import { useParallax } from '@/composables/useParallax';

const props = defineProps({
  banner: {
    type: Object,
    default: () => null
  },
  categories: Array,
  uspCards: {
    type: Array,
    default: () => []
  },
  orderSteps: {
    type: Array,
    default: () => []
  },
  meta: Object
});

// Drifts the hero glows and the big ghost section word.
useParallax();

const varietyCount = computed(() =>
  (props.categories || []).reduce((total, cat) => total + (cat.subCategories?.length || 0), 0)
);

// Where the closing tile points, and how it names the section.
const showAllHref = '/products?segment=b2b';
const showAllLabel = 'B2B supply';

const isModalOpen = ref(false);
const expandedCatId = ref(null);

// 4 stacks on desktop, 3 on tablet, 2 on phone — the split has to follow the
// viewport or the phone shows four DOM columns crammed into two grid tracks.
const { columnCount } = useColumnCount();

const categoryColumns = computed(() => {
  // The "Show all products" tile rides along as one more item in the deal, so
  // it lands in the very next slot after the last category — directly beside
  // it in reading order — instead of being parked at the foot of whichever
  // column happened to be shortest, which left a gap on the right.
  const items = [...(props.categories || []), { id: '__show_all__', isShowAll: true }];

  return splitIntoColumns(items, columnCount.value);
});

function toggleExpandCard(id) {
  if (expandedCatId.value === id) {
    expandedCatId.value = null;
  } else {
    expandedCatId.value = id;
  }
}

// catalogueFor tells the template whether a PDF has actually been uploaded,
// so the banner and each card only exist when there is something to download.
const { catalogueFor, downloadCatalogue, missingCatalogue, closeCatalogueModal } = useCatalogues();

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
/* PREMIUM HEADER BAR */
.catalog-header-premium {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 40px;
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

.count-pill {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  padding: 10px 20px;
  border-radius: 14px;
  font-size: 13.5px;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

/* 4 INDEPENDENT COLUMN STACKS LAYOUT */
.category-columns-wrapper {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  align-items: start;
  margin-bottom: 48px;
}

.category-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.premium-cat-card {
  background: #FFFFFF;
  border: 1.5px solid #E2E8F0;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  cursor: pointer;
  min-height: 280px;
  opacity: 1 !important;
  visibility: visible !important;
}

.premium-cat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
  border-color: #E4652E;
}

.premium-cat-card.is-expanded {
  border-color: #E4652E;
  box-shadow: 0 16px 40px rgba(228, 101, 46, 0.14);
  background: #FFFFFF;
}

/* TOP HERO GRAPHIC BOX WITH LARGE PNG CUTOUT */
.card-hero-box {
  height: 185px;
  background: radial-gradient(circle at center, rgba(228, 101, 46, 0.05) 0%, rgba(248, 250, 252, 0.95) 100%);
  padding: 8px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid #F1F5F9;
  overflow: hidden;
}

.img-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
}

.cat-cutout-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transform: scale(1.1);
  filter: drop-shadow(0 10px 20px rgba(6, 20, 39, 0.15));
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.premium-cat-card:hover .cat-cutout-img {
  transform: scale(1.22);
}

/* CARD DETAILS BODY */
.card-details-box {
  padding: 16px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  text-align: center;
}

.card-title-row {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  width: 100%;
}

.cat-title {
  font-family: var(--font-heading);
  font-size: 17.5px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 14px 0;
  line-height: 1.25;
  text-align: center;
  width: 100%;
}

/* IN-PLACE EXPANDED PANEL STYLES */
.expanded-card-panel {
  background: #FAFBFD;
  border: 1.5px solid #F8E2D5;
  border-radius: 14px;
  padding: 14px;
  margin-bottom: 16px;
  animation: expandFade 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  text-align: left;
}

@keyframes expandFade {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.expanded-title {
  font-family: var(--font-heading);
  font-size: 12.5px;
  font-weight: 800;
  color: #061427;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 6px;
  padding-bottom: 8px;
  border-bottom: 1px solid #F1F5F9;
}

.expanded-varieties-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
  max-height: 180px;
  overflow-y: auto;
  padding-right: 4px;
}

.expanded-variety-item {
  font-size: 11.5px;
  font-weight: 700;
  color: #1E293B;
  display: flex;
  align-items: center;
  gap: 8px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  padding: 8px 12px;
  border-radius: 8px;
  line-height: 1.3;
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.expanded-variety-item:hover {
  background-color: #FDF3ED;
  border-color: #E4652E;
  color: #E4652E;
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(228, 101, 46, 0.12);
}

.expanded-variety-item i.fa-arrow-right {
  transition: transform 0.25s ease;
}

.expanded-variety-item:hover i.fa-arrow-right {
  transform: translateX(4px);
  color: #E4652E !important;
}

/* "View All" sits above the varieties, styled as the lead item */
.expanded-variety-item.is-view-all {
  background: #E4652E;
  border-color: #E4652E;
  color: #FFFFFF;
  font-weight: 800;
  margin-bottom: 4px;
}

.expanded-variety-item.is-view-all i {
  color: #FFFFFF !important;
}

.expanded-variety-item.is-view-all:hover {
  background: #C9541F;
  border-color: #C9541F;
  color: #FFFFFF;
}

/* CLOSING "SHOW ALL PRODUCTS" TILE */
.show-all-card {
  background: linear-gradient(160deg, #061427 0%, #0F2544 55%, #16325C 100%);
  border: 1.5px solid #0F2544;
  color: #FFFFFF;
  text-decoration: none;
  padding: 26px 22px;
  justify-content: flex-start;
  align-items: flex-start;
  gap: 0;
  min-height: 280px;
  text-align: left;
}

.show-all-card:hover {
  border-color: #E4652E;
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(6, 20, 39, 0.28);
}

.show-all-icon {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(228, 101, 46, 0.18);
  border: 1px solid rgba(228, 101, 46, 0.45);
  color: #F29163;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-bottom: 16px;
}

.show-all-title {
  display: block;
  font-family: var(--font-heading);
  font-size: 19px;
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 8px;
}

.show-all-sub {
  display: block;
  font-size: 12.5px;
  line-height: 1.55;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 18px;
}

.show-all-cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: auto;
  font-size: 12.5px;
  font-weight: 800;
  color: #F29163;
}

.show-all-card:hover .show-all-cta i {
  transform: translateX(4px);
}

.show-all-cta i {
  transition: transform 0.25s var(--ease-interact);
}

/* ACTION BUTTON */
.card-action-bar {
  margin-top: auto;
}

.btn-explore-cat {
  width: 100%;
  background: #F8FAFC;
  border: 1.5px solid #E2E8F0;
  color: #061427;
  font-size: 12.5px;
  font-weight: 800;
  padding: 10px 14px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: all 0.25s ease;
}

.premium-cat-card:hover .btn-explore-cat,
.btn-explore-cat:hover,
.premium-cat-card.is-expanded .btn-explore-cat {
  background-color: #E4652E;
  border-color: #E4652E;
  color: #FFFFFF;
  box-shadow: 0 6px 20px rgba(228, 101, 46, 0.25);
}

.btn-explore-cat i {
  transition: transform 0.25s ease;
}

.btn-explore-cat:hover i,
.premium-cat-card:hover .btn-explore-cat i {
  transform: translateY(2px);
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

/* The column count itself is bound inline from useColumnCount(), so these
   blocks only compact the cards. */
@media (max-width: 1024px) {
  .master-catalogues-banner {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .category-columns-wrapper {
    gap: 12px;
    margin-bottom: 32px;
  }

  .category-column {
    gap: 12px;
  }

  .premium-cat-card {
    min-height: 0;
    border-radius: 16px;
    border-width: 1px;
  }

  .show-all-card {
    min-height: 0;
    padding: 18px 14px;
  }

  .show-all-icon {
    width: 40px;
    height: 40px;
    font-size: 16px;
    border-radius: 12px;
    margin-bottom: 12px;
  }

  .show-all-title {
    font-size: 15px;
  }

  .show-all-sub {
    font-size: 11.5px;
    margin-bottom: 12px;
  }

  .show-all-cta {
    font-size: 11.5px;
  }

  .card-hero-box {
    height: 118px;
  }

  .card-details-box {
    padding: 12px 10px;
  }

  .cat-title {
    font-size: 13.5px;
    margin-bottom: 10px;
  }

  .expanded-card-panel {
    padding: 10px;
    border-radius: 12px;
    margin-bottom: 12px;
  }

  .expanded-title {
    font-size: 11px;
    margin-bottom: 8px;
  }

  .expanded-varieties-list {
    max-height: 150px;
  }

  .expanded-variety-item {
    font-size: 10.5px;
    padding: 7px 9px;
    gap: 6px;
  }

  .btn-explore-cat {
    font-size: 11px;
    padding: 9px 10px;
    border-radius: 9px;
  }
}
</style>
