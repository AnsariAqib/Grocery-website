<template>
  <div class="about-page-root">
    <TopBar />
    <Navbar activePage="about" @openModal="isModalOpen = true" />

    <main>
      <!-- Page Hero Banner -->
      <section class="about-hero-section">
        <div class="container">
          <div class="about-hero-card reveal">
            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=80" alt="Jay Tulsi Stores & HQ" class="about-hero-bg">
            <div class="about-hero-overlay"></div>
            <div class="about-hero-content">
              <div class="breadcrumb-trail">
                <Link href="/"><i class="fa-solid fa-house"></i> Home</Link>
                <span>/</span>
                <span class="active">About Us</span>
              </div>
              <h1 class="about-hero-title">
                About Us <br>
                <span class="hero-title-gradient shiny-text">Building Trust Since 1984</span>
              </h1>
              <p class="about-hero-sub">
                From a single neighborhood grocery store to one of Gujarat's trusted retail and B2B supply organizations.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Company Story Section -->
      <section class="section-padding" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">LEGACY</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="story-grid">
            <div class="story-image-wrapper reveal-left">
              <div class="story-img-main">
                <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=800&q=80" alt="Supermarket Store">
                <div class="experience-badge">
                  <span class="exp-years">40+</span>
                  <span class="exp-text">Years of Retail<br>& Supply Trust</span>
                </div>
              </div>
              <div class="story-img-secondary">
                <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=600&q=80" alt="B2B Warehouse Operations">
              </div>
            </div>

            <div class="story-content reveal-right">
              <span class="section-badge"><i class="fa-solid fa-building"></i> Our Legacy & Story</span>
              <h2 class="section-title" style="text-align:left;">Building Trust Since 1984</h2>
              <p class="story-desc">
                From a single neighborhood grocery store to one of Gujarat's trusted retail and B2B supply organizations, our journey has been driven by one simple philosophy—quality products, fair pricing, and lasting relationships.
              </p>
              <p class="story-desc">
                Under the <strong>Vinayak</strong> and <strong>Kalptaru</strong> brands, <strong>Jay Tulsi Agency</strong> today serves <strong>50,000+ retail customers</strong> and <strong>1,000+ businesses</strong> across Gujarat and beyond. We provide a complete procurement solution for households, industries, institutions, and corporate clients through an extensive portfolio of groceries, FMCG products, housekeeping supplies, office essentials, food service products, and industrial consumables.
              </p>
              <p class="story-desc">
                With direct partnerships with leading national brands and an efficient delivery network, we help customers source everything they need through one reliable partner.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Why Choose Us -->
      <section class="section-padding bg-light-surface" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">WHY US</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-award"></i> Our Strengths</span>
            <h2 class="section-title">Why Choose Jay Tulsi Agency</h2>
            <p class="section-subtitle">Four decades of dependable supply for households, industries, and institutions.</p>
          </div>

          <div class="reasons-grid stagger">
            <div v-for="reason in reasons" :key="reason.title" class="reason-card reveal">
              <div class="reason-icon-box"><i class="fa-solid" :class="reason.icon"></i></div>
              <h3 class="reason-title">{{ reason.title }}</h3>
              <p class="reason-desc">{{ reason.desc }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Mission, Vision & Core Values Section -->
      <section class="section-padding" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">PRINCIPLES</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-compass"></i> Guided Principles</span>
            <h2 class="section-title">Our Mission, Vision & Core Values</h2>
            <p class="section-subtitle">The foundation that drives our daily commitment to customers and commercial partners.</p>
          </div>

          <div class="values-grid">
            <div class="value-card reveal-left">
              <div class="value-icon-box"><i class="fa-solid fa-bullseye"></i></div>
              <h3>Our Mission</h3>
              <p>To make quality products easily accessible through dependable service, competitive pricing, and long-term partnerships that create value for both households and businesses.</p>
            </div>
            <div class="value-card reveal">
              <div class="value-icon-box"><i class="fa-solid fa-eye"></i></div>
              <h3>Our Vision</h3>
              <p>To become India's most trusted integrated retail and B2B procurement company by combining modern technology, strong manufacturer partnerships, and customer-first service.</p>
            </div>
            <div class="value-card reveal-right">
              <div class="value-icon-box"><i class="fa-solid fa-shield-heart"></i></div>
              <h3>Our Core Values</h3>
              <ul class="core-values-list">
                <li v-for="value in coreValues" :key="value">
                  <i class="fa-solid fa-circle-check"></i> {{ value }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Our Journey Timeline -->
      <section class="section-padding" style="position:relative; overflow:hidden; background-color: #faf9f6;">
        <div class="watermark-backdrop" data-parallax="26">JOURNEY</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-route"></i> Milestones</span>
            <h2 class="section-title">Our Journey</h2>
            <p class="section-subtitle">Four decades of steady growth — from one grocery store to a PAN India supply network.</p>
          </div>

          <div class="journey-timeline" ref="journeyRef">
            <!-- Rail fill tracks how far the visitor has scrolled through the
                 timeline, so the line draws itself as the years go by. -->
            <span class="journey-rail-fill" :style="{ height: journeyProgress + '%' }"></span>
            <div
              v-for="(item, idx) in milestones"
              :key="item.year"
              class="journey-item"
              :class="[
                idx % 2 === 0 ? 'is-left' : 'is-right',
                { 'is-final': item.isFinal, 'is-reached': reachedSteps > idx },
              ]"
            >
              <div class="journey-node">
                <i v-if="item.isFinal" class="fa-solid fa-rocket"></i>
              </div>
              <div class="journey-card">
                <div class="journey-year">{{ item.year }}</div>
                <h3 class="journey-title">{{ item.title }}</h3>
                <p class="journey-desc">{{ item.desc }}</p>
              </div>
            </div>
          </div>

          <!-- Journey Video -->
          <div v-if="journeyVideo" class="journey-video-block reveal">
            <div class="journey-video-text">
              <span class="section-badge"><i class="fa-solid fa-circle-play"></i> Watch Our Story</span>
              <h3 class="journey-video-title">{{ journeyVideo.title }}</h3>
              <p class="journey-video-desc">{{ journeyVideo.desc }}</p>
              <a :href="journeyVideo.watchUrl" target="_blank" rel="noopener" class="btn-watch-youtube">
                <i class="fa-brands fa-youtube"></i> Watch on YouTube
              </a>
            </div>
            <div class="journey-video-frame">
              <iframe
                :src="journeyVideo.embedUrl"
                :title="journeyVideo.title"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </section>

      <!-- Leadership Team Section -->
      <section class="section-padding bg-light-surface" style="position:relative; overflow:hidden;">
        <div class="watermark-backdrop" data-parallax="26">LEADERSHIP</div>
        <div class="container" style="position:relative; z-index:2;">
          <div class="section-header center">
            <span class="section-badge"><i class="fa-solid fa-users-gear"></i> Leadership</span>
            <h2 class="section-title">Our Leadership Team</h2>
            <p class="section-subtitle">Experienced leaders driving service quality and operational excellence across every division.</p>
          </div>

          <div class="leaders-grid stagger">
            <div v-for="leader in leadership" :key="leader.id ?? leader.name" class="leader-card reveal">
              <img
                v-if="leader.photo"
                :src="leader.photo"
                :alt="leader.name"
                class="leader-photo"
                loading="lazy"
                decoding="async"
              >
              <div v-else class="leader-initials">{{ leader.initials || initialsOf(leader.name) }}</div>

              <h3 class="leader-name">{{ leader.name }}</h3>
              <span v-if="leader.company" class="leader-company">{{ leader.company }}</span>
              <p v-if="leader.desc" class="leader-bio">{{ leader.desc }}</p>
            </div>
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
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';
import PromoPopup from '@/Components/PromoPopup.vue';
import { useParallax } from '@/composables/useParallax';

defineProps({
  reasons: {
    type: Array,
    default: () => []
  },
  coreValues: {
    type: Array,
    default: () => []
  },
  milestones: {
    type: Array,
    default: () => []
  },
  journeyVideo: Object,
  leadership: {
    type: Array,
    default: () => []
  },
  meta: Object
});

const isModalOpen = ref(false);

// Drifts the big ghost section words and the hero backdrop.
useParallax();

// ---- Scroll-driven journey -------------------------------------------------
// One rAF-throttled scroll handler measures how far the timeline has travelled
// through the viewport: the rail fills to `journeyProgress`, and each card
// switches on once the line has reached its node.
const journeyRef = ref(null);
const journeyProgress = ref(0);
const reachedSteps = ref(0);
let journeyTicking = false;

function measureJourney() {
  journeyTicking = false;

  const el = journeyRef.value;
  if (!el) return;

  const rect = el.getBoundingClientRect();
  const anchor = window.innerHeight * 0.62;          // where the rail head sits
  const travelled = anchor - rect.top;
  const progress = Math.max(0, Math.min(1, travelled / Math.max(1, rect.height)));

  journeyProgress.value = Number((progress * 100).toFixed(2));

  const nodes = el.querySelectorAll('.journey-node');
  let reached = 0;
  nodes.forEach(node => {
    if (node.getBoundingClientRect().top <= anchor) reached += 1;
  });
  reachedSteps.value = reached;
}

function onJourneyScroll() {
  if (journeyTicking) return;
  journeyTicking = true;
  requestAnimationFrame(measureJourney);
}

// Team members have no photographs on file, so each card carries their initials
// instead of a stock portrait that would misrepresent a real person.
function initialsOf(name) {
  return (name || '')
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map(part => part[0].toUpperCase())
    .join('');
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
    }, {
      threshold: 0.08,
      rootMargin: '0px 0px -20px 0px'
    });

    revealElements.forEach(el => observer.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('active'));
  }

  measureJourney();
  window.addEventListener('scroll', onJourneyScroll, { passive: true });
  window.addEventListener('resize', onJourneyScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', onJourneyScroll);
  window.removeEventListener('resize', onJourneyScroll);
});
</script>

