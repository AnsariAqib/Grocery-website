<template>
  <div class="fl-legal-page">
    <TopBar />
    <Navbar activePage="about" @openModal="isModalOpen = true" />

    <main class="fl-legal-main">
      <div class="container">
        <!-- 1. Centered Photographic Banner with Dark Gradient Overlay -->
        <section class="fl-legal-hero">
          <div class="fl-legal-hero-overlay"></div>
          <div class="fl-legal-hero-copy">
            <h1 class="fl-legal-hero-title">Privacy Policy</h1>
            <p class="fl-legal-hero-sub">
              Your trust matters to us. Here is how VIZORA FOODS keeps your personal information safe.
            </p>
          </div>
        </section>

        <!-- 2. Larger, Clean Text-Only Privacy Policy (3 Headings) -->
        <article class="fl-legal-prose">
          <p class="fl-legal-updated">Last updated: November 2026</p>

          <p>
            At <strong>VIZORA FOODS</strong>, we respect your privacy and are committed to protecting the personal details you share while browsing our store, placing grocery orders, or contacting support.
          </p>

          <h2>1. Information We Collect &amp; How We Use It</h2>
          <p>
            When you place an order, request a callback, or submit an inquiry on VIZORA, we collect only the essential information required to deliver your groceries smoothly — including your name, mobile number, delivery address, city, pincode, and email address. We also use essential session cookies and browser storage to keep your shopping cart and selected delivery location intact.
          </p>
          <p>
            We use this information strictly to pack and deliver your orders on time, send order and delivery slot updates, and assist you with customer support. We never sell, rent, or trade your personal information with third-party marketers.
          </p>

          <h2>2. Payments, Security &amp; Data Sharing</h2>
          <p>
            Whether you pay via Cash on Delivery (COD), UPI, or card, all payment and order details are handled over secure channels with strict safeguards against unauthorized access. We never store your card PINs or banking passwords. Your contact number and delivery address are shared solely with our verified packing and delivery partners to complete your doorstep delivery, or where required by law.
          </p>

          <h2>3. Your Rights &amp; Contact Us</h2>
          <p>
            You have full control over your personal data and may request to view, update, or remove your contact details from our records at any time. If you have any questions regarding this Privacy Policy, please reach out to our support team through the Contact page.
          </p>
        </article>
      </div>
    </main>

    <Footer />
    <ContactModal :isOpen="isModalOpen" @close="isModalOpen = false" />
    <WhatsAppFloat />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import TopBar from '@/Components/TopBar.vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import ContactModal from '@/Components/ContactModal.vue';
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue';

const isModalOpen = ref(false);
</script>

<style scoped>
.fl-legal-page {
  background-color: #FFFFFF;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', var(--font-main), sans-serif;
}

.fl-legal-main {
  padding: 20px 0 68px;
}

/* Top Photographic Banner with Dark Gradient Overlay */
.fl-legal-hero {
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
  background-image: url('/assets/organic-banner-bg.jpg');
  background-size: cover;
  background-position: center;
  box-shadow: 0 12px 28px rgba(15, 46, 26, 0.14);
}

.fl-legal-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(10, 24, 15, 0.68) 0%, rgba(10, 24, 15, 0.82) 100%);
  pointer-events: none;
}

.fl-legal-hero-copy {
  position: relative;
  z-index: 2;
  max-width: 640px;
}

.fl-legal-hero-title {
  font-size: 50px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 10px 0;
  letter-spacing: -0.025em;
}

.fl-legal-hero-sub {
  font-size: 16.5px;
  color: rgba(255, 255, 255, 0.88);
  margin: 0;
  line-height: 1.5;
}

/* Larger Clean Text-Only Prose */
.fl-legal-prose {
  max-width: 920px;
  margin: 44px auto 12px;
  padding: 0 8px;
}

.fl-legal-updated {
  font-size: 14px;
  font-weight: 600;
  color: #3F9C1E;
  margin: 0 0 18px 0;
}

.fl-legal-prose h2 {
  font-size: 24px;
  font-weight: 800;
  color: #124E26;
  margin: 32px 0 12px 0;
  letter-spacing: -0.015em;
}

.fl-legal-prose p {
  font-size: 17.5px;
  line-height: 1.85;
  color: #374151;
  margin: 0 0 18px 0;
}

@media (max-width: 900px) {
  .fl-legal-hero {
    padding: 0 28px;
    height: 190px;
  }
  .fl-legal-hero-title {
    font-size: 34px;
  }
}
</style>

