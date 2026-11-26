<template>
  <div class="home-page-root">
    <TopBar />
    <Navbar activePage="home" :activeSection="currentSection" @openModal="openInquiryModal(null)" />

    <main id="home">
      <!-- 1. Hero Slider & Stats Banner -->
      <HeroSlider :slides="heroSlides" />
      <StatsBanner :stats="stats" />

      <!-- 1b. Offer Cards, Featured Products, Mega Sale, Best Sellers, New Arrivals & Farmers -->
      <PromoAndFeaturedSection />
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" :prefill="inquiryPrefill" @close="isModalOpen = false" />
    <WhatsAppFloat />
    <PromoPopup @openEnquiry="openInquiryModal(null)" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import HeroSlider from '@/Components/HeroSlider.vue';
import StatsBanner from '@/Components/StatsBanner.vue';
import PromoAndFeaturedSection from '@/Components/PromoAndFeaturedSection.vue';
import PromotionsSection from '@/Components/PromotionsSection.vue';
import FeaturedShowcase from '@/Components/FeaturedShowcase.vue';
import TripleSection from '@/Components/TripleSection.vue';
import CertificatesSection from '@/Components/CertificatesSection.vue';
import BulkInquiryForm from '@/Components/BulkInquiryForm.vue';
import QuickCalloutBanner from '@/Components/QuickCalloutBanner.vue';
import Testimonials from '@/Components/Testimonials.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import { useParallax } from '@/composables/useParallax';

defineProps({
  heroSlides: Array,
  stats: Array,
  panels: {
    type: Object,
    default: () => ({ store: null, b2b: null })
  },
  homePromotions: {
    type: Array,
    default: () => []
  },
  featuredCategories: {
    type: Array,
    default: () => []
  },
  featuredProducts: {
    type: Array,
    default: () => []
  },
  galleryImages: {
    type: Array,
    default: () => []
  },
  galleryVideos: {
    type: Array,
    default: () => []
  },
  testimonials: {
    type: Array,
    default: () => []
  },
  clientLogos: {
    type: Array,
    default: () => []
  },
  brandLogos: {
    type: Array,
    default: () => []
  },
  certificates: {
    type: Array,
    default: () => []
  },
  meta: Object
});

const isModalOpen = ref(false);
const inquiryPrefill = ref(null);
const currentSection = ref('home');

function openInquiryModal(item) {
  if (!item) {
    inquiryPrefill.value = { inquiryType: 'retail', productName: '', message: '' };
  } else {
    const lines = [`Product: ${item.name}`];
    if (item.brand) lines.push(`Brand: ${item.brand}`);
    if (item.packSize) lines.push(`Pack Size: ${item.packSize}`);

    inquiryPrefill.value = {
      inquiryType: item.segment === 'b2b' ? 'b2b' : 'retail',
      productName: item.name,
      message: `${lines.join('\n')}\n\nPlease share pricing and availability.`,
    };
  }
  isModalOpen.value = true;
}

// Cleared on unmount so a visitor who navigates away inside 90 seconds is not
// ambushed by the popup on whatever page they land on.
let popupTimer = null;

// Drives the hero backdrop and the big ghost section words (`data-parallax`).
useParallax();

onMounted(() => {
  // 1. Scroll Reveal Intersection Observer setup
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          obs.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.08,
      rootMargin: '0px 0px -20px 0px'
    });

    revealElements.forEach(el => observer.observe(el));

    // 2. Section ScrollSpy Observer for Navbar active highlighting
    const sectionIds = ['home', 'categories', 'b2b', 'promotions', 'contact'];
    const sections = sectionIds.map(id => document.getElementById(id)).filter(Boolean);

    const scrollSpyObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          currentSection.value = entry.target.id;
        }
      });
    }, {
      threshold: 0.25
    });

    sections.forEach(sec => scrollSpyObserver.observe(sec));
  } else {
    revealElements.forEach(el => el.classList.add('active'));
  }

  // 3. Contact form invites itself in once the visitor has had time to browse
  //    — 90 seconds in, and only once per session.
  if (!sessionStorage.getItem('hasSeenPopup')) {
    popupTimer = setTimeout(() => {
      openInquiryModal(null);
      sessionStorage.setItem('hasSeenPopup', 'true');
    }, 90000);
  }
});

onUnmounted(() => {
  if (popupTimer) clearTimeout(popupTimer);
});
</script>