<style scoped>
/* ---------- WHY CHOOSE US ---------- */
.reasons-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.reason-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  padding: 28px 24px;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.reason-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
  border-color: rgba(228, 101, 46, 0.35);
}

.reason-icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background-color: #FDF3ED;
  color: #E4652E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  margin-bottom: 16px;
}

.reason-title {
  font-family: var(--font-heading);
  font-size: 16.5px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.reason-desc {
  font-size: 13px;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

/* ---------- CORE VALUES LIST ---------- */
.core-values-list {
  list-style: none;
  padding: 0;
  margin: 0;
  text-align: left;
}

.core-values-list li {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 13px;
  color: #475569;
  line-height: 1.5;
  margin-bottom: 8px;
}

.core-values-list li i {
  color: #E4652E;
  font-size: 12px;
  margin-top: 3px;
  flex-shrink: 0;
}

/* ---------- JOURNEY TIMELINE ---------- */
.journey-timeline {
  position: relative;
  max-width: 1000px;
  margin: 0 auto;
  padding: 8px 0;
}

/* Centre rail on desktop, left rail on mobile (see media queries). The track
   is drawn flat and the fill grows over it as the page scrolls. */
.journey-timeline::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 3px;
  transform: translateX(-50%);
  background: #E9EDF2;
  border-radius: 3px;
}

.journey-rail-fill {
  position: absolute;
  top: 0;
  left: 50%;
  width: 3px;
  transform: translateX(-50%);
  background: linear-gradient(180deg, #061427 0%, #E4652E 100%);
  border-radius: 3px;
  z-index: 1;
  transition: height 0.15s linear;
}

.journey-item {
  position: relative;
  width: 50%;
  padding: 0 44px 32px 0;
  box-sizing: border-box;
  opacity: 0;
  transform: translateY(26px);
  transition: opacity 0.6s var(--ease-entrance), transform 0.6s var(--ease-entrance);
}

/* Switched on by the scroll handler once the rail reaches this step */
.journey-item.is-reached {
  opacity: 1;
  transform: translateY(0);
}

.journey-item.is-left {
  transform: translate(-18px, 26px);
}

.journey-item.is-right {
  transform: translate(18px, 26px);
}

.journey-item.is-left.is-reached,
.journey-item.is-right.is-reached {
  transform: translate(0, 0);
}

.journey-item.is-right {
  margin-left: 50%;
  padding: 0 0 32px 44px;
}

.journey-node {
  position: absolute;
  top: 22px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 3px solid #CBD5E1;
  transition: border-color 0.4s var(--ease-entrance), box-shadow 0.4s var(--ease-entrance);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 9px;
  color: #FFFFFF;
  z-index: 2;
}

.journey-item.is-left .journey-node {
  right: -10px;
}

.journey-item.is-right .journey-node {
  left: -10px;
}

.journey-item.is-reached .journey-node {
  border-color: #E4652E;
  box-shadow: 0 0 0 5px rgba(228, 101, 46, 0.14);
}

.journey-item.is-final.is-reached .journey-node {
  background: #E4652E;
  border-color: #FFFFFF;
  box-shadow: 0 0 0 3px #E4652E;
}

.journey-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 18px;
  padding: 22px 24px;
  box-shadow: 0 6px 20px rgba(6, 20, 39, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.journey-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 34px rgba(6, 20, 39, 0.1);
  border-color: rgba(228, 101, 46, 0.35);
}

.journey-item.is-final .journey-card {
  background: linear-gradient(135deg, #E4652E 0%, #F29163 100%);
  border-color: #E4652E;
}

.journey-item.is-final .journey-year,
.journey-item.is-final .journey-title,
.journey-item.is-final .journey-desc {
  color: #FFFFFF;
}

.journey-year {
  font-family: var(--font-heading);
  font-size: 22px;
  font-weight: 800;
  color: #E4652E;
  line-height: 1.1;
  margin-bottom: 4px;
}

.journey-title {
  font-family: var(--font-heading);
  font-size: 16px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 6px 0;
}

.journey-desc {
  font-size: 13px;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

/* ---------- JOURNEY VIDEO ---------- */
.journey-video-block {
  margin-top: 48px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: var(--radius-banner);
  padding: 28px;
  display: grid;
  grid-template-columns: 1fr 1.15fr;
  gap: 32px;
  align-items: center;
  box-shadow: 0 8px 28px rgba(6, 20, 39, 0.06);
}

.journey-video-title {
  font-family: var(--font-heading);
  font-size: 24px;
  font-weight: 800;
  color: #061427;
  margin: 10px 0 8px 0;
}

.journey-video-desc {
  font-size: 14px;
  color: #475569;
  line-height: 1.6;
  margin: 0 0 18px 0;
}

.btn-watch-youtube {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background-color: #E4652E;
  color: #FFFFFF;
  font-size: 13.5px;
  font-weight: 800;
  padding: 11px 20px;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(228, 101, 46, 0.25);
  transition: all 0.25s ease;
}

.btn-watch-youtube:hover {
  background-color: #C9541F;
  transform: translateY(-2px);
}

.journey-video-frame {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  border-radius: 18px;
  overflow: hidden;
  background: #061427;
  box-shadow: 0 12px 30px rgba(6, 20, 39, 0.18);
}

.journey-video-frame iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

/* ---------- LEADERSHIP ---------- */
.leaders-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 22px;
}

.leader-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  padding: 26px 22px;
  text-align: center;
  box-shadow: 0 4px 16px rgba(6, 20, 39, 0.04);
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.leader-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(6, 20, 39, 0.1);
  border-color: rgba(228, 101, 46, 0.35);
}

.leader-photo,
.leader-initials {
  width: 62px;
  height: 62px;
  margin: 0 auto 14px auto;
  border-radius: 50%;
  background: linear-gradient(135deg, #061427 0%, #0F2544 100%);
  color: #FFFFFF;
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: 800;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid #FDF3ED;
}

.leader-card:hover .leader-initials {
  background: linear-gradient(135deg, #E4652E 0%, #F29163 100%);
}

/* Uploaded portrait sits in the same circle the initials use */
.leader-photo {
  border-radius: 50%;
  object-fit: cover;
  display: block;
  border: 3px solid #FDF3ED;
  background: #F1F5F9;
  transition: border-color 0.3s ease;
}

.leader-card:hover .leader-photo {
  border-color: #F3D2BF;
}

.leader-name {
  font-family: var(--font-heading);
  font-size: 17px;
  font-weight: 800;
  color: #061427;
  margin: 0 0 4px 0;
}

.leader-company {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #E4652E;
  margin-bottom: 10px;
}

.leader-bio {
  font-size: 12.5px;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}

/* ---------- RESPONSIVE ---------- */
@media (max-width: 1024px) {
  .reasons-grid,
  .leaders-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .journey-video-block {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  /* Timeline moves to a single left-rail column */
  .journey-timeline::before,
  .journey-rail-fill {
    left: 9px;
    transform: none;
  }

  .journey-item.is-left,
  .journey-item.is-right {
    transform: translateY(20px);
  }

  .journey-item.is-left.is-reached,
  .journey-item.is-right.is-reached {
    transform: translateY(0);
  }

  .journey-item,
  .journey-item.is-right {
    width: 100%;
    margin-left: 0;
    padding: 0 0 22px 34px;
  }

  .journey-item.is-left .journey-node,
  .journey-item.is-right .journey-node {
    left: 0;
    right: auto;
    top: 18px;
    width: 18px;
    height: 18px;
  }

  .journey-card {
    padding: 16px 16px;
    border-radius: 16px;
  }

  .journey-year {
    font-size: 18px;
  }

  .journey-title {
    font-size: 14.5px;
  }

  .journey-desc {
    font-size: 12.5px;
  }

  /* Two compact cards per row on phones */
  .reasons-grid,
  .leaders-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .reason-card {
    padding: 18px 14px;
    border-radius: 16px;
  }

  .reason-icon-box {
    width: 42px;
    height: 42px;
    font-size: 18px;
    border-radius: 12px;
    margin-bottom: 12px;
  }

  .reason-title {
    font-size: 14px;
  }

  .reason-desc {
    font-size: 12px;
  }

  .leader-card {
    padding: 18px 12px;
    border-radius: 16px;
  }

  .leader-photo,
  .leader-initials {
    width: 50px;
    height: 50px;
    font-size: 16px;
    margin-bottom: 10px;
  }

  .leader-name {
    font-size: 14.5px;
  }

  .leader-company {
    font-size: 11px;
  }

  .leader-bio {
    font-size: 11.5px;
  }

  .journey-video-block {
    margin-top: 32px;
    padding: 16px;
    border-radius: 18px;
    gap: 18px;
  }

  .journey-video-title {
    font-size: 19px;
  }

  .journey-video-desc {
    font-size: 13px;
  }
}
</style>
