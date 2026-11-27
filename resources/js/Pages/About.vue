<template>
  <div class="fl-about-page">
    <TopBar />
    <Navbar activePage="about" @openModal="isModalOpen = true" />

    <main class="fl-about-main">
      <div class="container">
        <!-- 1. Centered Photographic Banner with Dark Gradient Overlay -->
        <section class="fl-about-hero">
          <div class="fl-about-hero-overlay"></div>
          <div class="fl-about-hero-copy">
            <h1 class="fl-about-hero-title">About Us</h1>
            <p class="fl-about-hero-sub">
              Fresh groceries, daily essentials and farm-fresh produce, straight from local farms to your home.
            </p>
          </div>
        </section>

        <!-- 2. Clean ~200-Word About Us Text -->
        <section class="fl-about-prose">
          <h2>Who We Are</h2>
          <p>
            VIZORA FOODS is a neighborhood grocery and fresh produce platform built to bridge the gap between local farms, morning mandis, and your family’s kitchen. Most store-bought fruits, vegetables, and daily staples pass through layers of middlemen and sit in cold storage for days before reaching the shelf. We started VIZORA with a simple goal: to bring honest, naturally grown, and freshly harvested groceries directly from trusted regional farmers to your doorstep within hours.
          </p>
          <p>
            Every morning, our sourcing team works closely with verified farmers and local mandis to handpick seasonal produce, farm-fresh dairy, unpolished dals, cold-pressed oils, and pure spices. Each batch is carefully sorted for freshness, weighed accurately, and hygienically packed so your household gets consistent quality at fair everyday prices.
          </p>
          <p>
            By choosing VIZORA FOODS, you are not only bringing cleaner, fresher food to your dining table — you are also supporting local farming communities with fair, transparent pricing and dependable daily demand. Whether it is your morning milk and vegetables or your monthly pantry restock, we deliver real freshness right to your home.
          </p>
        </section>

        <!-- 3. Visual Feature 1 (Image Left + Story Right) -->
        <section class="fl-about-feature">
          <div class="fl-feature-img">
            <img src="/assets/organic-banner-bg.jpg" alt="100% Organic Farm Harvest" loading="lazy">
          </div>
          <div class="fl-feature-copy">
            <h3>Direct from Local Farms &amp; Morning Mandis</h3>
            <p>
              We partner directly with experienced farmers across Bareilly, Pilibhit, Rampur, Shahjahanpur, and nearby agricultural belts. Harvests are picked at peak ripeness early in the morning and transported straight to our local sorting hub — skipping long warehouse chains.
            </p>
            <p>
              This farm-first supply chain keeps leafy greens crisp, fruits naturally sweet, and ensures our partner growers earn a fair price for every harvest.
            </p>
          </div>
        </section>

        <!-- 4. Visual Feature 2 (Text Only) -->
        <section class="fl-about-feature is-text-only">
          <div class="fl-feature-copy">
            <h3>Quality Checked, Hygienically Packed &amp; Delivered Fast</h3>
            <p>
              From fresh A2 milk, paneer, and farm eggs to stone-ground atta, unpolished pulses, and aromatic Indian spices, every product undergoes strict quality and weight verification before leaving our hub.
            </p>
            <p>
              With express 2-hour and scheduled morning or evening delivery slots, VIZORA makes eating fresh, clean, and local effortless for busy households every single day.
            </p>
          </div>
        </section>
      </div>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <WhatsAppFloat />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';

const isModalOpen = ref(false);
</script>

<style scoped>
.fl-about-page {
  background-color: #FFFFFF;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-about-main {
  padding: 20px 0 68px;
}

/* 1. Hero Banner matching Privacy Policy */
.fl-about-hero {
  position: relative;
  height: 250px;
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 0 32px;
  background-color: #102A18;
  background-image: url('/assets/mega-sale-banner-bg.jpg');
  background-size: cover;
  background-position: center;
  box-shadow: 0 12px 28px rgba(15, 46, 26, 0.14);
}

.fl-about-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(10, 24, 15, 0.68) 0%, rgba(10, 24, 15, 0.82) 100%);
  pointer-events: none;
}

.fl-about-hero-copy {
  position: relative;
  z-index: 2;
  max-width: 640px;
}

.fl-about-hero-title {
  font-size: 50px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 10px 0;
  letter-spacing: -0.025em;
}

.fl-about-hero-sub {
  font-size: 16.5px;
  color: rgba(255, 255, 255, 0.88);
  margin: 0;
  line-height: 1.5;
}

/* 2. Clean ~200-Word Editorial Prose */
.fl-about-prose {
  max-width: 920px;
  margin: 44px auto 12px;
  padding: 0 8px;
}

.fl-about-prose h2 {
  font-size: 28px;
  font-weight: 800;
  color: #124E26;
  margin: 0 0 18px 0;
  letter-spacing: -0.02em;
}

.fl-about-prose p {
  font-size: 16.5px;
  line-height: 1.8;
  color: #374151;
  margin: 0 0 18px 0;
  font-weight: 400;
}

.fl-about-prose p:last-child {
  margin-bottom: 0;
}

/* 3 & 4. Visual Feature Rows */
.fl-about-feature {
  max-width: 960px;
  margin: 48px auto 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 38px;
  align-items: center;
}

.fl-about-feature.is-text-only {
  grid-template-columns: 1fr;
  max-width: 920px;
}

.fl-feature-img {
  border-radius: 18px;
  overflow: hidden;
  height: 290px;
  box-shadow: 0 10px 26px rgba(20, 83, 45, 0.12);
}

.fl-feature-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.fl-feature-copy h3 {
  font-size: 24px;
  font-weight: 800;
  color: #124E26;
  line-height: 1.25;
  margin: 0 0 12px 0;
  letter-spacing: -0.02em;
}

.fl-feature-copy p {
  font-size: 15px;
  line-height: 1.7;
  color: #4A5548;
  margin: 0 0 12px 0;
}

.fl-feature-copy p:last-child {
  margin-bottom: 0;
}

@media (max-width: 900px) {
  .fl-about-feature {
    grid-template-columns: 1fr;
    gap: 22px;
  }
  .fl-about-feature.is-reverse .fl-feature-img {
    order: -1;
  }
  .fl-about-hero {
    padding: 0 28px;
    height: 220px;
  }
  .fl-about-hero-overlay {
    background: linear-gradient(112deg, rgba(20, 77, 35, 0.94) 0%, rgba(20, 77, 35, 0.55) 100%);
  }
  .fl-about-hero-title {
    font-size: 38px;
  }
  .fl-about-split {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  .fl-about-metrics {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
