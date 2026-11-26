<template>
  <section class="section-padding section-flush-top">
    <div class="container">
      <div class="stacked-rows-wrapper">
        
        <!-- ROW 1 & 2: SIDE BY SIDE -->
        <div class="side-by-side-layout stagger" style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; align-items: stretch;">
          <!-- CORPORATE CLIENT NETWORK MARQUEE -->
          <div class="info-block-card reveal" style="flex: 2 1 550px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
            <div class="block-header stagger" style="flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
              <h3 class="block-title reveal"><i class="fa-solid fa-handshake-simple" style="color:#E4652E; margin-right:8px;"></i> Trusted Corporate Clients</h3>
              <span class="live-pill reveal"><span class="pulse-dot"></span> 1000+ Business Accounts</span>
            </div>

            <div class="marquee-wrapper" style="flex: 1; display: flex; align-items: center;">
              <div class="marquee-track track-left">
                <!-- Rendered 4 times: Ensures the marquee track never leaves blank space on any resolution -->
                <template v-for="pass in 4" :key="pass">
                  <div v-for="logo in clientLogos" :key="`${pass}-${logo.id}`" class="logo-box">
                    <img :src="logo.image" :alt="logo.name" class="client-logo-img">
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- INDUSTRY PROCUREMENT CATEGORIES (5 ITEMS SLIDER) -->
          <div 
            class="info-block-card reveal" 
            style="flex: 1 1 340px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;"
            @mouseenter="stopAutoSlide"
            @mouseleave="startAutoSlide"
          >
            <div class="block-header stagger" style="flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
              <h3 class="block-title reveal" style="font-size: 16.5px;"><i class="fa-solid fa-industry" style="color:#E4652E; margin-right:8px;"></i> Key Industries We Serve</h3>
              <span class="live-pill reveal" style="margin-left: auto;"><i class="fa-solid fa-check-double" style="color:#10B981;"></i> Specialized B2B</span>
            </div>

            <!-- Single Row Grid of 5 Items with smooth slide transition -->
            <transition name="fade-slide" mode="out-in">
              <div :key="currentIndustryPage" class="full-industries-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-bottom: 6px;">
                <div 
                  v-for="(item, idx) in industryPages[currentIndustryPage]" 
                  :key="idx" 
                  class="industry-item"
                >
                  <div class="industry-icon"><i class="fa-solid" :class="item.icon"></i></div>
                  <span class="industry-name">{{ item.name }}</span>
                </div>
              </div>
            </transition>

            <!-- Bottom Page Indicator Bar -->
            <div class="industry-controls-bar">
              <div class="dots-indicator">
                <span 
                  v-for="(page, idx) in industryPages" 
                  :key="idx" 
                  class="dot-pill" 
                  :class="{ active: currentIndustryPage === idx }"
                  @click="currentIndustryPage = idx"
                ></span>
              </div>
            </div>
          </div>
        </div>

        <!-- ROW 3: AUTHORISED BRAND PARTNERS MARQUEE -->
        <div class="info-block-card full-width-card reveal" id="dealers">
          <div class="block-header stagger">
            <h3 class="block-title reveal"><i class="fa-solid fa-certificate" style="color:#E4652E; margin-right:8px;"></i> Authorised Brand Agencies</h3>
            <span class="live-pill reveal"><i class="fa-solid fa-shield-halved" style="color:#3B82F6;"></i> 100% Genuine Partner</span>
          </div>

          <div class="marquee-wrapper">
            <div class="marquee-track track-right">
              <template v-for="pass in 4" :key="pass">
                <div v-for="logo in brandLogos" :key="`${pass}-${logo.id}`" class="logo-box">
                  <img :src="logo.image" :alt="logo.name" class="client-logo-img">
                </div>
              </template>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
  clientLogos: {
    type: Array,
    default: () => []
  },
  brandLogos: {
    type: Array,
    default: () => []
  }
});

const currentIndustryPage = ref(0);
let autoSlideTimer = null;

const industryPages = [
  [
    { icon: 'fa-shirt', name: 'Textiles & Garments' },
    { icon: 'fa-flask', name: 'Chemical & Petrochem' },
    { icon: 'fa-building-user', name: 'Construction & Infra' },
    { icon: 'fa-gem', name: 'Diamond & Jewellery' },
    { icon: 'fa-graduation-cap', name: 'Education & Academics' }
  ],
  [
    { icon: 'fa-hospital', name: 'Healthcare & Pharma' },
    { icon: 'fa-hotel', name: 'Hospitality & Food' },
    { icon: 'fa-laptop-code', name: 'IT & Tech Parks' },
    { icon: 'fa-truck-ramp-box', name: 'Logistics & Ports' },
    { icon: 'fa-building-circle-check', name: 'Corporate Offices' }
  ]
];

function nextPage() {
  currentIndustryPage.value = (currentIndustryPage.value + 1) % industryPages.length;
}

function prevPage() {
  currentIndustryPage.value = (currentIndustryPage.value - 1 + industryPages.length) % industryPages.length;
}

function startAutoSlide() {
  stopAutoSlide();
  autoSlideTimer = setInterval(() => {
    nextPage();
  }, 3000);
}

function stopAutoSlide() {
  if (autoSlideTimer) {
    clearInterval(autoSlideTimer);
    autoSlideTimer = null;
  }
}

onMounted(() => {
  startAutoSlide();
});

onUnmounted(() => {
  stopAutoSlide();
});
</script>

<style scoped>
.industry-controls-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 4px;
  padding-top: 4px;
  border-top: 1px dashed #F1F5F9;
}

.slider-arrow-btn {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #F1F5F9;
  border: 1px solid #E2E8F0;
  color: #061427;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.slider-arrow-btn:hover {
  background-color: #E4652E;
  border-color: #E4652E;
  color: #FFFFFF;
}

.dots-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
}

.dot-pill {
  width: 7px;
  height: 7px;
  border-radius: 4px;
  background-color: #CBD5E1;
  cursor: pointer;
  transition: all 0.25s ease;
}

.dot-pill.active {
  width: 18px;
  background-color: #E4652E;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.35s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}
</style>
